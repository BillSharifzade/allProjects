use std::env;

use axum::{
    body::Body,
    http::{Request, StatusCode},
    routing::post,
    Router,
};
use serde_json::json;
use tower::ServiceExt;
use uuid::Uuid;

async fn setup_app() -> (Router, sqlx::PgPool) {
    dotenvy::dotenv().ok();
    env::set_var("SERVER_ADDRESS", "127.0.0.1:0");
    env::set_var(
        "DATABASE_URL",
        "postgres://postgres:password@localhost:5432/recruitment_db",
    );
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

    let state = recruitment_backend::AppState::new(pool.clone());
    let app = Router::new()
        .route(
            "/webhook/test-assigned",
            post(recruitment_backend::routes::webhook::handle_test_assigned),
        )
        .route(
            "/webhook/test-completed",
            post(recruitment_backend::routes::webhook::handle_test_completed),
        )
        .with_state(state);

    (app, pool)
}

#[tokio::test]
async fn webhook_routes_require_secret_and_enqueue() {
    let (app, pool) = setup_app().await;

    let attempt_id = Uuid::new_v4();
    let body = json!({
        "event": "test_assigned",
        "attempt_id": attempt_id,
        "candidate": { "name": "Alice" },
        "test": { "title": "Test" },
        "access_token": "token",
        "expires_at": chrono::Utc::now(),
    });

    let req_missing = Request::builder()
        .method("POST")
        .uri("/webhook/test-assigned")
        .header("content-type", "application/json")
        .body(Body::from(body.to_string()))
        .unwrap();
    let resp = app.clone().oneshot(req_missing).await.unwrap();
    assert_eq!(resp.status(), StatusCode::UNAUTHORIZED);

    let req = Request::builder()
        .method("POST")
        .uri("/webhook/test-assigned")
        .header("content-type", "application/json")
        .header("x-webhook-secret", "whsec_test")
        .body(Body::from(body.to_string()))
        .unwrap();
    let resp = app.clone().oneshot(req).await.unwrap();
    assert_eq!(resp.status(), StatusCode::ACCEPTED);

    let stored = sqlx::query!(
        r#"SELECT event_type, payload FROM webhook_logs WHERE event_type = $1 ORDER BY created_at DESC LIMIT 1"#,
        "test_assigned"
    )
    .fetch_optional(&pool)
    .await
    .expect("fetch webhook log");

    let record = stored.expect("webhook log not inserted");
    assert_eq!(record.event_type, "test_assigned");
    let payload_json = record.payload;
    let attempt_id_str = attempt_id.to_string();
    assert_eq!(
        payload_json["attempt_id"].as_str(),
        Some(attempt_id_str.as_str())
    );
    assert_eq!(payload_json["candidate"]["name"].as_str(), Some("Alice"));
}
