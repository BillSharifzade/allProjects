use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use serde_json::Value as JsonValue;

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct Answer {
    pub question_id: i32,
    pub answer: JsonValue, // Flexible to hold different answer types
    pub time_spent: i32,
    pub marked_for_review: bool,
    pub answered_at: DateTime<Utc>,
}
