use crate::error::Result;
use crate::models::test::Test;
use crate::models::test_attempt::TestAttempt;
use crate::utils::token::generate_access_token;
use crate::dto::public_dto::{SaveAnswerRequest, SubmitTestRequest};
use crate::models::question::Question;
use rust_decimal::prelude::FromPrimitive;
use rust_decimal::Decimal;
use chrono::{DateTime, Duration, Utc};
use serde_json::json;
use sqlx::PgPool;
use uuid::Uuid;

#[derive(Clone)]
pub struct AttemptService {
    pool: PgPool,
}

impl AttemptService {
    pub fn new(pool: PgPool) -> Self { Self { pool } }

    pub async fn create_invite(
        &self,
        test_id: Uuid,
        candidate: InviteCandidate,
        expires_in_hours: i64,
        metadata: Option<serde_json::Value>,
    ) -> Result<CreateInviteResult> {
        let test = sqlx::query_as!(
            Test,
            r#"SELECT 
                id, external_id, title, description, instructions, 
                questions as "questions: serde_json::Value",
                duration_minutes,
                passing_score as "passing_score: rust_decimal::Decimal",
                max_attempts, shuffle_questions, shuffle_options, show_results_immediately,
                created_by, is_active, created_at, updated_at
            FROM tests WHERE id = $1"#,
            test_id
        )
        .fetch_one(&self.pool)
        .await?;

        let access_token = generate_access_token(32);
        let expires_at: DateTime<Utc> = Utc::now() + Duration::hours(expires_in_hours);

        let attempt = sqlx::query_as::<_, TestAttempt>(
            r#"
            INSERT INTO test_attempts (
                test_id, candidate_external_id, candidate_name, candidate_email, candidate_telegram_id, candidate_phone,
                access_token, expires_at, questions_snapshot, answers, score, max_score, percentage, passed,
                started_at, completed_at, time_spent_seconds, status, ip_address, user_agent, tab_switches, suspicious_activity, metadata
            ) VALUES (
                $1, $2, $3, $4, $5, $6,
                $7, $8, $9, NULL, NULL, NULL, NULL, NULL,
                NULL, NULL, NULL, 'pending', NULL, NULL, 0, NULL, $10
            )
            RETURNING *
            "#
        )
        .bind(test.id)
        .bind(candidate.external_id)
        .bind(candidate.name)
        .bind(candidate.email)
        .bind(candidate.telegram_id)
        .bind(candidate.phone)
        .bind(access_token.clone())
        .bind(expires_at)
        .bind(test.questions)
        .bind(metadata)
        .fetch_one(&self.pool)
        .await?;

        Ok(CreateInviteResult {
            attempt_id: attempt.id,
            access_token: attempt.access_token,
            expires_at,
            status: attempt.status,
        })
    }

    pub async fn get_attempt_and_test_by_token(&self, token: &str) -> Result<(TestAttempt, Test)> {
        let attempt = sqlx::query_as::<_, TestAttempt>(
            r#"SELECT * FROM test_attempts WHERE access_token = $1"#
        )
        .bind(token)
        .fetch_one(&self.pool)
        .await?;

        let test = sqlx::query_as!(
            Test,
            r#"SELECT 
                id, external_id, title, description, instructions, 
                questions as "questions: serde_json::Value",
                duration_minutes,
                passing_score as "passing_score: rust_decimal::Decimal",
                max_attempts, shuffle_questions, shuffle_options, show_results_immediately,
                created_by, is_active, created_at, updated_at
            FROM tests WHERE id = $1"#,
            attempt.test_id
        )
        .fetch_one(&self.pool)
        .await?;

        Ok((attempt, test))
    }

    pub async fn start_attempt_by_token(&self, token: &str) -> Result<TestAttempt> {
        // Load attempt and test
        let (attempt, test) = self.get_attempt_and_test_by_token(token).await?;

        let now = Utc::now();
        let expires_candidate = now + Duration::minutes(test.duration_minutes as i64);
        let new_expires = if expires_candidate < attempt.expires_at { expires_candidate } else { attempt.expires_at };

        let updated = sqlx::query_as::<_, TestAttempt>(
            r#"
            UPDATE test_attempts
            SET status = 'in_progress', started_at = COALESCE(started_at, $1), expires_at = $2
            WHERE access_token = $3
            RETURNING *
            "#
        )
        .bind(now)
        .bind(new_expires)
        .bind(token)
        .fetch_one(&self.pool)
        .await?;

        Ok(updated)
    }

    pub async fn save_answer_by_token(&self, token: &str, req: SaveAnswerRequest) -> Result<DateTime<Utc>> {
        let (mut attempt, _test) = self.get_attempt_and_test_by_token(token).await?;
        let timestamp = Utc::now();

        // Insert into answer_logs
        sqlx::query!(
            r#"INSERT INTO answer_logs (attempt_id, question_id, answer_value, time_spent_seconds) VALUES ($1, $2, $3, $4)"#,
            attempt.id,
            req.question_id,
            req.answer,
            req.time_spent_seconds
        )
        .execute(&self.pool)
        .await?;

        // Update attempt.answers JSON (merge or insert)
        let mut answers: Vec<serde_json::Value> = match attempt.answers.take() {
            Some(v) => serde_json::from_value(v).unwrap_or_default(),
            None => Vec::new(),
        };

        let new_item = json!({
            "question_id": req.question_id,
            "answer": req.answer,
            "time_spent": req.time_spent_seconds,
            "marked_for_review": req.marked_for_review.unwrap_or(false),
            "answered_at": timestamp,
        });

        // Upsert by question_id
        if let Some(pos) = answers.iter().position(|a| a.get("question_id").and_then(|v| v.as_i64()) == Some(req.question_id as i64)) {
            answers[pos] = new_item;
        } else {
            answers.push(new_item);
        }

        let answers_json = serde_json::to_value(answers)?;
        sqlx::query!(
            r#"UPDATE test_attempts SET answers = $1, updated_at = NOW() WHERE id = $2"#,
            answers_json,
            attempt.id
        )
        .execute(&self.pool)
        .await?;

        Ok(timestamp)
    }

