use axum::{
    extract::{Path, Query, State},
    http::StatusCode,
    response::{IntoResponse, Json},
};
use uuid::Uuid;
use validator::Validate;

use crate::{
    dto::vacancy_dto::{
        CreateVacancyPayload, UpdateVacancyPayload, VacancyListQuery, VacancyListResponse,
        VacancyPublicListResponse, VacancyPublicQuery, VacancyPublicSummary, VacancyResponse,
    },
    error::Result,
    AppState,
};

#[axum::debug_handler]
pub async fn create_vacancy(
    State(state): State<AppState>,
    Json(payload): Json<CreateVacancyPayload>,
) -> Result<impl IntoResponse> {
    payload.validate()?;
    let vacancy = state.vacancy_service.create(payload).await?;
    Ok((StatusCode::CREATED, Json(VacancyResponse::from(vacancy))))
}

#[axum::debug_handler]
pub async fn update_vacancy(
    State(state): State<AppState>,
    Path(id): Path<Uuid>,
    Json(payload): Json<UpdateVacancyPayload>,
) -> Result<impl IntoResponse> {
    payload.validate()?;
    let vacancy = state.vacancy_service.update(id, payload).await?;
    Ok(Json(VacancyResponse::from(vacancy)))
}

#[axum::debug_handler]
pub async fn delete_vacancy(
    State(state): State<AppState>,
    Path(id): Path<Uuid>,
) -> Result<impl IntoResponse> {
    state.vacancy_service.delete(id).await?;
    Ok(StatusCode::NO_CONTENT)
}

#[axum::debug_handler]
pub async fn list_vacancies(
    State(state): State<AppState>,
    Query(query): Query<VacancyListQuery>,
) -> Result<impl IntoResponse> {
    let result = state.vacancy_service.list(query).await?;
    Ok(Json(VacancyListResponse::from(result)))
}

#[axum::debug_handler]
pub async fn get_vacancy(
    State(state): State<AppState>,
    Path(id): Path<Uuid>,
) -> Result<impl IntoResponse> {
    let vacancy = state.vacancy_service.get_by_id(id).await?;
    Ok(Json(VacancyResponse::from(vacancy)))
}

#[axum::debug_handler]
pub async fn list_public_vacancies(
    State(state): State<AppState>,
    Query(query): Query<VacancyPublicQuery>,
) -> Result<impl IntoResponse> {
    let limit = query.limit.unwrap_or(20).min(100);
    let items = state.vacancy_service.list_published(limit).await?;
    let summaries: Vec<VacancyPublicSummary> = items.into_iter().map(Into::into).collect();
    Ok(Json(VacancyPublicListResponse { items: summaries }))
}

#[axum::debug_handler]
pub async fn get_public_vacancy(
    State(state): State<AppState>,
    Path(id): Path<Uuid>,
) -> Result<impl IntoResponse> {
    let vacancy = state.vacancy_service.get_by_id(id).await?;
    if vacancy.status != "published" {
        return Err(crate::error::Error::Unauthorized(
            "Vacancy not published".into(),
        ));
    }
    Ok(Json(VacancyResponse::from(vacancy)))
}
