use axum::{
    routing::{get, post},
    Router,
};
use recruitment_backend::services::queue_service::AiQueueService;
use recruitment_backend::{
    config::{get_config, init_config},
    database::pool::create_pool,
    routes, AppState,
};
use std::net::SocketAddr;
use std::time::Duration;
use tokio::net::TcpListener;
use tower_http::{cors::CorsLayer, trace::TraceLayer};
use tracing::info;

#[tokio::main]
async fn main() -> anyhow::Result<()> {
    tracing_subscriber::fmt::init();
    init_config()?;
    let config = get_config();

    let pool = create_pool().await?;
    sqlx::migrate!("./migrations").run(&pool).await?;

    let app_state = AppState::new(pool);

    {
        let state = app_state.clone();
        tokio::spawn(async move {
            let queue = AiQueueService::new(state.pool.clone());
            loop {
                match queue.run_once(&state).await {
                    Ok(true) => {
                    }
                    Ok(false) => {
                        tokio::time::sleep(Duration::from_millis(750)).await;
                    }
                    Err(e) => {
                        tracing::error!(error = ?e, "AI queue worker error");
                        tokio::time::sleep(Duration::from_secs(1)).await;
                    }
                }
            }
        });
    }

    {
        let state = app_state.clone();
        tokio::spawn(async move {
            let notif =
                recruitment_backend::services::notification_service::NotificationService::new(
                    state.pool.clone(),
                    recruitment_backend::config::get_config()
                        .telegram_bot_webhook_url
                        .clone(),
                );
            loop {
                match notif.run_once().await {
                    Ok(true) => { /* processed one, loop again */ }
                    Ok(false) => {
                        tokio::time::sleep(Duration::from_millis(1000)).await;
                    }
                    Err(e) => {
                        tracing::error!(error = ?e, "Webhook worker error");
                        tokio::time::sleep(Duration::from_secs(2)).await;
                    }
                }
            }
        });
    }

    let base_routes = Router::new().route("/health", get(routes::health::health));

    let integration_api = Router::new()
        .route(
            "/api/integration/tests",
            get(routes::integration::list_tests).post(routes::integration::create_test),
        )
        .route(
            "/api/integration/tests/:id",
            get(routes::integration::get_test_by_id)
                .patch(routes::integration::update_test)
                .delete(routes::integration::delete_test),
        )
        .route(
            "/api/integration/test-invites",
            post(routes::integration::create_test_invite),
        )
        .route(
            "/api/integration/tests/generate",
            post(routes::integration::generate_test_spec),
        )
        .route(
            "/api/integration/tests/generate-ai",
            post(routes::integration::generate_ai_test),
        )
        .route(
            "/api/integration/vacancies/description",
            post(routes::integration::generate_vacancy_description),
        )
        .route(
            "/api/integration/vacancies",
            get(routes::vacancy::list_vacancies).post(routes::vacancy::create_vacancy),
        )
        .route(
            "/api/integration/vacancies/external",
            post(routes::external_vacancy::create_external_vacancy),
        )
        .route(
            "/api/integration/vacancies/external/delete",
            post(routes::external_vacancy::delete_external_vacancy),
        )
        .route(
            "/api/integration/vacancies/:id",
            get(routes::vacancy::get_vacancy)
                .patch(routes::vacancy::update_vacancy)
                .delete(routes::vacancy::delete_vacancy),
        )
        .route(
            "/api/integration/ai-jobs",
            post(routes::integration::enqueue_ai_job),
        )
        .route(
            "/api/integration/ai-jobs/:id",
            get(routes::integration::get_ai_job),
        )
        .route(
            "/api/integration/test-attempts/:id",
            get(routes::integration::get_test_attempt_by_id),
        )
        .route(
            "/api/integration/test-attempts",
            get(routes::integration::list_test_attempts),
        )
        .layer(axum::middleware::from_fn_with_state(
            recruitment_backend::middleware::rate_limit::new_rps_state(config.integration_rps),
            recruitment_backend::middleware::rate_limit::rps_middleware,
        ));

    let public_api = Router::new()
        .route(
            "/api/public/tests/:token",
            get(routes::public::get_test_by_token),
        )
        .route(
            "/api/public/tests/:token/start",
            post(routes::public::start_test),
        )
        .route(
            "/api/public/tests/:token/answer",
            axum::routing::patch(routes::public::save_answer),
        )
        .route(
            "/api/public/tests/:token/submit",
            post(routes::public::submit_test),
        )
        .route(
            "/api/public/tests/:token/status",
            get(routes::public::get_status),
        )
        .route(
            "/api/public/vacancies",
            get(routes::vacancy::list_public_vacancies),
        )
        .route(
            "/api/public/vacancies/:id",
            get(routes::vacancy::get_public_vacancy),
        )
        .layer(axum::middleware::from_fn_with_state(
            recruitment_backend::middleware::rate_limit::new_rps_state(config.public_rps),
            recruitment_backend::middleware::rate_limit::rps_middleware,
        ));

    let app = base_routes
        .merge(integration_api)
        .merge(public_api)
        .with_state(app_state)
        .layer(CorsLayer::permissive())
        .layer(TraceLayer::new_for_http());

    let addr: SocketAddr = config.server_address.parse()?;
    info!("Server listening on {}", addr);
    let listener = TcpListener::bind(addr).await?;
    axum::serve(listener, app).await?;

    Ok(())
}
