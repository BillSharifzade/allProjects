// Declare all application modules
pub mod config;
pub mod database;
pub mod dto;
pub mod error;
pub mod middleware;
pub mod models;
pub mod routes;
pub mod services;
pub mod utils;

use crate::services::{
    ai_service::AIService, embed_service::EmbedService, eval_service::EvalService,
    notification_service::NotificationService, test_service::TestService,
    vacancy_service::VacancyService,
};
use reqwest::Client;
use sqlx::PgPool;

#[derive(Clone)]
pub struct AppState {
    pub pool: PgPool,
    pub test_service: TestService,
    pub ai_service: AIService,
    pub eval_service: EvalService,
    pub embed_service: EmbedService,
    pub notification_service: NotificationService,
    pub vacancy_service: VacancyService,
}

impl AppState {
    pub fn new(pool: PgPool) -> Self {
        let config = crate::config::get_config();
        let http_client = Client::builder()
            .timeout(std::time::Duration::from_secs(60))
            .build()
            .unwrap();

        let test_service = TestService::new(pool.clone());
        let ai_service = AIService::new(config.openai_api_key.clone(), http_client.clone());
        let eval_service = EvalService::new(config.openai_api_key.clone(), http_client.clone());
        let embed_service = EmbedService::new(config.openai_api_key.clone(), http_client);
        let notification_service =
            NotificationService::new(pool.clone(), config.telegram_bot_webhook_url.clone());
        let vacancy_service = VacancyService::new(pool.clone());

        Self {
            pool,
            test_service,
            ai_service,
            eval_service,
            embed_service,
            notification_service,
            vacancy_service,
        }
    }
}
