FROM rust:1.84-slim AS builder
WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends pkg-config libssl-dev curl unzip \
    && rm -rf /var/lib/apt/lists/*

ENV SQLX_OFFLINE=true

# Prime the dependency cache and provide SQLx offline data
COPY Cargo.toml Cargo.lock ./
COPY .sqlx ./.sqlx/
RUN mkdir -p src && echo "fn main() {}" > src/main.rs
RUN cargo build --release && rm -rf src

# Copy application sources and perform the final build
COPY . .
RUN cargo build --release

FROM debian:bookworm-slim AS runtime
WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        python3 \
        python3-venv \
        python3-pip \
        chromium \
        chromium-driver \
        fonts-liberation \
        libglib2.0-0 \
        libnss3 \
        libx11-6 \
        libxcomposite1 \
        libxcursor1 \
        libxdamage1 \
        libxi6 \
        libxtst6 \
        libatk1.0-0 \
        libatk-bridge2.0-0 \
        libxrandr2 \
        libgbm1 \
        libasound2 \
        libpangocairo-1.0-0 \
    && rm -rf /var/lib/apt/lists/*

RUN python3 -m pip install --no-cache-dir --break-system-packages selenium

COPY --from=builder /app/target/release/recruitment-backend /usr/local/bin/recruitment-backend
COPY --from=builder /app/migrations ./migrations
COPY --from=builder /app/scripts ./scripts

ENV SERVER_ADDRESS=0.0.0.0:8080 \
    RUST_LOG=info \
    VACANCY_PYTHON_BIN=/usr/bin/python3 \
    VACANCY_SCRIPT_PATH=/app/scripts/vacancy_creation.py \
    VACANCY_CHROME_BINARY=/usr/bin/chromium

EXPOSE 8080

CMD ["/usr/local/bin/recruitment-backend"]