use crate::error::Error;
use crate::error::Result;
use crate::models::question::Question;
use crate::models::test::Test;
use rust_decimal::prelude::FromPrimitive;
use rust_decimal::Decimal;
use serde_json::Value as JsonValue;
use sqlx::PgPool;
use uuid::Uuid;
#[derive(Debug, serde::Serialize)]
pub struct PaginatedTests {
    pub tests: Vec<Test>,
    pub total: i64,
    pub page: i64,
    pub per_page: i64,
    pub total_pages: i64,
}

#[derive(Debug)]
pub struct TestFilter {
    pub is_active: Option<bool>,
    pub created_by: Option<Uuid>,
    pub search: Option<String>,
}

#[derive(Clone)]
pub struct TestService {
    pool: PgPool,
}

impl TestService {
    pub fn new(pool: PgPool) -> Self {
        Self { pool }
    }

    pub async fn create_test(
        &self,
        payload: crate::dto::integration_dto::CreateTestPayload,
        created_by: Uuid,
    ) -> Result<Test> {
        let questions_with_ids = assign_question_ids(&payload.questions);
        let questions_json = serde_json::to_value(&questions_with_ids)?;
        let passing_score_decimal = Decimal::from_f64(payload.passing_score)
            .ok_or_else(|| crate::error::Error::Anyhow(anyhow::anyhow!("Invalid passing score")))?;

        let test = sqlx::query_as!(
            Test,
            r#"
            INSERT INTO tests (title, external_id, description, instructions, questions, duration_minutes, passing_score, shuffle_questions, shuffle_options, show_results_immediately, created_by)
            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11)
            RETURNING 
                id,
                title,
                external_id,
                description,
                instructions,
                questions as "questions: JsonValue",
                duration_minutes,
                passing_score as "passing_score: rust_decimal::Decimal",
                max_attempts,
                shuffle_questions,
                shuffle_options,
                show_results_immediately,
                created_by,
                is_active,
                created_at,
                updated_at
            "#,
            payload.title,
            payload.external_id,
            payload.description,
            payload.instructions,
            questions_json,
            payload.duration_minutes,
            passing_score_decimal,
            payload.shuffle_questions.unwrap_or(false),
            payload.shuffle_options.unwrap_or(false),
            payload.show_results_immediately.unwrap_or(false),
            created_by
        )
        .fetch_one(&self.pool)
        .await?;

        Ok(test)
    }

    pub async fn get_test_by_id(&self, test_id: Uuid) -> Result<Test> {
        let test = sqlx::query_as!(
            Test,
            r#"
            SELECT 
                id, title, external_id, description, instructions, questions as "questions: JsonValue", 
                duration_minutes, passing_score as "passing_score: rust_decimal::Decimal", 
                max_attempts, shuffle_questions, shuffle_options, show_results_immediately, 
                created_by, is_active, created_at, updated_at
            FROM tests
            WHERE id = $1
            "#,
            test_id
        )
        .fetch_one(&self.pool)
        .await?;

        Ok(test)
    }

    pub async fn update_test(
        &self,
        test_id: Uuid,
        payload: crate::dto::integration_dto::UpdateTestPayload,
    ) -> Result<Test> {
        // Convert the questions to JSON if provided
        let questions_json = match payload.questions {
            Some(questions) => {
                let with_ids = assign_question_ids(&questions);
                Some(serde_json::to_value(with_ids)?)
            }
            None => None,
        };

        // Convert passing_score to Decimal if provided
        let passing_score_decimal = match payload.passing_score {
            Some(score) => Some(
                Decimal::from_f64(score)
                    .ok_or_else(|| Error::Anyhow(anyhow::anyhow!("Invalid passing score")))?,
            ),
            None => None,
        };

        let test = sqlx::query_as!(
            Test,
            r#"
            UPDATE tests
            SET
                title = COALESCE($1, title),
                external_id = COALESCE($2, external_id),
                description = COALESCE($3, description),
                instructions = COALESCE($4, instructions),
                questions = COALESCE($5, questions),
                duration_minutes = COALESCE($6, duration_minutes),
                passing_score = COALESCE($7, passing_score),
                max_attempts = COALESCE($8, max_attempts),
                shuffle_questions = COALESCE($9, shuffle_questions),
                shuffle_options = COALESCE($10, shuffle_options),
                show_results_immediately = COALESCE($11, show_results_immediately),
                is_active = COALESCE($12, is_active),
                updated_at = NOW()
            WHERE id = $13
            RETURNING
                id, title, external_id, description, instructions, questions as "questions: JsonValue",
                duration_minutes, passing_score as "passing_score: rust_decimal::Decimal",
                max_attempts, shuffle_questions, shuffle_options, show_results_immediately,
                created_by, is_active, created_at, updated_at
            "#,
            payload.title,
            payload.external_id,
            payload.description,
            payload.instructions,
            questions_json,
            payload.duration_minutes,
            passing_score_decimal,
            payload.max_attempts,
            payload.shuffle_questions,
            payload.shuffle_options,
            payload.show_results_immediately,
            payload.is_active,
            test_id
        )
        .fetch_one(&self.pool)
        .await?;

        Ok(test)
    }

    pub async fn list_tests(
        &self,
        page: i64,
        per_page: i64,
        filter: Option<TestFilter>,
    ) -> Result<PaginatedTests> {
        let offset = (page - 1) * per_page;
        let filter = filter.unwrap_or_else(|| TestFilter {
            is_active: None,
            created_by: None,
            search: None,
        });

        // Prepare optional filters
        let is_active_param: Option<bool> = filter.is_active;
        let created_by_param: Option<Uuid> = filter.created_by;
        let search_param: Option<String> = filter.search.map(|s| format!("%{}%", s));

        // Count with optional filters
        let total = sqlx::query_scalar!(
            r#"
            SELECT COUNT(*) as "count!" FROM tests
            WHERE ($1::bool IS NULL OR is_active = $1)
              AND ($2::uuid IS NULL OR created_by = $2)
              AND ($3::text IS NULL OR (title ILIKE $3 OR description ILIKE $3 OR external_id ILIKE $3))
            "#,
            is_active_param,
            created_by_param,
            search_param
        )
        .fetch_one(&self.pool)
        .await?;

        let total_pages = if per_page > 0 {
            ((total as f64) / (per_page as f64)).ceil() as i64
        } else {
            1
        };

        // Fetch page with same filters; select full Test shape
        let tests = sqlx::query_as!(
            Test,
            r#"
            SELECT 
                id,
                external_id,
                title,
                description,
                instructions,
                questions as "questions: JsonValue",
                duration_minutes,
                passing_score as "passing_score: rust_decimal::Decimal",
                max_attempts,
                shuffle_questions,
                shuffle_options,
                show_results_immediately,
                created_by,
                is_active,
                created_at,
                updated_at
            FROM tests
            WHERE ($1::bool IS NULL OR is_active = $1)
              AND ($2::uuid IS NULL OR created_by = $2)
              AND ($3::text IS NULL OR (title ILIKE $3 OR description ILIKE $3 OR external_id ILIKE $3))
            ORDER BY created_at DESC
            LIMIT $4 OFFSET $5
            "#,
            is_active_param,
            created_by_param,
            search_param,
            per_page,
            offset
        )
        .fetch_all(&self.pool)
        .await?;

        Ok(PaginatedTests {
            tests,
            total,
            page,
            per_page,
            total_pages,
        })
    }

    pub async fn delete_test(&self, test_id: Uuid) -> Result<bool> {
        let result = sqlx::query!("DELETE FROM tests WHERE id = $1", test_id)
            .execute(&self.pool)
            .await?;

        Ok(result.rows_affected() > 0)
    }
}

