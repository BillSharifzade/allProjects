use crate::dto::vacancy_dto::{CreateVacancyPayload, UpdateVacancyPayload, VacancyListQuery};
use crate::error::Result;
use crate::models::vacancy::Vacancy;
use sqlx::{postgres::PgQueryResult, PgPool};
use uuid::Uuid;

#[derive(Clone)]
pub struct VacancyService {
    pool: PgPool,
}

pub struct VacancyList {
    pub items: Vec<Vacancy>,
    pub total: i64,
    pub page: i64,
    pub per_page: i64,
    pub total_pages: i64,
}

impl VacancyService {
    pub fn new(pool: PgPool) -> Self {
        Self { pool }
    }

    pub async fn create(&self, payload: CreateVacancyPayload) -> Result<Vacancy> {
        let status = payload
            .status
            .clone()
            .unwrap_or_else(|| "draft".to_string());
        let vacancy = sqlx::query_as!(
            Vacancy,
            r#"
            INSERT INTO vacancies (
                external_id, title, company, location, employment_type,
                salary_from, salary_to, currency, negotiated_salary, description, requirements,
                responsibilities, benefits, apply_url, contact_email, contact_phone,
                status, published_at
            ) VALUES (
                $1,$2,$3,$4,$5,
                $6,$7,$8,$9,$10,
                $11,$12,$13,$14,$15,
                $16,$17,$18
            )
            RETURNING
                id,
                external_id,
                title,
                company,
                location,
                employment_type,
                salary_from,
                salary_to,
                currency,
                negotiated_salary,
                description,
                requirements,
                responsibilities,
                benefits,
                apply_url,
                contact_email,
                contact_phone,
                status,
                published_at,
                created_at,
                updated_at
            "#,
            payload.external_id,
            payload.title,
            payload.company,
            payload.location,
            payload.employment_type,
            payload.salary_from,
            payload.salary_to,
            payload.currency,
            payload.negotiated_salary,
            payload.description,
            payload.requirements,
            payload.responsibilities,
            payload.benefits,
            payload.apply_url,
            payload.contact_email,
            payload.contact_phone,
            status,
            payload.published_at,
        )
        .fetch_one(&self.pool)
        .await?;

        Ok(vacancy)
    }

    pub async fn update(&self, id: Uuid, payload: UpdateVacancyPayload) -> Result<Vacancy> {
        self.get_by_id(id).await?;

        let vacancy = sqlx::query_as!(
            Vacancy,
            r#"
            UPDATE vacancies
            SET
                external_id = COALESCE($2, external_id),
                title = COALESCE($3, title),
                company = COALESCE($4, company),
                location = COALESCE($5, location),
                employment_type = COALESCE($6, employment_type),
                salary_from = COALESCE($7, salary_from),
                salary_to = COALESCE($8, salary_to),
                currency = COALESCE($9, currency),
                negotiated_salary = COALESCE($10, negotiated_salary),
                description = COALESCE($11, description),
                requirements = COALESCE($12, requirements),
                responsibilities = COALESCE($13, responsibilities),
                benefits = COALESCE($14, benefits),
                apply_url = COALESCE($15, apply_url),
                contact_email = COALESCE($16, contact_email),
                contact_phone = COALESCE($17, contact_phone),
                status = COALESCE($18, status),
                published_at = COALESCE($19, published_at),
                updated_at = NOW()
            WHERE id = $1
            RETURNING
                id,
                external_id,
                title,
                company,
                location,
                employment_type,
                salary_from,
                salary_to,
                currency,
                negotiated_salary,
                description,
                requirements,
                responsibilities,
                benefits,
                apply_url,
                contact_email,
                contact_phone,
                status,
                published_at,
                created_at,
                updated_at
            "#,
            id,
            payload.external_id,
            payload.title,
            payload.company,
            payload.location,
            payload.employment_type,
            payload.salary_from,
            payload.salary_to,
            payload.currency,
            payload.negotiated_salary,
            payload.description,
            payload.requirements,
            payload.responsibilities,
            payload.benefits,
            payload.apply_url,
            payload.contact_email,
            payload.contact_phone,
            payload.status,
            payload.published_at,
        )
        .fetch_one(&self.pool)
        .await?;

        Ok(vacancy)
    }

