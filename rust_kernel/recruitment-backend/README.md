This service powers recruitment workflows between the "Первая Форма" HR system, Telegram bot, and Telegram Mini App. It exposes integration endpoints for partner systems, public candidate flows, AI-assisted tooling, and incoming webhooks.

## Environment Setup

Copy `.env.example` to `.env` and review the defaults (override `DATABASE_URL` to match your Postgres host/IP when running in Docker):

```
SERVER_ADDRESS=127.0.0.1:8080
DATABASE_URL=postgres://postgres:postgres@localhost:5432/recruitment
JWT_SECRET=changeme-super-secret
WEBHOOK_SECRET=changeme-webhook
OPENAI_API_KEY=
TELEGRAM_BOT_WEBHOOK_URL=
PUBLIC_RPS=20
INTEGRATION_RPS=10
```

Required infrastructure:
- PostgreSQL 15+ reachable at `DATABASE_URL`
- OpenRouter/OpenAI key if AI generation should run end-to-end
- Chromium + chromedriver binaries (installed automatically in Docker image)

### Provisioning PostgreSQL (example on Ubuntu)

```bash
sudo apt install postgresql postgresql-contrib -y
sudo -iu postgres psql -c "ALTER USER postgres WITH PASSWORD 'password';"
sudo -iu postgres psql -c "CREATE DATABASE recruitment_db;"

# Allow Docker bridge/network access (adjust CIDR as needed)
sudo tee /etc/postgresql/16/main/pg_hba.conf >/dev/null <<'EOF'
local   all             all                                     peer
host    all             all             127.0.0.1/32            md5
host    all             all             172.17.0.0/16           md5
EOF
sudo sed -i "s/^#listen_addresses = 'localhost'/listen_addresses = '*'/" /etc/postgresql/16/main/postgresql.conf
sudo systemctl restart postgresql
```

Run database migrations:

```bash
cargo sqlx migrate run
```

Start the API:

```bash
cargo run
```

### Docker Deployment

Build the production image:

```bash
docker build \
  --build-arg DATABASE_URL=postgres://postgres:password@10.10.10.25:5432/recruitment_db \
  -t recruitment-backend:latest .
```

Run the container (example using environment variables and network binding):

```bash
docker run \
  --env-file .env \
  -e SERVER_ADDRESS=0.0.0.0:8080 \
  -p 8080:8080 \
  recruitment-backend:latest
```

Notes:
- Inside Docker, `localhost` refers to the container. Set `DATABASE_URL` to the host/IP (e.g., `postgres://postgres:password@10.10.10.25:5432/recruitment_db`).
- Selenium automation expects `VACANCY_PYTHON_BIN`, `VACANCY_SCRIPT_PATH`, and `VACANCY_CHROME_BINARY` (defaults baked into the image). Override if running outside Docker.
- Startup runs `sqlx::migrate!`, so begin with an empty database or ensure `_sqlx_migrations` is in sync.

## Endpoint Guide

- **Health**
  - `GET /health` — readiness probe.

- **Integration API** (JWT protected under `/api/integration/*`)
  - `GET /api/integration/tests` — list tests with pagination.
  - `POST /api/integration/tests` — create a test from a `CreateTestPayload` body.
  - `GET /api/integration/tests/:id` — fetch test by UUID.
  - `PATCH /api/integration/tests/:id` — update metadata/questions.
  - `DELETE /api/integration/tests/:id` — archive a test.
  - `POST /api/integration/test-invites` — invite a candidate and create an attempt.
  - `GET /api/integration/test-attempts` — list attempts with filters.
  - `GET /api/integration/test-attempts/:id` — retrieve attempt details, answers, and status.
  - `POST /api/integration/ai-jobs` — enqueue AI test generation and return job ID.
  - `GET /api/integration/ai-jobs/:id` — poll AI job progress/result.
  - `POST /api/integration/tests/spec` — generate & persist a test from blueprint specs.
  - `POST /api/integration/vacancies/external` — trigger Selenium vacancy creation.
  - `POST /api/integration/vacancies/external/delete` — trigger Selenium vacancy deletion by ID.

- **Public Candidate API** (token-based under `/api/public/*`)
  - `GET /api/public/tests/:token` — fetch test metadata and questions for a candidate.
  - `POST /api/public/tests/:token/start` — mark the attempt as started.
  - `PATCH /api/public/tests/:token/answer` — save in-progress answers.
  - `POST /api/public/tests/:token/submit` — submit final answers for grading.
  - `GET /api/public/tests/:token/status` — check attempt outcomes and scoring.

- **Webhook Ingestion** (signed with `X-Webhook-Secret`)
  - `POST /webhook/test-assigned` — record that a test invite was delivered; enqueues outgoing notifications.
  - `POST /webhook/test-completed` — notify that an attempt is complete; triggers downstream messaging.

## Testing & Tooling

- Run `cargo test` with PostgreSQL accessible via `DATABASE_URL`.
- AI-dependent tests use mocked payloads but still need env defaults (`WEBHOOK_SECRET`, etc.).
- Webhook integration test: see `tests/webhook_test.rs` for DB assertions.
- Inspect recent webhook jobs:

```bash
psql "$DATABASE_URL" -c "SELECT id, event_type, status, attempts FROM webhook_logs ORDER BY created_at DESC LIMIT 20"
```

Background processing:
- AI job queue worker: `AiQueueService::run_once()` processes `ai_jobs` table entries.
- Notification delivery: `NotificationService` retries webhook deliveries based on `webhook_logs` status.