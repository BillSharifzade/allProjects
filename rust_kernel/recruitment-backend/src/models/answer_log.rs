use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use serde_json::Value as JsonValue;
use sqlx::FromRow;
use uuid::Uuid;

#[derive(Debug, Clone, Serialize, Deserialize, FromRow)]
pub struct AnswerLog {
    pub id: Uuid,
    pub attempt_id: Uuid,
    pub question_id: i32,
    pub answer_value: JsonValue,
    pub time_spent_seconds: Option<i32>,
    pub created_at: DateTime<Utc>,
}