    pub async fn list(&self, query: VacancyListQuery) -> Result<VacancyList> {
        let page = query.page.unwrap_or(1).max(1);
        let per_page = query.per_page.unwrap_or(20).clamp(1, 100);
        let offset = (page - 1) * per_page;

        let mut filters = Vec::new();
        let mut args: Vec<String> = Vec::new();

        if let Some(status) = query.status {
            filters.push(format!("status = ${}", args.len() + 1));
            args.push(status);
        }
        if let Some(company) = query.company {
            filters.push(format!("company ILIKE ${}", args.len() + 1));
            args.push(format!("%{}%", company));
        }
        if let Some(search) = query.search {
            let first = args.len() + 1;
            let second = first + 1;
            filters.push(format!(
                "(title ILIKE ${} OR location ILIKE ${})",
                first, second
            ));
            args.push(format!("%{}%", search.clone()));
            args.push(format!("%{}%", search));
        }

        let where_clause = if filters.is_empty() {
            "".to_string()
        } else {
            format!("WHERE {}", filters.join(" AND "))
        };

        let items_query = format!(
            "SELECT id, external_id, title, company, location, employment_type, salary_from, salary_to, currency, negotiated_salary, description, requirements, responsibilities, benefits, apply_url, contact_email, contact_phone, status, published_at, created_at, updated_at
             FROM vacancies
             {}
             ORDER BY COALESCE(published_at, created_at) DESC
             LIMIT ${} OFFSET ${}",
            where_clause,
            args.len() + 1,
            args.len() + 2
        );

        let total_query = format!("SELECT COUNT(*) FROM vacancies {}", where_clause);

        let mut items_statement = sqlx::query_as::<_, Vacancy>(&items_query);
        for value in &args {
            items_statement = items_statement.bind(value);
        }
        items_statement = items_statement.bind(per_page).bind(offset);
        let items = items_statement.fetch_all(&self.pool).await?;

        let mut total_statement = sqlx::query_scalar::<_, i64>(&total_query);
        for value in &args {
            total_statement = total_statement.bind(value);
        }
        let total = total_statement.fetch_one(&self.pool).await?;

        let total_pages = ((total as f64) / (per_page as f64)).ceil() as i64;

        Ok(VacancyList {
            items,
            total,
            page,
            per_page,
            total_pages,
        })
    }

    pub async fn get_by_id(&self, id: Uuid) -> Result<Vacancy> {
        let vacancy = sqlx::query_as!(
            Vacancy,
            r#"
            SELECT id, external_id, title, company, location, employment_type, salary_from, salary_to, currency, negotiated_salary, description, requirements, responsibilities, benefits, apply_url, contact_email, contact_phone, status, published_at, created_at, updated_at
            FROM vacancies
            WHERE id = $1
            "#,
            id
        )
        .fetch_one(&self.pool)
        .await?;

        Ok(vacancy)
    }

    pub async fn delete(&self, id: Uuid) -> Result<PgQueryResult> {
        let res = sqlx::query!("DELETE FROM vacancies WHERE id = $1", id)
            .execute(&self.pool)
            .await?;

        Ok(res)
    }

    pub async fn list_published(&self, limit: i64) -> Result<Vec<Vacancy>> {
        let limit = if limit <= 0 { 20 } else { limit.min(100) };
        let items = sqlx::query_as!(
            Vacancy,
            r#"
            SELECT id, external_id, title, company, location, employment_type, salary_from, salary_to, currency, negotiated_salary, description, requirements, responsibilities, benefits, apply_url, contact_email, contact_phone, status, published_at, created_at, updated_at
            FROM vacancies
            WHERE status = 'published'
            ORDER BY COALESCE(published_at, created_at) DESC
            LIMIT $1
            "#,
            limit
        )
        .fetch_all(&self.pool)
        .await?;

        Ok(items)
    }
}
