use crate::config::get_config;
use crate::error::Result;
use sqlx::{postgres::PgPoolOptions, PgPool};

/// Creates a new database connection pool.
///
/// The pool is configured with a maximum of 10 connections and uses the
/// DATABASE_URL from the application's configuration.
pub async fn create_pool() -> Result<PgPool> {
    let config = get_config();
    let pool = PgPoolOptions::new()
        .max_connections(10)
        .connect(&config.database_url)
        .await?;
    Ok(pool)
}
