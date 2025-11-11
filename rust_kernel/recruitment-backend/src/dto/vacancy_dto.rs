use chrono::{DateTime, Utc};
use rust_decimal::Decimal;
use serde::{Deserialize, Serialize};
use validator::Validate;

use crate::models::vacancy::Vacancy;
use crate::services::vacancy_service::VacancyList;

#[derive(Debug, Clone, Serialize, Deserialize, Validate)]
pub struct CreateVacancyPayload {
    pub external_id: Option<String>,
    #[validate(length(min = 1))]
    pub title: String,
    #[validate(length(min = 1))]
    pub company: String,
    #[validate(length(min = 1))]
    pub location: String,
    pub employment_type: Option<String>,
    pub salary_from: Option<Decimal>,
    pub salary_to: Option<Decimal>,
    pub currency: Option<String>,
    #[serde(default)]
    pub negotiated_salary: bool,
    #[validate(length(min = 1))]
    pub description: Option<String>,
    pub requirements: Option<String>,
    pub responsibilities: Option<String>,
    pub benefits: Option<String>,
    pub apply_url: Option<String>,
    pub contact_email: Option<String>,
    pub contact_phone: Option<String>,
    pub status: Option<String>,
    pub published_at: Option<DateTime<Utc>>,
}

#[derive(Debug, Clone, Serialize, Deserialize, Validate)]
pub struct UpdateVacancyPayload {
    pub external_id: Option<String>,
    #[validate(length(min = 1))]
    pub title: Option<String>,
    #[validate(length(min = 1))]
    pub company: Option<String>,
    #[validate(length(min = 1))]
    pub location: Option<String>,
    pub employment_type: Option<String>,
    pub salary_from: Option<Decimal>,
    pub salary_to: Option<Decimal>,
    pub currency: Option<String>,
    pub negotiated_salary: Option<bool>,
    #[validate(length(min = 1))]
    pub description: Option<String>,
    pub requirements: Option<String>,
    pub responsibilities: Option<String>,
    pub benefits: Option<String>,
    pub apply_url: Option<String>,
    pub contact_email: Option<String>,
    pub contact_phone: Option<String>,
    pub status: Option<String>,
    pub published_at: Option<DateTime<Utc>>,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct VacancyResponse {
    pub id: uuid::Uuid,
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

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct VacancyPublicSummary {
    pub id: uuid::Uuid,
    pub title: String,
    pub company: String,
    pub location: String,
    pub employment_type: Option<String>,
    pub salary_from: Option<Decimal>,
    pub salary_to: Option<Decimal>,
    pub currency: Option<String>,
    pub negotiated_salary: bool,
    pub summary: Option<String>,
    pub published_at: Option<DateTime<Utc>>,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct VacancyListResponse {
    pub items: Vec<VacancyResponse>,
    pub total: i64,
    pub page: i64,
    pub per_page: i64,
    pub total_pages: i64,
}

#[derive(Debug, Clone, Serialize, Deserialize)]
pub struct VacancyPublicListResponse {
    pub items: Vec<VacancyPublicSummary>,
}

#[derive(Debug, Clone, Serialize, Deserialize, Default)]
#[serde(default)]
pub struct VacancyListQuery {
    pub page: Option<i64>,
    pub per_page: Option<i64>,
    pub status: Option<String>,
    pub company: Option<String>,
    pub search: Option<String>,
}

#[derive(Debug, Clone, Serialize, Deserialize, Default)]
#[serde(default)]
pub struct VacancyPublicQuery {
    pub limit: Option<i64>,
}

impl From<Vacancy> for VacancyResponse {
    fn from(value: Vacancy) -> Self {
        Self {
            id: value.id,
            external_id: value.external_id,
            title: value.title,
            company: value.company,
            location: value.location,
            employment_type: value.employment_type,
            salary_from: value.salary_from,
            salary_to: value.salary_to,
            currency: value.currency,
            negotiated_salary: value.negotiated_salary,
            description: value.description,
            requirements: value.requirements,
            responsibilities: value.responsibilities,
            benefits: value.benefits,
            apply_url: value.apply_url,
            contact_email: value.contact_email,
            contact_phone: value.contact_phone,
            status: value.status,
            published_at: value.published_at,
            created_at: value.created_at,
            updated_at: value.updated_at,
        }
    }
}

impl From<Vacancy> for VacancyPublicSummary {
    fn from(value: Vacancy) -> Self {
        let summary = value
            .description
            .as_ref()
            .or(value.requirements.as_ref())
            .map(|text| {
                let trimmed = text.trim();
                if trimmed.len() > 320 {
                    format!("{}…", trimmed.chars().take(320).collect::<String>())
                } else {
                    trimmed.to_string()
                }
            });

        Self {
            id: value.id,
            title: value.title,
            company: value.company,
            location: value.location,
            employment_type: value.employment_type,
            salary_from: value.salary_from,
            salary_to: value.salary_to,
            currency: value.currency,
            negotiated_salary: value.negotiated_salary,
            summary,
            published_at: value.published_at,
        }
    }
}

impl From<VacancyList> for VacancyListResponse {
    fn from(value: VacancyList) -> Self {
        Self {
            items: value.items.into_iter().map(Into::into).collect(),
            total: value.total,
            page: value.page,
            per_page: value.per_page,
            total_pages: value.total_pages,
        }
    }
}
