use crate::models::question::{QuestionDetails, QuestionType};
use serde::{Deserialize, Serialize};
use validator::Validate;

#[derive(Debug, Clone, Serialize, Deserialize, Validate)]
pub struct CreateQuestion {
    #[serde(rename = "type")]
    pub question_type: QuestionType,
    pub question: String,
    pub points: i32,
    #[serde(flatten)]
    pub details: QuestionDetails,
}

#[derive(Debug, Deserialize, Validate)]
pub struct GenerateVacancyDescriptionPayload {
    #[validate(length(min = 1))]
    pub title: String,
    #[validate(length(min = 1))]
    pub company: String,
    #[validate(length(min = 1))]
    pub location: String,
    pub language: Option<String>,
    #[validate(length(min = 1))]
    pub age: Option<String>,
    #[validate(length(min = 1))]
    pub education: Option<String>,
    #[validate(length(min = 1))]
    #[serde(rename = "working_experience")]
    pub working_experience: Option<String>,
    #[validate(length(min = 1))]
    #[serde(rename = "professional_skills")]
    pub professional_skills: Option<String>,
    #[validate(length(min = 1))]
    #[serde(rename = "computer_knowledge")]
    pub computer_knowledge: Option<String>,
    #[validate(length(min = 1))]
    #[serde(rename = "personal_qualities")]
    pub personal_qualities: Option<String>,
    #[validate(length(min = 1))]
    pub schedule: Option<String>,
}

#[derive(Debug, Clone, Serialize, Deserialize, Validate)]
pub struct CreateTestPayload {
    #[validate(length(min = 1))]
    pub title: String,
    pub external_id: Option<String>,
    pub description: Option<String>,
    pub instructions: Option<String>,
    #[validate(length(min = 1))]
    pub questions: Vec<CreateQuestion>,
    pub duration_minutes: i32,
    pub passing_score: f64,
    pub shuffle_questions: Option<bool>,
    pub shuffle_options: Option<bool>,
    pub show_results_immediately: Option<bool>,
}

#[derive(Debug, Deserialize, Validate)]
pub struct UpdateTestPayload {
    #[validate(length(min = 1, message = "Title cannot be empty"))]
    pub title: Option<String>,

    pub external_id: Option<String>,
    pub description: Option<String>,
    pub instructions: Option<String>,

    pub questions: Option<Vec<CreateQuestion>>,

    #[validate(range(min = 1, message = "Duration must be at least 1 minute"))]
    pub duration_minutes: Option<i32>,

    #[validate(range(
        min = 0.0,
        max = 100.0,
        message = "Passing score must be between 0 and 100"
    ))]
    pub passing_score: Option<f64>,

    #[validate(range(min = 1, message = "Max attempts must be at least 1"))]
    pub max_attempts: Option<i32>,

    pub shuffle_questions: Option<bool>,
    pub shuffle_options: Option<bool>,
    pub show_results_immediately: Option<bool>,
    pub is_active: Option<bool>,
}

#[derive(Debug, Deserialize, Clone)]
pub struct GenerateAiTestPayload {
    pub profession: String,
    pub cv_summary: Option<String>,
    pub skills: Option<Vec<String>>,
    pub num_questions: Option<usize>,
    pub persist: Option<bool>,
    pub title: Option<String>,
    pub description: Option<String>,
    pub duration_minutes: Option<i32>,
    pub passing_score: Option<f64>,
}

#[derive(Debug, Deserialize)]
pub struct EnqueueAiJobPayload {
    pub profession: String,
    pub cv_summary: Option<String>,
    pub skills: Option<Vec<String>>,
    pub num_questions: Option<usize>,
    pub persist: Option<bool>,
    pub title: Option<String>,
    pub description: Option<String>,
    pub duration_minutes: Option<i32>,
    pub passing_score: Option<f64>,
}

// Spec-based AI generation payload (from rust_kernel_tech_task.md)
#[derive(Debug, Deserialize, Clone)]
pub struct SpecGenerateTestPayload {
    pub position: String,
    pub topics: Vec<String>,
    pub difficulty: Option<String>,
    pub question_count: usize,
    pub duration_minutes: Option<i32>,
    pub question_types: Option<Vec<String>>, // ["multiple_choice","code","short_answer"]
    pub distribution: Option<serde_json::Value>,
}

#[derive(Debug, Deserialize, Validate)]
pub struct CandidatePayload {
    #[validate(length(min = 1, message = "Candidate name cannot be empty"))]
    pub name: String,
    #[validate(email(message = "Invalid email format"))]
    pub email: String,
}

#[derive(Debug, Deserialize, Validate)]
pub struct CreateInvitePayload {
    #[validate(nested)]
    pub candidate: CandidatePayload,
    pub test_id: uuid::Uuid,
    pub expires_in_hours: Option<u32>,
    pub send_notification: Option<bool>,
}
