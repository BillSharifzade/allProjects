use axum::{extract::State, http::StatusCode, response::IntoResponse, Json};
use validator::Validate;

use crate::{
    error::{Error, Result},
    services::external_vacancy_service::{
        ExternalVacancyDeletePayload,
        ExternalVacancyPayload,
        ExternalVacancyService,
    },
    AppState,
};

#[axum::debug_handler]
pub async fn create_external_vacancy(
    State(state): State<AppState>,
    Json(payload): Json<ExternalVacancyPayload>,
) -> Result<impl IntoResponse> {
    payload.validate()?;
    let service = ExternalVacancyService::new(state.clone());
    let result = service.create_vacancy(payload).await?;

    if result.success {
        Ok((StatusCode::ACCEPTED, Json(result)))
    } else {
        Err(Error::BadRequest(result.message))
    }
}

#[axum::debug_handler]
pub async fn delete_external_vacancy(
    State(state): State<AppState>,
    Json(payload): Json<ExternalVacancyDeletePayload>,
) -> Result<impl IntoResponse> {
    payload.validate()?;
    let service = ExternalVacancyService::new(state);
    let result = service.delete_vacancy(payload).await?;

    if result.success {
        Ok((StatusCode::ACCEPTED, Json(result)))
    } else {
        Err(Error::BadRequest(result.message))
    }
}