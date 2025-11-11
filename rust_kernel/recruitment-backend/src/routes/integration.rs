use crate::{
    dto::integration_dto::{
        CreateTestPayload, EnqueueAiJobPayload, GenerateAiTestPayload,
        GenerateVacancyDescriptionPayload, UpdateTestPayload,
    },
    error::Result,
    middleware::auth::Claims,
    AppState,
};
use axum::{
    extract::{Path, Query, State},
    http::StatusCode,
    response::IntoResponse,
    Extension, Json,
};
use serde_json::{json, Value as JsonValue};
use sqlx::Row;
use std::time::Duration;
use uuid::Uuid;
use validator::Validate;

#[axum::debug_handler]
pub async fn create_test(
    State(state): State<AppState>,
    Json(payload): Json<CreateTestPayload>,
) -> Result<impl IntoResponse> {
    payload.validate()?;

    // Placeholder for auth - in a real app, this would come from a JWT
    let created_by = Uuid::parse_str("2cd84131-6e83-4c98-91ba-f9b9a5f0a06c").unwrap();

    let test = state.test_service.create_test(payload, created_by).await?;

    let response = json!({
        "id": test.id,
        "external_id": test.external_id,
        "title": test.title,
        "created_at": test.created_at,
    });

    Ok((StatusCode::CREATED, Json(response)))
}

pub async fn get_test_by_id(
    State(state): State<AppState>,
    axum::extract::Path(test_id): axum::extract::Path<Uuid>,
) -> Result<impl IntoResponse> {
    let test = state.test_service.get_test_by_id(test_id).await?;
    Ok(Json(test))
}

#[axum::debug_handler]
pub async fn update_test(
    State(state): State<AppState>,
    Path(id): Path<Uuid>,
    Json(payload): Json<UpdateTestPayload>,
) -> Result<impl IntoResponse> {
    payload.validate()?;
    let test = state.test_service.update_test(id, payload).await?;
    let response = json!({
        "status": "success",
        "test": test,
    });
    Ok(Json(response))
}

#[derive(Debug, serde::Deserialize, Default)]
#[serde(default)]
pub struct ListTestsQuery {
    pub page: Option<i64>,
    pub per_page: Option<i64>,
    pub is_active: Option<bool>,
    pub search: Option<String>,
}

pub async fn list_tests(
    State(state): State<AppState>,
    axum::extract::Query(query): axum::extract::Query<ListTestsQuery>,
) -> Result<impl IntoResponse> {
    let page = query.page.unwrap_or(1);
    let per_page = query.per_page.unwrap_or(10).clamp(1, 100); // Limit to 100 items per page

    let filter = crate::services::test_service::TestFilter {
        is_active: query.is_active,
        created_by: None, // In a real app, you might want to filter by the current user
        search: query.search,
    };

    let result = state
        .test_service
        .list_tests(page, per_page, Some(filter))
        .await?;
    Ok(Json(result))
}

pub async fn delete_test(
    State(state): State<AppState>,
    Path(id): Path<Uuid>,
) -> Result<impl IntoResponse> {
    state.test_service.delete_test(id).await?;
    Ok(StatusCode::NO_CONTENT)
}

#[derive(Debug, serde::Deserialize)]
pub struct CreateInviteRequest {
    pub test_id: Uuid,
    pub candidate: InviteCandidateDto,
    pub expires_in_hours: i64,
    pub send_notification: Option<bool>,
    pub notification_method: Option<String>,
    pub metadata: Option<serde_json::Value>,
}

#[derive(Debug, serde::Deserialize)]
pub struct InviteCandidateDto {
    pub external_id: Option<String>,
    pub name: String,
    pub email: String,
    pub telegram_id: Option<i64>,
    pub phone: Option<String>,
}

