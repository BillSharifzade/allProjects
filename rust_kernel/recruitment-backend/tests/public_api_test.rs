use std::env;

use axum::{
    body::{to_bytes, Body},
    http::{Request, StatusCode},
    routing::{get, patch, post},
    Router,
};
use serde_json::{json, Value as JsonValue};
use tower::ServiceExt;
use uuid::Uuid;

#[tokio::test]
async fn public_flow_end_to_end() {
    dotenvy::dotenv().ok();
    env::set_var("SERVER_ADDRESS", "127.0.0.1:0");
    env::set_var("JWT_SECRET", "test_secret_key");
    env::set_var("WEBHOOK_SECRET", "whsec_test");
    env::set_var("OPENAI_API_KEY", "sk-test");
    env::set_var("TELEGRAM_BOT_WEBHOOK_URL", "http://localhost/webhook");
    env::set_var("PUBLIC_RPS", "100");
    env::set_var("INTEGRATION_RPS", "100");

    recruitment_backend::config::init_config().expect("init config");
    let pool = recruitment_backend::database::pool::create_pool()
        .await
        .expect("pool");
    sqlx::migrate!("./migrations")
        .run(&pool)
        .await
        .expect("migrations");

    let creator = Uuid::new_v4();
    let _ = sqlx::query!(
        r#"INSERT INTO users (id, external_id, name, email, role, is_active)
           VALUES ($1, $2, $3, $4, $5, $6)"#,
        creator,
        format!("ext-{}", creator),
        "Pub User",
        format!("pub_{}@example.com", creator),
        "hr",
        true
    )
    .execute(&pool)
    .await
    .expect("seed user");

    let test_service = recruitment_backend::services::test_service::TestService::new(pool.clone());
    let test = test_service
        .create_test(
            recruitment_backend::dto::integration_dto::CreateTestPayload {
                title: "Public Test".into(),
                external_id: Some("pub-ext".into()),
                description: Some("Desc".into()),
                instructions: None,
                questions: vec![recruitment_backend::dto::integration_dto::CreateQuestion {
                    question_type:
                        recruitment_backend::models::question::QuestionType::MultipleChoice,
                    question: "2+2?".into(),
                    points: 1,
                    details: recruitment_backend::models::question::QuestionDetails::MultipleChoice(
                        recruitment_backend::models::question::MultipleChoiceDetails {
                            options: vec!["1".into(), "2".into(), "3".into(), "4".into()],
                            correct_answer: 3,
                            explanation: None,
                        },
                    ),
                }],
                duration_minutes: 10,
                passing_score: 50.0,
                shuffle_questions: Some(false),
                shuffle_options: Some(false),
                show_results_immediately: Some(false),
            },
            creator,
        )
        .await
        .expect("create test");

    let attempt_service =
        recruitment_backend::services::attempt_service::AttemptService::new(pool.clone());
    let invite = attempt_service
        .create_invite(
            test.id,
            recruitment_backend::services::attempt_service::InviteCandidate {
                external_id: None,
                name: "Alice".into(),
                email: "alice@example.com".into(),
                telegram_id: None,
                phone: None,
            },
            2,
            None,
        )
        .await
        .expect("invite");
    let token = invite.access_token;

    // Build public API router
    let app_state = recruitment_backend::AppState::new(pool.clone());
    let public_api = Router::new()
        .route(
            "/api/public/tests/:token",
            get(recruitment_backend::routes::public::get_test_by_token),
        )
        .route(
            "/api/public/tests/:token/start",
            post(recruitment_backend::routes::public::start_test),
        )
        .route(
            "/api/public/tests/:token/answer",
            patch(recruitment_backend::routes::public::save_answer),
        )
        .route(
            "/api/public/tests/:token/submit",
            post(recruitment_backend::routes::public::submit_test),
        )
        .route(
            "/api/public/tests/:token/status",
            get(recruitment_backend::routes::public::get_status),
        )
        .layer(axum::middleware::from_fn_with_state(
            recruitment_backend::middleware::rate_limit::new_rps_state(100),
            recruitment_backend::middleware::rate_limit::rps_middleware,
        ))
        .with_state(app_state);

    let app = public_api;

    // GET test by token
    let req = Request::builder()
        .method("GET")
        .uri(format!("/api/public/tests/{}", token))
        .body(Body::empty())
        .unwrap();
    let resp = app.clone().oneshot(req).await.unwrap();
    assert_eq!(resp.status(), StatusCode::OK);

    // Start test
    let req = Request::builder()
        .method("POST")
        .uri(format!("/api/public/tests/{}/start", token))
        .body(Body::empty())
        .unwrap();
    let resp = app.clone().oneshot(req).await.unwrap();
    assert_eq!(resp.status(), StatusCode::OK);

    // Save answer
    let save_body = json!({
        "question_id": 1,
        "answer": json!({"selected": 3}),
        "time_spent_seconds": 5,
        "marked_for_review": false
    });
    let req = Request::builder()
        .method("PATCH")
        .uri(format!("/api/public/tests/{}/answer", token))
        .header("content-type", "application/json")
        .body(Body::from(save_body.to_string()))
        .unwrap();
    let resp = app.clone().oneshot(req).await.unwrap();
    assert_eq!(resp.status(), StatusCode::OK);

    // Submit test
    let submit_body = json!({
        "answers": [ {"question_id": 1, "answer": {"selected": 3}, "time_spent_seconds": 5} ]
    });
    let req = Request::builder()
        .method("POST")
        .uri(format!("/api/public/tests/{}/submit", token))
        .header("content-type", "application/json")
        .body(Body::from(submit_body.to_string()))
        .unwrap();
    let resp = app.clone().oneshot(req).await.unwrap();
    assert_eq!(resp.status(), StatusCode::OK);
    let bytes = to_bytes(resp.into_body(), 1024 * 1024).await.unwrap();
    let body: JsonValue = serde_json::from_slice(&bytes).unwrap();
    assert!(body["passed"].is_boolean());

    // Status
    let req = Request::builder()
        .method("GET")
        .uri(format!("/api/public/tests/{}/status", token))
        .body(Body::empty())
        .unwrap();
    let resp = app.clone().oneshot(req).await.unwrap();
    assert_eq!(resp.status(), StatusCode::OK);
}
