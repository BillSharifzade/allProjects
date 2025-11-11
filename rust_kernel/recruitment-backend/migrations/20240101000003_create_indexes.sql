-- Indexes for users table
CREATE INDEX idx_users_external_id ON users(external_id);
CREATE INDEX idx_users_email ON users(email);

-- Indexes for tests table
CREATE INDEX idx_tests_external_id ON tests(external_id);
CREATE INDEX idx_tests_created_by ON tests(created_by);
CREATE INDEX idx_tests_is_active ON tests(is_active);

-- Indexes for test_attempts table
CREATE INDEX idx_attempts_test_id ON test_attempts(test_id);
CREATE INDEX idx_attempts_candidate_email ON test_attempts(candidate_email);
CREATE INDEX idx_attempts_token ON test_attempts(access_token);
CREATE INDEX idx_attempts_status ON test_attempts(status);
CREATE INDEX idx_attempts_expires_at ON test_attempts(expires_at);
CREATE INDEX idx_attempts_candidate_external_id ON test_attempts(candidate_external_id);

-- Indexes for answer_logs table
CREATE INDEX idx_answer_logs_attempt_id ON answer_logs(attempt_id);

-- Indexes for webhook_logs table
CREATE INDEX idx_webhooks_status ON webhook_logs(status);
CREATE INDEX idx_webhooks_next_retry ON webhook_logs(next_retry_at);

-- Indexes for audit_logs table
CREATE INDEX idx_audit_entity ON audit_logs(entity_type, entity_id);
CREATE INDEX idx_audit_user ON audit_logs(user_id);