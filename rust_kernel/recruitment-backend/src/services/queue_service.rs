use crate::error::Result;
use rust_decimal::prelude::FromPrimitive;
use rust_decimal::Decimal;
use serde_json::Value as JsonValue;
use sqlx::{PgPool, Row};
use uuid::Uuid;

#[derive(Clone)]
pub struct AiQueueService {
    pub pool: PgPool,
}

impl AiQueueService {
    pub fn new(pool: PgPool) -> Self {
        Self { pool }
    }

    pub async fn enqueue(
        &self,
        payload: JsonValue,
        persist: bool,
        title: Option<String>,
        description: Option<String>,
        duration_minutes: Option<i32>,
        passing_score: Option<f64>,
    ) -> Result<Uuid> {
        let passing_dec: Option<Decimal> = passing_score.and_then(Decimal::from_f64);
        let row = sqlx::query(
            r#"
            INSERT INTO ai_jobs (payload, persist, title, description, duration_minutes, passing_score)
            VALUES ($1, $2, $3, $4, $5, $6)
            RETURNING id
            "#,
        )
        .bind(payload)
        .bind(persist)
        .bind(title)
        .bind(description)
        .bind(duration_minutes)
        .bind(passing_dec)
        .fetch_one(&self.pool)
        .await?;
        let id: Uuid = row.try_get("id")?;
        Ok(id)
    }

    pub async fn get(&self, id: Uuid) -> Result<JsonValue> {
        let row = sqlx::query(
            r#"SELECT id, status, payload, result, error, test_id, created_at, started_at, finished_at FROM ai_jobs WHERE id=$1"#,
        )
        .bind(id)
        .fetch_one(&self.pool)
        .await?;
        Ok(serde_json::json!({
            "id": row.try_get::<Uuid,_>("id")?,
            "status": row.try_get::<String,_>("status")?,
            "payload": row.try_get::<JsonValue,_>("payload")?,
            "result": row.try_get::<Option<JsonValue>,_>("result")?,
            "error": row.try_get::<Option<String>,_>("error")?,
            "test_id": row.try_get::<Option<Uuid>,_>("test_id")?,
            "created_at": row.try_get::<chrono::DateTime<chrono::Utc>,_>("created_at")?,
            "started_at": row.try_get::<Option<chrono::DateTime<chrono::Utc>>,_>("started_at")?,
            "finished_at": row.try_get::<Option<chrono::DateTime<chrono::Utc>>,_>("finished_at")?,
        }))
    }

