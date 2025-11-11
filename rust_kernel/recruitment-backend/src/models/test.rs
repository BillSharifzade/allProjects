use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use serde_json::Value as JsonValue;
use sqlx::FromRow;
use uuid::Uuid;

#[derive(Debug, Clone, Serialize, Deserialize, FromRow)]
pub struct Test {
    pub id: Uuid,
    pub external_id: Option<String>,
    pub title: String,
    pub description: Option<String>,
    pub instructions: Option<String>,
    pub questions: JsonValue, // JSONB -> serde_json::Value
    pub duration_minutes: i32,
    pub passing_score: rust_decimal::Decimal,
    pub max_attempts: Option<i32>,
    pub shuffle_questions: Option<bool>,
    pub shuffle_options: Option<bool>,
    pub show_results_immediately: Option<bool>,
    pub created_by: Option<Uuid>,
    pub is_active: Option<bool>,
    pub created_at: Option<DateTime<Utc>>,
    pub updated_at: Option<DateTime<Utc>>,
}
