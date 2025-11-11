use crate::models::question::{Question, QuestionDetails, QuestionType};
use serde_json::Value as JsonValue;

pub struct GradingService;

impl GradingService {
    pub fn grade_mcq_only(
        questions: &[Question],
        answers: &[JsonValue],
    ) -> (i32, i32, Vec<JsonValue>) {
        let mut total_max_points: i32 = 0;
        let mut earned_points: i32 = 0;
        let mut graded: Vec<JsonValue> = Vec::new();

        for (idx, q) in questions.iter().enumerate() {
            total_max_points += q.points;
            let question_id = q.id.max((idx as i32) + 1);
            let ans = answers.iter().find(|a| {
                a.get("question_id").and_then(|v| v.as_i64()) == Some(question_id as i64)
            });
            match q.question_type {
                QuestionType::MultipleChoice => {
                    let mut score = 0;
                    let mut is_correct = false;
                    if let Some(a) = ans {
                        // Accept both {"answer": 3} and {"answer": {"selected": 3}}
                        let candidate = a.get("answer").cloned().unwrap_or(serde_json::json!(null));
                        let given_idx_opt = candidate
                            .as_i64()
                            .or_else(|| candidate.get("selected").and_then(|v| v.as_i64()));
                        if let Some(given_idx) = given_idx_opt {
                            if let QuestionDetails::MultipleChoice(ref mc) = q.details {
                                if given_idx as i32 == mc.correct_answer {
                                    score = q.points;
                                    is_correct = true;
                                }
                            }
                        }
                    }
                    earned_points += score;
                    graded.push(serde_json::json!({
                        "question_id": question_id,
                        "score": score,
                        "max_score": q.points,
                        "is_correct": is_correct,
                    }));
                }
                _ => {
                    graded.push(serde_json::json!({
                        "question_id": question_id,
                        "score": 0,
                        "max_score": q.points,
                        "is_correct": false,
                    }));
                }
            }
        }

        (earned_points, total_max_points, graded)
    }
}
