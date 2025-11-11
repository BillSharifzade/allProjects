-- TEST ATTEMPTS TABLE
CREATE TABLE test_attempts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    test_id UUID NOT NULL REFERENCES tests(id) ON DELETE CASCADE,
    candidate_external_id VARCHAR(255),
    candidate_name VARCHAR(255) NOT NULL,
    candidate_email VARCHAR(255) NOT NULL,
    candidate_telegram_id BIGINT,
    candidate_phone VARCHAR(50),
    access_token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMPTZ NOT NULL,
    questions_snapshot JSONB NOT NULL,
    answers JSONB,
    score DECIMAL(5,2),
    max_score DECIMAL(5,2),
    percentage DECIMAL(5,2),
    passed BOOLEAN,
    graded_answers JSONB,
    started_at TIMESTAMPTZ,
    completed_at TIMESTAMPTZ,
    time_spent_seconds INT,
    status VARCHAR(50) NOT NULL DEFAULT 'pending' 
        CHECK (status IN ('pending', 'in_progress', 'completed', 'expired', 'abandoned')),
    ip_address INET,
    user_agent TEXT,
    tab_switches INT DEFAULT 0,
    suspicious_activity JSONB,
    metadata JSONB,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TRIGGER update_attempts_updated_at BEFORE UPDATE ON test_attempts 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- ANSWER LOG TABLE
CREATE TABLE answer_logs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    attempt_id UUID NOT NULL REFERENCES test_attempts(id) ON DELETE CASCADE,
    question_id INT NOT NULL,
    answer_value JSONB NOT NULL,
    time_spent_seconds INT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- WEBHOOK LOGS TABLE
CREATE TABLE webhook_logs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    event_type VARCHAR(100) NOT NULL,
    payload JSONB NOT NULL,
    target_url TEXT NOT NULL,
    http_status INT,
    response_body TEXT,
    attempts INT DEFAULT 0,
    max_attempts INT DEFAULT 3,
    next_retry_at TIMESTAMPTZ,
    status VARCHAR(50) DEFAULT 'pending' CHECK (status IN ('pending', 'success', 'failed')),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- AUDIT LOG TABLE
CREATE TABLE audit_logs (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES users(id) ON DELETE SET NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id UUID NOT NULL,
    changes JSONB,
    ip_address INET,
    user_agent TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);