fn assign_question_ids(
    questions: &Vec<crate::dto::integration_dto::CreateQuestion>,
) -> Vec<Question> {
    questions
        .iter()
        .enumerate()
        .map(|(idx, q)| Question {
            id: (idx as i32) + 1,
            question_type: q.question_type.clone(),
            question: q.question.clone(),
            points: q.points,
            details: q.details.clone(),
        })
        .collect()
}

#[cfg(test)]
mod tests {
    use super::*;
    use dotenvy::dotenv;
    use sqlx::postgres::PgPoolOptions;
    use std::env;

    async fn setup_test_db() -> PgPool {
        dotenv().ok();
        let database_url = env::var("DATABASE_URL").expect("DATABASE_URL must be set");
        let pool = PgPoolOptions::new()
            .max_connections(5)
            .connect(&database_url)
            .await
            .expect("Failed to create test pool");

        // Run migrations
        sqlx::migrate!("./migrations")
            .run(&pool)
            .await
            .expect("Failed to run migrations");

        pool
    }

    #[tokio::test]
    async fn test_list_tests() {
        let pool = setup_test_db().await;
        let service = TestService::new(pool);

        // Create a test user
        let user_id = Uuid::new_v4();
        sqlx::query!(
            r#"INSERT INTO users (id, external_id, name, email, role, is_active)
               VALUES ($1, $2, $3, $4, $5, $6)"#,
            user_id,
            format!("ext-{}", user_id),
            "Test User",
            format!("test_{}@example.com", user_id),
            "hr",
            true
        )
        .execute(&service.pool)
        .await
        .expect("failed to insert test user");

        // Create some test data
        let test1 = service
            .create_test(
                crate::dto::integration_dto::CreateTestPayload {
                    title: "Test 1".to_string(),
                    external_id: Some("ext1".to_string()),
                    description: Some("Description 1".to_string()),
                    instructions: None,
                    questions: vec![],
                    duration_minutes: 60,
                    passing_score: 70.0,
                    shuffle_questions: Some(false),
                    shuffle_options: Some(false),
                    show_results_immediately: Some(false),
                },
                user_id,
            )
            .await
            .unwrap();

        let test2 = service
            .create_test(
                crate::dto::integration_dto::CreateTestPayload {
                    title: "Test 2".to_string(),
                    external_id: Some("ext2".to_string()),
                    description: Some("Description 2".to_string()),
                    instructions: None,
                    questions: vec![],
                    duration_minutes: 30,
                    passing_score: 80.0,
                    shuffle_questions: Some(true),
                    shuffle_options: Some(true),
                    show_results_immediately: Some(true),
                },
                user_id,
            )
            .await
            .unwrap();

        // Test listing all tests
        let result = service.list_tests(1, 10, None).await.unwrap();
        assert!(result.tests.len() >= 2);
        assert!(result.total >= 2);

        // Test filtering by is_active
        let result = service
            .list_tests(
                1,
                10,
                Some(TestFilter {
                    is_active: Some(true),
                    created_by: None,
                    search: None,
                }),
            )
            .await
            .unwrap();

        assert!(result.tests.iter().all(|t| t.is_active.unwrap_or(true)));

        // Test search
        let result = service
            .list_tests(
                1,
                10,
                Some(TestFilter {
                    is_active: None,
                    created_by: None,
                    search: Some("Test 1".to_string()),
                }),
            )
            .await
            .unwrap();

        assert_eq!(result.tests[0].title, "Test 1");
        assert_eq!(result.total, 1);

        // Clean up
        service.delete_test(test1.id).await.unwrap();
        service.delete_test(test2.id).await.unwrap();
    }
}
