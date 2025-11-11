use serde::{Deserialize, Serialize};
use validator::Validate;

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct PublicTestSummary {
    pub title: String,
    pub description: Option<String>,
    pub instructions: Option<String>,
    pub duration_minutes: i32,
    pub total_questions: usize,
    pub passing_score: f64,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct PublicAttemptSummary {
    pub id: uuid::Uuid,
    pub status: String,
    pub expires_at: chrono::DateTime<chrono::Utc>,
    pub candidate_name: String,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct GetTestByTokenResponse {
    pub test: PublicTestSummary,
    pub attempt: PublicAttemptSummary,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct StartTestResponse {
    pub attempt_id: uuid::Uuid,
    pub status: String,
    pub started_at: chrono::DateTime<chrono::Utc>,
    pub expires_at: chrono::DateTime<chrono::Utc>,
    pub questions: serde_json::Value,
}

#[derive(Debug, Clone, Serialize, Deserialize, Validate)]
pub struct SaveAnswerRequest {
    pub question_id: i32,
    pub answer: serde_json::Value,
    pub time_spent_seconds: i32,
    pub marked_for_review: Option<bool>,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct SaveAnswerResponse {
    pub saved: bool,
    pub question_id: i32,
    pub timestamp: chrono::DateTime<chrono::Utc>,
}

#[derive(Debug, Clone, Serialize, Deserialize, Validate)]
pub struct SubmitTestRequest {
    pub answers: Vec<SaveAnswerRequest>,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct SubmitTestResponse {
    pub attempt_id: uuid::Uuid,
    pub status: String,
    pub score: f64,
    pub max_score: f64,
    pub percentage: f64,
    pub passed: bool,
    pub show_results: bool,
    pub message: String,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct StatusResponse {
    pub status: String,
    pub started_at: Option<chrono::DateTime<chrono::Utc>>,
    pub time_remaining_seconds: Option<i32>,
    pub questions_answered: Option<i32>,
    pub total_questions: Option<i32>,
}
