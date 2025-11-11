use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use serde_json::Value as JsonValue;
use sqlx::FromRow;
use uuid::Uuid;

#[derive(Debug, Clone, Serialize, Deserialize, FromRow)]
pub struct WebhookLog {
    pub id: Uuid,
    pub event_type: String,
    pub payload: JsonValue,
    pub target_url: String,
    pub http_status: Option<i32>,
    pub response_body: Option<String>,
    pub attempts: Option<i32>,
    pub max_attempts: Option<i32>,
    pub next_retry_at: Option<DateTime<Utc>>,
    pub status: Option<String>,
    pub created_at: Option<DateTime<Utc>>,
    pub updated_at: Option<DateTime<Utc>>,
}
