-- AI JOBS TABLE
CREATE TABLE IF NOT EXISTS ai_jobs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    status VARCHAR(20) NOT NULL DEFAULT 'pending' CHECK (status IN ('pending','running','succeeded','failed')),
    payload JSONB NOT NULL,
    result JSONB,
    error TEXT,
    persist BOOLEAN DEFAULT false,
    title VARCHAR(255),
    description TEXT,
    duration_minutes INT,
    passing_score DECIMAL(5,2),
    test_id UUID REFERENCES tests(id) ON DELETE SET NULL,
    started_at TIMESTAMPTZ,
    finished_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_ai_jobs_status_created_at ON ai_jobs(status, created_at);

CREATE TRIGGER update_ai_jobs_updated_at BEFORE UPDATE ON ai_jobs 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();


