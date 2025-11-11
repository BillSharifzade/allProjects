use axum::{extract::State, http::StatusCode, Json};
use serde::Deserialize;
use subtle::ConstantTimeEq;

use crate::{
    config::get_config,
    error::{Error, Result},
    AppState,
};

#[derive(Debug, Deserialize)]
pub struct WebhookEnvelope<T> {
    pub event: String,
    #[serde(flatten)]
    pub payload: T,
}

#[derive(Debug, Deserialize)]
pub struct TestAssignedPayload {
    pub attempt_id: uuid::Uuid,
    pub candidate: serde_json::Value,
    pub test: serde_json::Value,
    pub access_token: String,
    pub expires_at: chrono::DateTime<chrono::Utc>,
}

#[derive(Debug, Deserialize)]
pub struct TestCompletedPayload {
    pub attempt_id: uuid::Uuid,
    pub candidate: serde_json::Value,
    pub test: serde_json::Value,
    pub score: Option<f32>,
    pub passed: Option<bool>,
}

pub async fn handle_test_assigned(
    State(state): State<AppState>,
    headers: axum::http::HeaderMap,
    Json(envelope): Json<WebhookEnvelope<TestAssignedPayload>>,
) -> Result<(StatusCode, Json<serde_json::Value>)> {
    verify_secret(&headers)?;
    if envelope.event != "test_assigned" {
        return Ok((
            StatusCode::BAD_REQUEST,
            Json(serde_json::json!({ "error": "unexpected_event" })),
        ));
    }

    let payload = serde_json::json!({
        "attempt_id": envelope.payload.attempt_id,
        "candidate": envelope.payload.candidate,
        "test": envelope.payload.test,
        "access_token": envelope.payload.access_token,
        "expires_at": envelope.payload.expires_at,
    });

    state
        .notification_service
        .enqueue_webhook("test_assigned", &payload)
        .await?;

    Ok((
        StatusCode::ACCEPTED,
        Json(serde_json::json!({ "queued": true })),
    ))
}

pub async fn handle_test_completed(
    State(state): State<AppState>,
    headers: axum::http::HeaderMap,
    Json(envelope): Json<WebhookEnvelope<TestCompletedPayload>>,
) -> Result<(StatusCode, Json<serde_json::Value>)> {
    verify_secret(&headers)?;
    if envelope.event != "test_completed" {
        return Ok((
            StatusCode::BAD_REQUEST,
            Json(serde_json::json!({ "error": "unexpected_event" })),
        ));
    }

    let payload = serde_json::json!({
        "attempt_id": envelope.payload.attempt_id,
        "candidate": envelope.payload.candidate,
        "test": envelope.payload.test,
        "score": envelope.payload.score,
        "passed": envelope.payload.passed,
    });

    state
        .notification_service
        .enqueue_webhook("test_completed", &payload)
        .await?;

    Ok((
        StatusCode::ACCEPTED,
        Json(serde_json::json!({ "queued": true })),
    ))
}

fn verify_secret(headers: &axum::http::HeaderMap) -> Result<()> {
    let Some(secret_hdr) = headers.get("x-webhook-secret") else {
        return Err(Error::Unauthorized("missing_webhook_secret".into()));
    };
    let provided = secret_hdr
        .to_str()
        .map_err(|_| Error::Unauthorized("invalid_secret_header".into()))?;
    let expected = &get_config().webhook_secret;
    if ConstantTimeEq::ct_eq(provided.as_bytes(), expected.as_bytes()).into() {
        Ok(())
    } else {
        Err(Error::Unauthorized("invalid_webhook_secret".into()))
    }
}
