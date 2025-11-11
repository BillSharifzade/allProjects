use crate::error::{Error, Result};
use dotenvy::dotenv;
use std::env;
use std::sync::OnceLock;

#[derive(Debug, Clone)]
pub struct Config {
    pub server_address: String,
    pub database_url: String,
    pub jwt_secret: String,
    pub webhook_secret: String,
    pub openai_api_key: String,
    pub telegram_bot_webhook_url: String,
    pub integration_rps: u32,
    pub public_rps: u32,
    pub max_ai_questions: usize,
}

// Global static instance of the config
pub static CONFIG: OnceLock<Config> = OnceLock::new();

impl Config {
    pub fn from_env() -> Result<Self> {
        dotenv().ok();

        Ok(Self {
            server_address: get_env("SERVER_ADDRESS")?,
            database_url: get_env("DATABASE_URL")?,
            jwt_secret: get_env("JWT_SECRET")?,
            webhook_secret: get_env("WEBHOOK_SECRET")?,
            openai_api_key: get_env("OPENAI_API_KEY")?,
            telegram_bot_webhook_url: get_env("TELEGRAM_BOT_WEBHOOK_URL")?,
            integration_rps: get_env_parse("INTEGRATION_RPS")?,
            public_rps: get_env_parse("PUBLIC_RPS")?,
            max_ai_questions: get_env_parse("MAX_AI_QUESTIONS")?,
        })
    }
}

fn get_env(name: &str) -> Result<String> {
    env::var(name).map_err(|_| Error::Config(format!("Missing environment variable: {}", name)))
}

fn get_env_parse<T>(name: &str) -> Result<T>
where
    T: std::str::FromStr,
    T::Err: std::fmt::Display,
{
    let raw = get_env(name)?;
    raw.parse()
        .map_err(|e| Error::Config(format!("Invalid value for {}: {}", name, e)))
}

/// Initializes the global CONFIG static.
/// This must be called once at the beginning of the application.
pub fn init_config() -> Result<()> {
    let config = Config::from_env()?;
    CONFIG
        .set(config)
        .map_err(|_| Error::Config("Configuration has already been initialized".to_string()))?;
    Ok(())
}

/// Returns a reference to the global config.
/// Panics if the config has not been initialized.
pub fn get_config() -> &'static Config {
    CONFIG
        .get()
        .expect("Configuration has not been initialized")
}
