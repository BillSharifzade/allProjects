#!/usr/bin/env sh
set -eu

if [ "${RUN_MIGRATIONS:-1}" = "1" ]; then
  echo "Running database migrations..."
  /usr/local/bin/recruitment-backend migrate || {
    echo "Migrations failed" >&2
    exit 1
  }
fi

echo "Starting recruitment-backend service..."
exec /usr/local/bin/recruitment-backend serve
