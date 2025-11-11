use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use serde_json::Value as JsonValue;
use sqlx::FromRow;
use uuid::Uuid;

#[derive(Debug, Clone, Serialize, Deserialize, FromRow)]
pub struct AuditLog {
    pub id: Uuid,
    pub user_id: Option<Uuid>,
    pub action: String,
    pub entity_type: String,
    pub entity_id: Uuid,
    pub changes: Option<JsonValue>,
    pub ip_address: Option<sqlx::types::ipnetwork::IpNetwork>,
    pub user_agent: Option<String>,
    pub created_at: Option<DateTime<Utc>>,
}