#[axum::debug_handler]
pub async fn create_test_invite(
    State(state): State<AppState>,
    Json(payload): Json<CreateInviteRequest>,
) -> Result<impl IntoResponse> {
    let svc = crate::services::attempt_service::AttemptService::new(state.pool.clone());
    let candidate_name = payload.candidate.name.clone();
    let result = svc
        .create_invite(
            payload.test_id,
            crate::services::attempt_service::InviteCandidate {
                external_id: payload.candidate.external_id,
                name: candidate_name.clone(),
                email: payload.candidate.email,
                telegram_id: payload.candidate.telegram_id,
                phone: payload.candidate.phone,
            },
            payload.expires_in_hours,
            payload.metadata,
        )
        .await?;

    // Fetch test for webhook payload
    let test = state.test_service.get_test_by_id(payload.test_id).await?;

    // Enqueue webhook notification (test_assigned)
    let notif = crate::services::notification_service::NotificationService::new(
        state.pool.clone(),
        crate::config::get_config().telegram_bot_webhook_url.clone(),
    );
    let assigned = crate::dto::webhook_dto::TestAssignedWebhook {
        event: "test_assigned".to_string(),
        attempt_id: result.attempt_id,
        candidate: crate::dto::webhook_dto::WebhookCandidate {
            name: candidate_name,
            telegram_id: payload.candidate.telegram_id,
        },
        test: crate::dto::webhook_dto::WebhookTest {
            title: test.title.clone(),
        },
        access_token: result.access_token.clone(),
        expires_at: result.expires_at,
    };
    let payload_json = serde_json::to_value(&assigned)?;
    let _ = notif
        .enqueue_webhook("test_assigned", &payload_json)
        .await?;

    // Audit log
    let audit = crate::services::audit_service::AuditService::new(state.pool.clone());
    let _ = audit
        .log(
            None,
            "create_invite",
            "test_attempt",
            result.attempt_id,
            Some(serde_json::json!({"test_id": payload.test_id})),
            None,
            None,
        )
        .await?;

    let response = json!({
        "attempt_id": result.attempt_id,
        "access_token": result.access_token,
        "test_url": format!("https://t.me/YourBot/app?startapp=test_{}", result.access_token),
        "expires_at": result.expires_at,
        "status": result.status,
    });
    Ok((StatusCode::CREATED, Json(response)))
}

pub async fn get_test_attempt_by_id(
    State(state): State<AppState>,
    axum::extract::Path(attempt_id): axum::extract::Path<Uuid>,
) -> Result<impl IntoResponse> {
    let svc = crate::services::attempt_service::AttemptService::new(state.pool.clone());
    let attempt = svc.get_attempt_by_id(attempt_id).await?;
    let test = state.test_service.get_test_by_id(attempt.test_id).await?;
    let resp = serde_json::json!({
        "id": attempt.id,
        "test": {
            "id": test.id,
            "title": test.title,
        },
        "candidate": {
            "external_id": attempt.candidate_external_id,
            "name": attempt.candidate_name,
            "email": attempt.candidate_email,
        },
        "status": attempt.status,
        "score": attempt.score,
        "max_score": attempt.max_score,
        "percentage": attempt.percentage,
        "passed": attempt.passed,
        "started_at": attempt.started_at,
        "completed_at": attempt.completed_at,
        "time_spent_seconds": attempt.time_spent_seconds,
        "graded_answers": attempt.graded_answers,
        "metadata": attempt.metadata,
    });
    Ok(Json(resp))
}

#[derive(Debug, serde::Deserialize, Default)]
#[serde(default)]
pub struct ListAttemptsQuery {
    pub test_id: Option<Uuid>,
    pub candidate_email: Option<String>,
    pub status: Option<String>,
    pub page: Option<i64>,
    pub limit: Option<i64>,
}

pub async fn list_test_attempts(
    State(state): State<AppState>,
    Query(q): Query<ListAttemptsQuery>,
) -> Result<impl IntoResponse> {
    let page = q.page.unwrap_or(1);
    let limit = q.limit.unwrap_or(20).clamp(1, 100);
    let svc = crate::services::attempt_service::AttemptService::new(state.pool.clone());
    let (items, total) = svc
        .list_attempts(q.test_id, q.candidate_email, q.status, page, limit)
        .await?;
    let total_pages = ((total as f64) / (limit as f64)).ceil() as i64;
    let resp = serde_json::json!({
        "items": items,
        "total": total,
        "page": page,
        "limit": limit,
        "total_pages": total_pages,
    });
    Ok(Json(resp))
}

