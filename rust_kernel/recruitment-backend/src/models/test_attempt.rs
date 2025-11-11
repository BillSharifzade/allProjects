use chrono::{DateTime, Utc};
use serde::{Deserialize, Serialize};
use serde_json::Value as JsonValue;
use sqlx::FromRow;
use uuid::Uuid;

#[derive(Debug, Clone, Serialize, Deserialize, FromRow)]
pub struct TestAttempt {
    pub id: Uuid,
    pub test_id: Uuid,
    pub candidate_external_id: Option<String>,
    pub candidate_name: String,
    pub candidate_email: String,
    pub candidate_telegram_id: Option<i64>,
    pub candidate_phone: Option<String>,
    pub access_token: String,
    pub expires_at: DateTime<Utc>,
    pub questions_snapshot: JsonValue,
    pub answers: Option<JsonValue>,
    pub score: Option<rust_decimal::Decimal>,
    pub max_score: Option<rust_decimal::Decimal>,
    pub percentage: Option<rust_decimal::Decimal>,
    pub passed: Option<bool>,
    pub graded_answers: Option<JsonValue>,
    pub started_at: Option<DateTime<Utc>>,
    pub completed_at: Option<DateTime<Utc>>,
    pub time_spent_seconds: Option<i32>,
    pub status: String, // Consider an enum
    pub ip_address: Option<sqlx::types::ipnetwork::IpNetwork>,
    pub user_agent: Option<String>,
    pub tab_switches: Option<i32>,
    pub suspicious_activity: Option<JsonValue>,
    pub metadata: Option<JsonValue>,
    pub created_at: Option<DateTime<Utc>>,
    pub updated_at: Option<DateTime<Utc>>,
}
