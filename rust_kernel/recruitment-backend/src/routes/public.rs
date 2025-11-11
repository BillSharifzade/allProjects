use axum::{
    extract::{Path, State},
    http::StatusCode,
    response::{IntoResponse, Json, Response},
};
use chrono::Utc;
use serde_json::json;
use validator::Validate;

use crate::dto::public_dto::{
    GetTestByTokenResponse, SaveAnswerRequest, SaveAnswerResponse, StartTestResponse,
    StatusResponse, SubmitTestRequest, SubmitTestResponse,
};
use crate::services::attempt_service::AttemptService;
use crate::services::audit_service::AuditService;
use crate::services::notification_service::NotificationService;
use crate::AppState;

#[axum::debug_handler]
pub async fn get_test_by_token(
    State(state): State<AppState>,
    Path(token): Path<String>,
) -> crate::error::Result<Response> {
    let svc = AttemptService::new(state.pool.clone());
    let (attempt, test) = svc.get_attempt_and_test_by_token(&token).await?;
    if attempt.expires_at <= Utc::now() {
        return Ok((
            StatusCode::FORBIDDEN,
            Json(json!({
                "error": "test_expired",
                "message": "This test invitation has expired"
            })),
        )
            .into_response());
    }

    let questions: Vec<crate::models::question::Question> =
        serde_json::from_value(test.questions.clone()).unwrap_or_default();
    let response = GetTestByTokenResponse {
        test: crate::dto::public_dto::PublicTestSummary {
            title: test.title,
            description: test.description,
            instructions: test.instructions,
            duration_minutes: test.duration_minutes,
            total_questions: questions.len(),
            passing_score: test.passing_score.to_string().parse::<f64>().unwrap_or(0.0),
        },
        attempt: crate::dto::public_dto::PublicAttemptSummary {
            id: attempt.id,
            status: attempt.status,
            expires_at: attempt.expires_at,
            candidate_name: attempt.candidate_name,
        },
    };
    Ok(Json(response).into_response())
}

#[axum::debug_handler]
pub async fn start_test(
    State(state): State<AppState>,
    Path(token): Path<String>,
) -> crate::error::Result<Response> {
    let svc = AttemptService::new(state.pool.clone());
    let (attempt, _test) = svc.get_attempt_and_test_by_token(&token).await?;
    if attempt.expires_at <= Utc::now() {
        return Ok((
            StatusCode::FORBIDDEN,
            Json(json!({
                "error": "test_expired",
                "message": "This test invitation has expired"
            })),
        )
            .into_response());
    }
    let updated = svc.start_attempt_by_token(&token).await?;
    let response = StartTestResponse {
        attempt_id: updated.id,
        status: updated.status,
        started_at: updated.started_at.unwrap_or(Utc::now()),
        expires_at: updated.expires_at,
        questions: updated.questions_snapshot,
    };
    Ok(Json(response).into_response())
}

#[axum::debug_handler]
pub async fn save_answer(
    State(state): State<AppState>,
    Path(token): Path<String>,
    Json(req): Json<SaveAnswerRequest>,
) -> crate::error::Result<Response> {
    req.validate()?;
    let svc = AttemptService::new(state.pool.clone());
    let (attempt, _test) = svc.get_attempt_and_test_by_token(&token).await?;
    if attempt.expires_at <= Utc::now() {
        return Ok((
            StatusCode::FORBIDDEN,
            Json(json!({
                "error": "test_expired",
                "message": "This test invitation has expired"
            })),
        )
            .into_response());
    }
    let question_id = req.question_id;
    let ts = svc.save_answer_by_token(&token, req).await?;
    Ok(Json(SaveAnswerResponse {
        saved: true,
        question_id,
        timestamp: ts,
    })
    .into_response())
}

#[axum::debug_handler]
pub async fn submit_test(
    State(state): State<AppState>,
    Path(token): Path<String>,
    Json(req): Json<SubmitTestRequest>,
) -> crate::error::Result<Response> {
    let svc = AttemptService::new(state.pool.clone());
    let (attempt0, _test) = svc.get_attempt_and_test_by_token(&token).await?;
    if attempt0.expires_at <= Utc::now() {
        return Ok((
            StatusCode::FORBIDDEN,
            Json(json!({
                "error": "test_expired",
                "message": "This test invitation has expired"
            })),
        )
            .into_response());
    }
    let (attempt, score, max_score, percentage, passed) =
        svc.submit_attempt_by_token(&token, req).await?;

    // Notify bot (test_completed)
    let notif = NotificationService::new(
        state.pool.clone(),
        crate::config::get_config().telegram_bot_webhook_url.clone(),
    );
    let test = state.test_service.get_test_by_id(attempt.test_id).await?;
    let completed = crate::dto::webhook_dto::TestCompletedWebhook {
        event: "test_completed".to_string(),
        attempt_id: attempt.id,
        candidate: crate::dto::webhook_dto::WebhookCandidate {
            name: attempt.candidate_name.clone(),
            telegram_id: attempt.candidate_telegram_id,
        },
        test: crate::dto::webhook_dto::WebhookTest {
            title: test.title.clone(),
        },
        score,
        percentage,
        passed,
    };
    let payload_json = serde_json::to_value(&completed)?;
    let _ = notif
        .enqueue_webhook("test_completed", &payload_json)
        .await?;

    // Audit log
    let audit = AuditService::new(state.pool.clone());
    let _ = audit
        .log(
            None,
            "submit_attempt",
            "test_attempt",
            attempt.id,
            Some(serde_json::json!({"score": score, "percentage": percentage, "passed": passed})),
            None,
            None,
        )
        .await?;
    let resp = SubmitTestResponse {
        attempt_id: attempt.id,
        status: attempt.status,
        score,
        max_score,
        percentage,
        passed,
        show_results: false,
        message: "Test submitted successfully. Results have been sent to HR.".to_string(),
    };
    Ok(Json(resp).into_response())
}

#[axum::debug_handler]
pub async fn get_status(
    State(state): State<AppState>,
    Path(token): Path<String>,
) -> crate::error::Result<Response> {
    let svc = AttemptService::new(state.pool.clone());
    let (attempt, test) = svc.get_attempt_and_test_by_token(&token).await?;
    let total_questions: i32 = match serde_json::from_value::<Vec<serde_json::Value>>(
        attempt.questions_snapshot.clone(),
    ) {
        Ok(v) => v.len() as i32,
        Err(_) => 0,
    };
    let answered: i32 = match attempt.answers.clone() {
        Some(v) => serde_json::from_value::<Vec<serde_json::Value>>(v)
            .map(|a| a.len() as i32)
            .unwrap_or(0),
        None => 0,
    };
    let time_remaining = attempt.started_at.map(|started| {
        let end = started + chrono::Duration::minutes(test.duration_minutes as i64);
        let now = Utc::now();
        (end - now).num_seconds().max(0) as i32
    });
    let resp = StatusResponse {
        status: attempt.status,
        started_at: attempt.started_at,
        time_remaining_seconds: time_remaining,
        questions_answered: Some(answered),
        total_questions: Some(total_questions),
    };
    Ok(Json(resp).into_response())
}
