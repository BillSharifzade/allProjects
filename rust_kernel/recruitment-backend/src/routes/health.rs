use axum::{http::StatusCode, response::IntoResponse, Json};
use serde_json::json;

#[axum::debug_handler]
pub async fn health() -> impl IntoResponse {
    let body = json!({
        "status": "ok",
    });
    (StatusCode::OK, Json(body))
}