#[axum::debug_handler]
pub async fn generate_ai_test(
    State(state): State<AppState>,
    Extension(_claims): Extension<Claims>,
    Json(payload): Json<GenerateAiTestPayload>,
) -> Result<impl IntoResponse> {
    let cfg = crate::config::get_config();
    let num_q = payload.num_questions.unwrap_or(6).min(cfg.max_ai_questions);
    let skills: Vec<String> = payload.skills.clone().unwrap_or_default();

    let ai_future = state.ai_service.generate_test(
        &state.embed_service,
        &state.eval_service,
        &payload.profession,
        &skills,
        num_q,
    );

    let gen_output = match tokio::time::timeout(Duration::from_secs(60), ai_future).await {
        Ok(Ok(val)) => val,
        _ => {
            tracing::warn!("AI generation failed or timed out");
            crate::services::ai_service::GenerationOutput {
                questions: vec![],
                logs: vec!["Timeout or fatal error in generate_test".to_string()],
            }
        }
    };
    let questions_val = serde_json::to_value(&gen_output.questions)?;

    if payload.persist.unwrap_or(false) {
        let create_questions = state.ai_service.to_create_questions(&gen_output.questions);
        let created_by = Uuid::new_v4(); // Placeholder

        let test_payload = CreateTestPayload {
            title: payload
                .title
                .unwrap_or_else(|| format!("AI {} Test", payload.profession)),
            external_id: None,
            description: payload.description,
            instructions: None,
            questions: create_questions,
            duration_minutes: payload.duration_minutes.unwrap_or(45),
            passing_score: payload.passing_score.unwrap_or(70.0),
            shuffle_questions: Some(false),
            shuffle_options: Some(false),
            show_results_immediately: Some(false),
        };

        let test = state
            .test_service
            .create_test(test_payload, created_by)
            .await?;
        Ok((
            StatusCode::OK,
            Json(serde_json::json!({ "questions": questions_val, "test_id": test.id })),
        )
            .into_response())
    } else {
        Ok((
            StatusCode::OK,
            Json(serde_json::json!({ "questions": questions_val })),
        )
            .into_response())
    }
}

#[axum::debug_handler]
pub async fn generate_vacancy_description(
    State(state): State<AppState>,
    Json(payload): Json<GenerateVacancyDescriptionPayload>,
) -> Result<impl IntoResponse> {
    payload.validate()?;
    let description = state
        .ai_service
        .generate_vacancy_description(&payload)
        .await?;
    Ok(Json(serde_json::json!({ "description": description })))
}

/// Enqueue an AI test generation job
#[utoipa::path(
    post,
    path = "/api/integration/ai-jobs",
    request_body = EnqueueAiJobPayload,
    responses(
        (status = 202, description = "AI job enqueued successfully", body = Json<serde_json::Value>),
        (status = 400, description = "Invalid request payload"),
        (status = 500, description = "Internal server error"),
    ),
)]
pub async fn enqueue_ai_job(
    State(state): State<AppState>,
    Extension(claims): Extension<Claims>,
    Json(payload): Json<EnqueueAiJobPayload>,
) -> Result<impl IntoResponse> {
    let cfg = crate::config::get_config();
    let num_q = payload.num_questions.unwrap_or(6).min(cfg.max_ai_questions);
    let queue = crate::services::queue_service::AiQueueService::new(state.pool.clone());
    let job_payload: JsonValue = serde_json::json!({
        "profession": payload.profession,
        "cv_summary": payload.cv_summary.unwrap_or_default(),
        "skills": payload.skills.unwrap_or_default(),
        "num_questions": num_q,
        "created_by_sub": claims.sub,
        "created_by_role": claims.role,
    });
    let id = queue
        .enqueue(
            job_payload,
            payload.persist.unwrap_or(false),
            payload.title,
            payload.description,
            payload.duration_minutes,
            payload.passing_score,
        )
        .await?;
    Ok((
        StatusCode::ACCEPTED,
        Json(serde_json::json!({"job_id": id})),
    ))
}