    pub async fn run_once(&self, app_state: &crate::AppState) -> Result<bool> {
        // pick one pending
        let rec = sqlx::query(
            r#"
            UPDATE ai_jobs SET status='running', started_at=NOW()
            WHERE id = (
                SELECT id FROM ai_jobs WHERE status='pending' ORDER BY created_at ASC FOR UPDATE SKIP LOCKED LIMIT 1
            )
            RETURNING id
            "#
        )
        .fetch_optional(&self.pool)
        .await?;
        let Some(row) = rec else { return Ok(false) };
        let job_id: Uuid = row.try_get("id")?;

        // fetch full payload row
        let job_row = sqlx::query(
            r#"SELECT id, payload, persist, title, description, duration_minutes, passing_score FROM ai_jobs WHERE id=$1"#,
        )
        .bind(job_id)
        .fetch_one(&self.pool)
        .await?;
        let payload: JsonValue = job_row.try_get("payload")?;
        let persist: Option<bool> = job_row.try_get("persist")?;
        let title: Option<String> = job_row.try_get("title")?;
        let description: Option<String> = job_row.try_get("description")?;
        let duration_minutes: Option<i32> = job_row.try_get("duration_minutes")?;
        let passing_score_dec: Option<Decimal> = job_row.try_get("passing_score")?;

        let profession = payload
            .get("profession")
            .and_then(|v| v.as_str())
            .unwrap_or("");
        let _cv = payload
            .get("cv_summary")
            .and_then(|v| v.as_str())
            .unwrap_or("");
        let skills: Vec<String> = payload
            .get("skills")
            .and_then(|v| v.as_array())
            .map(|a| {
                a.iter()
                    .filter_map(|e| e.as_str().map(|s| s.to_string()))
                    .collect()
            })
            .unwrap_or_default();
        let num_q = payload
            .get("num_questions")
            .and_then(|v| v.as_u64())
            .unwrap_or(6) as usize;
        let created_by_sub = payload
            .get("created_by_sub")
            .and_then(|v| v.as_str())
            .map(|s| s.to_string());

        // generate questions
        let gen_result = app_state
            .ai_service
            .generate_test(
                &app_state.embed_service,
                &app_state.eval_service,
                profession,
                &skills,
                num_q,
            )
            .await;

        let gen_output = match gen_result {
            Ok(out) => out,
            Err(e) => {
                let error_message = format!(
                    "AI generation failed permanently for job. job_id={} error={}",
                    job_id, e
                );
                tracing::error!("{}", error_message);
                sqlx::query("UPDATE ai_jobs SET status = 'failed', error = $1, finished_at = NOW() WHERE id = $2")
                    .bind(e.to_string())
                    .bind(job_id)
                    .execute(&self.pool)
                    .await?;
                return Ok(true);
            }
        };

        // Ensure we have at least the requested number of questions; top up if needed
        let mut questions = gen_output.questions;
        if questions.len() < num_q {
            let need = num_q - questions.len();
            tracing::warn!(
                "AI returned {} questions, topping up {} via fallbacks",
                questions.len(),
                need
            );
            let skills: Vec<String> = payload
                .get("skills")
                .and_then(|v| v.as_array())
                .map(|a| {
                    a.iter()
                        .filter_map(|e| e.as_str().map(|s| s.to_string()))
                        .collect()
                })
                .unwrap_or_default();
            let raw = serde_json::to_value(&questions)?;
            let filled = app_state
                .ai_service
                .sanitize_questions(&raw, num_q, &skills);
            // If sanitize produced zero (pathological), keep original
            if !filled.is_empty() {
                questions = filled;
            }
        }

        let questions_val = serde_json::to_value(&questions)?;

        // persist if requested
        let mut test_id: Option<Uuid> = None;
        if persist.unwrap_or(false) {
            let result = (|| async {
                let create_questions = app_state.ai_service.to_create_questions(&questions);
                // resolve created_by: by sub if present (create user if missing), else fallback to system
                let created_by = match created_by_sub {
                    Some(ref sub) if !sub.is_empty() => {
                        // find user by external_id=sub; if not, create minimal admin user
                        if let Some(row) = sqlx::query("SELECT id FROM users WHERE external_id=$1")
                            .bind(sub)
                            .fetch_optional(&self.pool)
                            .await? {
                            row.try_get::<Uuid,_>("id")?
                        } else {
                            let new_id = Uuid::new_v4();
                            sqlx::query(
                                r#"INSERT INTO users (id, external_id, name, email, role, is_active)
                                   VALUES ($1,$2,$3,$4,$5,true) ON CONFLICT (external_id) DO NOTHING"#,
                            )
                            .bind(new_id)
                            .bind(sub)
                            .bind(format!("{}", sub))
                            .bind(format!("{}@example.com", sub))
                            .bind("admin")
                            .execute(&self.pool)
                            .await?;
                            // fetch id (in case it existed)
                            let row = sqlx::query("SELECT id FROM users WHERE external_id=$1")
                                .bind(sub)
                                .fetch_one(&self.pool)
                                .await?;
                            row.try_get::<Uuid,_>("id")?
                        }
                    }
                    _ => {
                        // system user fallback (create if missing)
                        let sys = Uuid::parse_str("2cd84131-6e83-4c98-91ba-f9b9a5f0a06c").unwrap();
                        sqlx::query(
                            r#"INSERT INTO users (id, external_id, name, email, role, is_active)
                               VALUES ($1,'sys','System','sys@example.com','admin',true)
                               ON CONFLICT (id) DO NOTHING"#,
                        )
                        .bind(sys)
                        .execute(&self.pool)
                        .await?;
                        sys
                    }
                };
                let test_payload = crate::dto::integration_dto::CreateTestPayload {
                    title: title.unwrap_or_else(|| format!("AI {} Test", profession)),
                    external_id: None,
                    description,
                    instructions: None,
                    questions: create_questions,
                    duration_minutes: duration_minutes.unwrap_or(45),
                    passing_score: passing_score_dec.and_then(|d| d.to_string().parse::<f64>().ok()).unwrap_or(70.0),
                    shuffle_questions: Some(false),
                    shuffle_options: Some(false),
                    show_results_immediately: Some(false),
                };

                let test = app_state.test_service.create_test(test_payload, created_by).await?;
                anyhow::Ok(test.id)
            })()
            .await;

            match result {
                Ok(id) => {
                    test_id = Some(id);
                    // Also update the test with AI metadata for traceability
                    sqlx::query("UPDATE tests SET ai_metadata = $1 WHERE id = $2")
                        .bind(serde_json::json!({
                            "logs": gen_output.logs,
                        }))
                        .bind(id)
                        .execute(&self.pool)
                        .await?;
                }
                Err(e) => {
                    // mark job failed and exit
                    let error_message = format!("Failed to persist test: {}", e);
                    sqlx::query(
                        r#"UPDATE ai_jobs SET status='failed', error=$1, finished_at=NOW() WHERE id=$2"#,
                    )
                    .bind(error_message)
                    .bind(job_id)
                    .execute(&self.pool)
                    .await?;
                    return Ok(true);
                }
            }
        }

        sqlx::query(
            r#"UPDATE ai_jobs SET status='succeeded', result=$1, test_id=$2, finished_at=NOW() WHERE id=$3"#,
        )
        .bind(questions_val)
        .bind(test_id)
        .bind(job_id)
        .execute(&self.pool)
        .await?;

        Ok(true)
    }
}
