use serde::{Deserialize, Serialize};

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct TestAssignedWebhook {
    pub event: String,
    pub attempt_id: uuid::Uuid,
    pub candidate: WebhookCandidate,
    pub test: WebhookTest,
    pub access_token: String,
    pub expires_at: chrono::DateTime<chrono::Utc>,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct TestCompletedWebhook {
    pub event: String,
    pub attempt_id: uuid::Uuid,
    pub candidate: WebhookCandidate,
    pub test: WebhookTest,
    pub score: f64,
    pub percentage: f64,
    pub passed: bool,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct WebhookCandidate {
    pub name: String,
    pub telegram_id: Option<i64>,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct WebhookTest {
    pub title: String,
}
