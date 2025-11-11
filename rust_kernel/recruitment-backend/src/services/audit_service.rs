use crate::error::Result;
use crate::models::audit_log::AuditLog;
use serde_json::Value as JsonValue;
use sqlx::PgPool;
use uuid::Uuid;

#[derive(Clone)]
pub struct AuditService {
    pool: PgPool,
}

impl AuditService {
    pub fn new(pool: PgPool) -> Self {
        Self { pool }
    }

    pub async fn log(
        &self,
        user_id: Option<Uuid>,
        action: &str,
        entity_type: &str,
        entity_id: Uuid,
        changes: Option<JsonValue>,
        ip: Option<sqlx::types::ipnetwork::IpNetwork>,
        ua: Option<String>,
    ) -> Result<AuditLog> {
        let row = sqlx::query_as!(
            AuditLog,
            r#"
            INSERT INTO audit_logs (user_id, action, entity_type, entity_id, changes, ip_address, user_agent)
            VALUES ($1, $2, $3, $4, $5, $6, $7)
            RETURNING id, user_id, action, entity_type, entity_id, changes as "changes: serde_json::Value", ip_address as "ip_address?: sqlx::types::ipnetwork::IpNetwork", user_agent, created_at as "created_at?: _"
            "#,
            user_id,
            action,
            entity_type,
            entity_id,
            changes,
            ip,
            ua
        )
        .fetch_one(&self.pool)
        .await?;
        Ok(row)
    }
}
