use std::env;

use axum::{
    body::{to_bytes, Body},
    http::{Request, StatusCode},
    routing::{get, post},
    Router,
};
use jsonwebtoken::{encode, EncodingKey, Header};
use serde_json::{json, Value as JsonValue};
use tower::ServiceExt;
use uuid::Uuid;

#[tokio::test]
async fn integration_api_end_to_end() {
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

    let created_by = Uuid::parse_str("2cd84131-6e83-4c98-91ba-f9b9a5f0a06c").unwrap();
    let _ = sqlx::query!(
        r#"INSERT INTO users (id, external_id, name, email, role, is_active)
            VALUES ($1, $2, $3, $4, $5, $6)
            ON CONFLICT (id) DO NOTHING"#,
        created_by,
        format!("ext-{}", created_by),
        "Integration Test User",
        format!("it_{}@example.com", created_by),
        "admin",
        true
    )
    .execute(&pool)
    .await
    .expect("seed user");

    let app_state = recruitment_backend::AppState::new(pool.clone());
    let integration_api = Router::new()
        .route(
            "/api/integration/tests",
            get(recruitment_backend::routes::integration::list_tests)
                .post(recruitment_backend::routes::integration::create_test),
        )
        .route(
            "/api/integration/tests/:id",
            get(recruitment_backend::routes::integration::get_test_by_id)
                .patch(recruitment_backend::routes::integration::update_test)
                .delete(recruitment_backend::routes::integration::delete_test),
        )
        .route(
            "/api/integration/test-invites",
            post(recruitment_backend::routes::integration::create_test_invite),
        )
        .route(
            "/api/integration/test-attempts/:id",
            get(recruitment_backend::routes::integration::get_test_attempt_by_id),
        )
        .route(
            "/api/integration/test-attempts",
            get(recruitment_backend::routes::integration::list_test_attempts),
        )
        .layer(axum::middleware::from_fn(
            recruitment_backend::middleware::auth::require_bearer_auth,
        ))
        .layer(axum::middleware::from_fn_with_state(
            recruitment_backend::middleware::rate_limit::new_rps_state(100),
            recruitment_backend::middleware::rate_limit::rps_middleware,
        ))
        .with_state(app_state.clone());

    let app = integration_api;

    #[derive(serde::Serialize)]
    struct Claims {
        sub: String,
        exp: usize,
        role: Option<String>,
    }
    let exp = (chrono::Utc::now() + chrono::Duration::hours(1)).timestamp() as usize;
    let token = encode(
        &Header::default(),
        &Claims {
            sub: "tester".into(),
            exp,
            role: Some("admin".into()),
        },
        &EncodingKey::from_secret(
            recruitment_backend::config::get_config()
                .jwt_secret
                .as_bytes(),
        ),
    )
    .expect("sign token");
    let auth = format!("Bearer {}", token);

    let create_body = json!({
        "title": "IT Test",
        "external_id": "ext-it-1",
        "description": "Desc",
        "instructions": null,
        "questions": [
            {
                "type": "multiple_choice",
                "question": "2+2?",
                "points": 1,
                "options": ["1","2","3","4"],
                "correct_answer": 3,
                "explanation": null
            }
        ],
        "duration_minutes": 45,
        "passing_score": 70.0,
        "shuffle_questions": false,
        "shuffle_options": false,
        "show_results_immediately": false
    });
    let req = Request::builder()
        .method("POST")
        .uri("/api/integration/tests")
        .header("content-type", "application/json")
        .header("authorization", auth.clone())
        .body(Body::from(create_body.to_string()))
        .unwrap();
    let resp = app.clone().oneshot(req).await.unwrap();
    assert_eq!(resp.status(), StatusCode::CREATED);
    let bytes = to_bytes(resp.into_body(), 1024 * 1024).await.unwrap();
    let created: JsonValue = serde_json::from_slice(&bytes).unwrap();
    let test_id = Uuid::parse_str(created["id"].as_str().unwrap()).unwrap();

    let req = Request::builder()
        .method("GET")
        .uri("/api/integration/tests?page=1&per_page=10")
        .header("authorization", auth.clone())
        .body(Body::empty())
        .unwrap();
    let resp = app.clone().oneshot(req).await.unwrap();
    assert_eq!(resp.status(), StatusCode::OK);

    let invite_body = json!({
        "test_id": test_id,
        "candidate": {"external_id": null, "name": "Alice", "email": "alice@example.com", "telegram_id": null, "phone": null},
        "expires_in_hours": 2,
        "send_notification": false,
        "notification_method": null,
        "metadata": {"source": "it"}
    });
    let req = Request::builder()
        .method("POST")
        .uri("/api/integration/test-invites")
        .header("content-type", "application/json")
        .header("authorization", auth.clone())
        .body(Body::from(invite_body.to_string()))
        .unwrap();
    let resp = app.clone().oneshot(req).await.unwrap();
    assert_eq!(resp.status(), StatusCode::CREATED);
    let bytes = to_bytes(resp.into_body(), 1024 * 1024).await.unwrap();
    let invite: JsonValue = serde_json::from_slice(&bytes).unwrap();
    let attempt_id = Uuid::parse_str(invite["attempt_id"].as_str().unwrap()).unwrap();

    let req = Request::builder()
        .method("GET")
        .uri(format!("/api/integration/test-attempts/{}", attempt_id))
        .header("authorization", auth)
        .body(Body::empty())
        .unwrap();
    let resp = app.clone().oneshot(req).await.unwrap();
    assert_eq!(resp.status(), StatusCode::OK);
}
