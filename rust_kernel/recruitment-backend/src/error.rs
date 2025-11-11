use axum::{
    http::StatusCode,
    response::{IntoResponse, Json},
};
use serde_json::json;

pub type Result<T> = std::result::Result<T, Error>;

#[derive(Debug, thiserror::Error)]
pub enum Error {
    #[error("Configuration error: {0}")]
    Config(String),

    #[error("Bad request: {0}")]
    BadRequest(String),

    #[error("Unauthorized: {0}")]
    Unauthorized(String),

    #[error("Not found: {0}")]
    NotFound(String),

    #[error("Database error: {0}")]
    Database(sqlx::Error),

    #[error("Validation error: {0}")]
    Validation(#[from] validator::ValidationErrors),

    #[error("JSON error: {0}")]
    Json(#[from] serde_json::Error),

    #[error(transparent)]
    Anyhow(#[from] anyhow::Error),

    #[error("HTTP error: {0}")]
    Reqwest(#[from] reqwest::Error),
}

impl IntoResponse for Error {
    fn into_response(self) -> axum::response::Response {
        let (status, error_message) = match self {
            Error::BadRequest(msg) => (StatusCode::BAD_REQUEST, msg),
            Error::Unauthorized(msg) => (StatusCode::UNAUTHORIZED, msg),
            Error::NotFound(msg) => (StatusCode::NOT_FOUND, msg),
            Error::Validation(err) => (StatusCode::BAD_REQUEST, err.to_string()),
            Error::Database(err) => (StatusCode::INTERNAL_SERVER_ERROR, err.to_string()),
            Error::Json(err) => (StatusCode::BAD_REQUEST, err.to_string()),
            _ => (
                StatusCode::INTERNAL_SERVER_ERROR,
                "An unexpected error occurred".to_string(),
            ),
        };

        let body = Json(json!({ "error": error_message }));
        (status, body).into_response()
    }
}

impl From<sqlx::Error> for Error {
    fn from(err: sqlx::Error) -> Self {
        match err {
            sqlx::Error::RowNotFound => Error::NotFound("Resource not found".to_string()),
            other => Error::Database(other),
        }
    }
}