    pub async fn submit_attempt_by_token(&self, token: &str, req: SubmitTestRequest) -> Result<(TestAttempt, f64, f64, f64, bool)> {
        let (attempt, test) = self.get_attempt_and_test_by_token(token).await?;

        // Update answers from request (to be authoritative)
        let answers_json = serde_json::to_value(&req.answers)?;
        sqlx::query!(
            r#"UPDATE test_attempts SET answers = $1 WHERE id = $2"#,
            answers_json,
            attempt.id
        )
        .execute(&self.pool)
        .await?;

        // Compute grading (MCQ only for MVP) via GradingService
        let questions: Vec<Question> = serde_json::from_value(test.questions.clone()).unwrap_or_default();
        let answers: Vec<serde_json::Value> = serde_json::from_value(answers_json.clone()).unwrap_or_default();
        let (earned_points, total_max_points, graded_answers) = crate::services::grading_service::GradingService::grade_mcq_only(&questions, &answers);

        let score_f = earned_points as f64;
        let max_score_f = total_max_points as f64;
        let percentage = if max_score_f > 0.0 { (score_f / max_score_f) * 100.0 } else { 0.0 };
        let passing_threshold = test.passing_score.to_string().parse::<f64>().unwrap_or(0.0);
        let passed = percentage >= passing_threshold;

        let graded_json = serde_json::to_value(graded_answers)?;
        let now = Utc::now();
        let score_dec = Decimal::from_f64(score_f).unwrap_or_else(|| Decimal::new(0, 0));
        let max_score_dec = Decimal::from_f64(max_score_f).unwrap_or_else(|| Decimal::new(0, 0));
        let percentage_dec = Decimal::from_f64(percentage).unwrap_or_else(|| Decimal::new(0, 0));

        let updated = sqlx::query_as::<_, TestAttempt>(
            r#"
            UPDATE test_attempts
            SET status = 'completed', completed_at = $1, time_spent_seconds = COALESCE(time_spent_seconds, 0),
                score = $2, max_score = $3, percentage = $4, passed = $5, graded_answers = $6
            WHERE id = $7
            RETURNING *
            "#
        )
        .bind(now)
        .bind(score_dec)
        .bind(max_score_dec)
        .bind(percentage_dec)
        .bind(passed)
        .bind(graded_json)
        .bind(attempt.id)
        .fetch_one(&self.pool)
        .await?;

        Ok((updated, score_f, max_score_f, percentage, passed))
    }

    pub async fn get_attempt_by_id(&self, attempt_id: Uuid) -> Result<TestAttempt> {
        let attempt = sqlx::query_as::<_, TestAttempt>(
            r#"SELECT * FROM test_attempts WHERE id = $1"#
        )
        .bind(attempt_id)
        .fetch_one(&self.pool)
        .await?;
        Ok(attempt)
    }

    pub async fn list_attempts(
        &self,
        test_id: Option<Uuid>,
        candidate_email: Option<String>,
        status: Option<String>,
        page: i64,
        limit: i64,
    ) -> Result<(Vec<TestAttempt>, i64)> {
        let offset = (page - 1) * limit;
        let rows = sqlx::query_as::<_, TestAttempt>(
            r#"
            SELECT * FROM test_attempts
            WHERE ($1::uuid IS NULL OR test_id = $1)
              AND ($2::text IS NULL OR candidate_email = $2)
              AND ($3::text IS NULL OR status = $3)
            ORDER BY created_at DESC
            LIMIT $4 OFFSET $5
            "#
        )
        .bind(test_id)
        .bind(candidate_email.clone())
        .bind(status.clone())
        .bind(limit)
        .bind(offset)
        .fetch_all(&self.pool)
        .await?;

        let total = sqlx::query_scalar!(
            r#"SELECT COUNT(*) as "count!" FROM test_attempts
               WHERE ($1::uuid IS NULL OR test_id = $1)
                 AND ($2::text IS NULL OR candidate_email = $2)
                 AND ($3::text IS NULL OR status = $3)"#,
            test_id,
            candidate_email,
            status
        )
        .fetch_one(&self.pool)
        .await?;

        Ok((rows, total))
    }
}

#[derive(Debug, Clone)]
pub struct InviteCandidate {
    pub external_id: Option<String>,
    pub name: String,
    pub email: String,
    pub telegram_id: Option<i64>,
    pub phone: Option<String>,
}

#[derive(Debug, Clone)]
pub struct CreateInviteResult {
    pub attempt_id: Uuid,
    pub access_token: String,
    pub expires_at: DateTime<Utc>,
    pub status: String,
}
