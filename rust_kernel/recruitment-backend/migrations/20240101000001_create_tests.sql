-- TESTS TABLE
CREATE TABLE tests (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    external_id VARCHAR(255), -- ID from "Первая Форма" (if applicable)
    title VARCHAR(255) NOT NULL,
    description TEXT,
    instructions TEXT,
    questions JSONB NOT NULL, -- Array of question objects
    duration_minutes INT NOT NULL DEFAULT 60,
    passing_score DECIMAL(5,2) NOT NULL DEFAULT 70.0,
    max_attempts INT DEFAULT 1,
    shuffle_questions BOOLEAN DEFAULT false,
    shuffle_options BOOLEAN DEFAULT false,
    show_results_immediately BOOLEAN DEFAULT false,
    created_by UUID REFERENCES users(id) ON DELETE SET NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TRIGGER update_tests_updated_at BEFORE UPDATE ON tests 
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();