#[utoipa::path(
    get,
    path = "/api/integration/ai-jobs/{id}",
    params(
        ("id" = Uuid, Path, description = "AI Job ID")
    ),
    responses(
        (status = 200, description = "AI job status retrieved successfully", body = Json<serde_json::Value>),
        (status = 404, description = "Job not found"),
    ),
)]
pub async fn get_ai_job(
    State(state): State<AppState>,
    Path(id): Path<Uuid>,
) -> Result<impl IntoResponse> {
    let queue = crate::services::queue_service::AiQueueService::new(state.pool.clone());
    let job = queue.get(id).await?;
    Ok(Json(job))
}

/// Generate and persist a test using spec payload (position/topics/question_count)
#[axum::debug_handler]
pub async fn generate_test_spec(
    State(state): State<AppState>,
    Extension(claims): Extension<Claims>,
    Json(payload): Json<crate::dto::integration_dto::SpecGenerateTestPayload>,
) -> Result<impl IntoResponse> {
    let cfg = crate::config::get_config();
    let num_q = payload.question_count.min(cfg.max_ai_questions);
    let skills = payload.topics.clone();
    let title = format!("{} Assessment", payload.position);

    // Generate questions (with timeout)
    let ai_future = state.ai_service.generate_test(
        &state.embed_service,
        &state.eval_service,
        &payload.position,
        &skills,
        num_q,
    );
    let gen_output = match tokio::time::timeout(std::time::Duration::from_secs(60), ai_future).await
    {
        Ok(Ok(v)) => v,
        _ => {
            tracing::warn!("AI generation failed or timed out for spec route");
            crate::services::ai_service::GenerationOutput {
                questions: vec![],
                logs: vec!["Timeout or fatal error".to_string()],
            }
        }
    };

    // Persist test
    // resolve created_by by claims.sub (create user if missing) or fallback to system id
    let created_by = {
        let sub = claims.sub.clone();
        if let Some(row) = sqlx::query("SELECT id FROM users WHERE external_id=$1")
            .bind(&sub)
            .fetch_optional(&state.pool)
            .await?
        {
            row.try_get::<Uuid, _>("id")?
        } else {
            let new_id = Uuid::new_v4();
            let _ = sqlx::query(
                r#"INSERT INTO users (id, external_id, name, email, role, is_active)
                   VALUES ($1,$2,$3,$4,$5,true) ON CONFLICT (external_id) DO NOTHING"#,
            )
            .bind(new_id)
            .bind(&sub)
            .bind(format!("{}", &sub))
            .bind(format!("{}@example.com", &sub))
            .bind("admin")
            .execute(&state.pool)
            .await?;
            let row = sqlx::query("SELECT id FROM users WHERE external_id=$1")
                .bind(&sub)
                .fetch_one(&state.pool)
                .await?;
            row.try_get::<Uuid, _>("id")?
        }
    };

    let create_payload = crate::dto::integration_dto::CreateTestPayload {
        title: title.clone(),
        external_id: None,
        description: Some(format!("Generated for position: {}", payload.position)),
        instructions: None,
        questions: state.ai_service.to_create_questions(&gen_output.questions),
        duration_minutes: payload.duration_minutes.unwrap_or(90),
        passing_score: 70.0,
        shuffle_questions: Some(false),
        shuffle_options: Some(false),
        show_results_immediately: Some(false),
    };
    let test = state
        .test_service
        .create_test(create_payload, created_by)
        .await?;

    let resp = json!({
        "id": test.id,
        "title": test.title,
        "questions": test.questions,
        "created_at": test.created_at,
    });
    Ok((StatusCode::CREATED, Json(resp)))
}
