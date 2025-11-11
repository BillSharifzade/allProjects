-- Enable vector extension if available; skip gracefully if not installed
DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM pg_available_extensions WHERE name = 'vector'
    ) THEN
        CREATE EXTENSION IF NOT EXISTS vector;
    ELSE
        RAISE NOTICE 'vector extension not available; skipping';
    END IF;
END
$$;

-- AI ITEMS TABLE (for traceability and analysis of generated content)
CREATE TABLE IF NOT EXISTS ai_items (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    job_id UUID REFERENCES ai_jobs(id) ON DELETE CASCADE,
    item_type VARCHAR(50), -- 'question', 'blueprint'
    content JSONB,
    model_used VARCHAR(100),
    status VARCHAR(50) DEFAULT 'accepted', -- 'accepted', 'rejected', 'revised'
    judge_score REAL,
    critique TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Add metadata column to tests table for AI provenance
ALTER TABLE tests
    ADD COLUMN IF NOT EXISTS ai_metadata JSONB;
