-- VACANCIES TABLE
CREATE TABLE vacancies (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    external_id VARCHAR(255),
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    employment_type VARCHAR(255),
    salary_from NUMERIC(12,2),
    salary_to NUMERIC(12,2),
    currency VARCHAR(16),
    description TEXT,
    requirements TEXT,
    responsibilities TEXT,
    benefits TEXT,
    apply_url TEXT,
    contact_email VARCHAR(255),
    contact_phone VARCHAR(64),
    status VARCHAR(32) NOT NULL DEFAULT 'draft',
    published_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX vacancies_status_idx ON vacancies (status);
CREATE INDEX vacancies_published_at_idx ON vacancies (published_at);

CREATE TRIGGER update_vacancies_updated_at BEFORE UPDATE ON vacancies
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
