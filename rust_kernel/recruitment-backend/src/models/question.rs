use serde::{Deserialize, Serialize};

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct Question {
    #[serde(default)]
    pub id: i32,
    #[serde(rename = "type")]
    pub question_type: QuestionType,
    pub question: String,
    #[serde(default = "default_points")]
    pub points: i32,
    #[serde(flatten)]
    pub details: QuestionDetails,
}

fn default_points() -> i32 {
    1
}

#[derive(Debug, Clone, Serialize, Deserialize)]
#[serde(rename_all = "snake_case")]
pub enum QuestionType {
    MultipleChoice,
    Code,
    ShortAnswer,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
#[serde(rename_all = "snake_case")]
#[serde(untagged)] // Use untagged to avoid extra 'type' field in JSON
pub enum QuestionDetails {
    MultipleChoice(MultipleChoiceDetails),
    Code(CodeDetails),
    ShortAnswer(ShortAnswerDetails),
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct MultipleChoiceDetails {
    pub options: Vec<String>,
    pub correct_answer: i32,
    pub explanation: Option<String>,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct CodeDetails {
    pub language: String,
    pub starter_code: Option<String>,
    pub test_cases: Vec<TestCase>,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct TestCase {
    pub input: String,
    pub expected: String,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct ShortAnswerDetails {
    pub expected_keywords: Option<Vec<String>>,
    pub min_words: Option<i32>,
    pub ai_grading: bool,
}
