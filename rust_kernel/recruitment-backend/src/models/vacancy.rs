use chrono::{DateTime, Utc};
use rust_decimal::Decimal;
use serde::{Deserialize, Serialize};
use sqlx::FromRow;
use uuid::Uuid;

#[derive(Debug, Clone, Serialize, Deserialize, FromRow)]
pub struct Vacancy {
    pub id: Uuid,
    pub external_id: Option<String>,
    pub title: String,
    pub company: String,
    pub location: String,
    pub employment_type: Option<String>,
    pub salary_from: Option<Decimal>,
    pub salary_to: Option<Decimal>,
    pub currency: Option<String>,
    pub negotiated_salary: bool,
    pub description: Option<String>,
    pub requirements: Option<String>,
    pub responsibilities: Option<String>,
    pub benefits: Option<String>,
    pub apply_url: Option<String>,
    pub contact_email: Option<String>,
    pub contact_phone: Option<String>,
    pub status: String,
    pub published_at: Option<DateTime<Utc>>,
    pub created_at: Option<DateTime<Utc>>,
    pub updated_at: Option<DateTime<Utc>>,
}
