use crate::error::Result;
use crate::models::webhook_log::WebhookLog;
use reqwest::Client;
use serde_json::Value as JsonValue;
use sqlx::{PgPool, Row};
use uuid::Uuid;

#[derive(Clone)]
pub struct NotificationService {
    pool: PgPool,
    client: Client,
    target_url: String,
}

impl NotificationService {
    pub fn new(pool: PgPool, target_url: String) -> Self {
        Self {
            pool,
            client: Client::new(),
            target_url,
        }
    }

    pub async fn enqueue_webhook(
        &self,
        event_type: &str,
        payload: &JsonValue,
    ) -> Result<WebhookLog> {
        let row = sqlx::query_as!(
            WebhookLog,
            r#"
            INSERT INTO webhook_logs (event_type, payload, target_url, status)
            VALUES ($1, $2, $3, 'pending')
            RETURNING 
                id, event_type, payload as "payload: serde_json::Value", target_url,
                http_status, response_body, attempts, max_attempts, next_retry_at, status,
                created_at as "created_at?: _", updated_at as "updated_at?: _"
            "#,
            event_type,
            payload,
            self.target_url
        )
        .fetch_one(&self.pool)
        .await?;
        Ok(row)
    }

    pub async fn deliver_once(&self, log_id: uuid::Uuid) -> Result<()> {
        let log = sqlx::query_as!(
            WebhookLog,
            r#"SELECT id, event_type, payload as "payload: serde_json::Value", target_url, http_status, response_body, attempts, max_attempts, next_retry_at, status, created_at as "created_at?: _", updated_at as "updated_at?: _" FROM webhook_logs WHERE id = $1"#,
            log_id
        )
        .fetch_one(&self.pool)
        .await?;

        let secret = crate::config::get_config().webhook_secret.clone();
        let res = self
            .client
            .post(&log.target_url)
            .header("X-Webhook-Secret", secret)
            .json(&log.payload)
            .send()
            .await;
        match res {
            Ok(resp) => {
                let status = resp.status().as_u16() as i32;
                let body = resp.text().await.unwrap_or_default();
                sqlx::query!(
                    r#"UPDATE webhook_logs SET http_status = $1, response_body = $2, status = CASE WHEN $1 BETWEEN 200 AND 299 THEN 'success' ELSE 'failed' END, attempts = COALESCE(attempts,0) + 1, updated_at = NOW() WHERE id = $3"#,
                    status,
                    body,
                    log.id
                )
                .execute(&self.pool)
                .await?;
            }
            Err(err) => {
                sqlx::query!(
                    r#"UPDATE webhook_logs SET response_body = $1, status = 'failed', attempts = COALESCE(attempts,0) + 1, updated_at = NOW() WHERE id = $2"#,
                    format!("{}", err),
                    log.id
                )
                .execute(&self.pool)
                .await?;
            }
        }
        Ok(())
    }

    /// Picks one pending webhook (if any) and delivers it.
    /// Returns true if a webhook was processed (success or fail), false if queue was empty.
    pub async fn run_once(&self) -> Result<bool> {
        // Find one pending item ready to send
        let row_opt = sqlx::query(
            r#"SELECT id FROM webhook_logs 
               WHERE status = 'pending' AND (next_retry_at IS NULL OR next_retry_at <= NOW())
               ORDER BY created_at ASC 
               FOR UPDATE SKIP LOCKED
               LIMIT 1"#,
        )
        .fetch_optional(&self.pool)
        .await?;

        let Some(row) = row_opt else { return Ok(false) };
        let id: Uuid = row.try_get("id")?;

        // Attempt delivery
        let _ = self.deliver_once(id).await;

        // Schedule next retry if failed
        let row2 =
            sqlx::query(r#"SELECT attempts, max_attempts, status FROM webhook_logs WHERE id = $1"#)
                .bind(id)
                .fetch_one(&self.pool)
                .await?;
        let attempts: i32 = row2.try_get("attempts")?;
        let max_attempts: i32 = row2.try_get::<Option<i32>, _>("max_attempts")?.unwrap_or(3);
        let status: String = row2.try_get("status")?;

        if status == "failed" && attempts < max_attempts {
            // Exponential backoff: min(3600, 30 * 2^(attempts-1)) seconds
            sqlx::query(
                r#"UPDATE webhook_logs 
                   SET next_retry_at = NOW() + make_interval(secs => LEAST(3600, 30 * power(2::float, GREATEST(0, attempts-1))::int))
                   WHERE id = $1"#,
            )
            .bind(id)
            .execute(&self.pool)
            .await?;
        }

        Ok(true)
    }
}
