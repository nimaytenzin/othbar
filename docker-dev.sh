#!/usr/bin/env bash
# Run Laravel + Vite via Docker (PHP 8.4). From repo root: ./docker-dev.sh
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT"

export HOST_UID="$(id -u)"
export HOST_GID="$(id -g)"
# Prefix avoids clashing with docker-compose reading Laravel .env (empty APP_PORT breaks ports)
export OTHBAR_APP_PORT="${APP_PORT:-8000}"
export OTHBAR_VITE_PORT="${VITE_PORT:-5173}"
COMPOSE=(docker compose -f docker-compose.dev.yml)

WITH_VITE=1
DB_ONLY=0
FORCE_BUILD=0
RUN_MIGRATE=0
RUN_MIGRATE_FRESH=0

usage() {
  echo "Usage: $0 [options]"
  echo "  Full stack: MariaDB (Docker) + Laravel app + optional Vite."
  echo "  App: http://localhost:\${APP_PORT:-8000}  |  DB published: localhost:\${MYSQL_PUBLISH_PORT:-3307}"
  echo ""
  echo "Options:"
  echo "  --db-only      Only start MariaDB in Docker (use when PHP runs on your machine:"
  echo "                 DB_HOST=127.0.0.1, DB_PORT same as MYSQL_PUBLISH_PORT, default 3307)"
  echo "  --no-vite      Only run php artisan serve (no Node/Vite container)"
  echo "  --migrate      Run php artisan migrate before starting servers"
  echo "  --migrate-fresh  Drop all tables, then migrate (--force). Wipes DB data; use when"
  echo "                   migrate fails with \"table already exists\" or schema is inconsistent."
  echo "  --build        Force rebuild the PHP dev image"
  echo "  --install      Always run composer install (default: run if vendor/ missing)"
  echo "  -h, --help     Show this help"
}

FORCE_INSTALL=0
while [[ $# -gt 0 ]]; do
  case "$1" in
    --db-only) DB_ONLY=1 ;;
    --no-vite) WITH_VITE=0 ;;
    --migrate) RUN_MIGRATE=1 ;;
    --migrate-fresh) RUN_MIGRATE_FRESH=1 ;;
    --build) FORCE_BUILD=1 ;;
    --install) FORCE_INSTALL=1 ;;
    -h|--help) usage; exit 0 ;;
    *)
      echo "Unknown option: $1" >&2
      usage >&2
      exit 1
      ;;
  esac
  shift
done

if [[ "${RUN_MIGRATE}" -eq 1 && "${RUN_MIGRATE_FRESH}" -eq 1 ]]; then
  echo "Use only one of --migrate or --migrate-fresh." >&2
  exit 1
fi

if [[ ! -f .env ]]; then
  if [[ -f .env.example ]]; then
    cp .env.example .env
    echo "Created .env from .env.example — edit DB_* and other values if needed."
  else
    echo "Missing .env and .env.example. Cannot continue." >&2
    exit 1
  fi
fi

if grep -qE '^DB_USERNAME=root(\s|$)' .env 2>/dev/null; then
  echo "WARNING: MariaDB Docker cannot use MYSQL_USER=root. Use a non-root DB_USERNAME (e.g. othbar) and DB_PASSWORD, or edit docker-compose.dev.yml." >&2
fi

if [[ "${DB_ONLY}" -eq 1 ]]; then
  echo "Starting MariaDB only (Docker). Stop with Ctrl+C, or run detached: docker compose -f docker-compose.dev.yml up -d mariadb"
  echo "  Host connection: 127.0.0.1:${MYSQL_PUBLISH_PORT:-3307}  (user/pass from .env DB_USERNAME / DB_PASSWORD)"
  "${COMPOSE[@]}" up mariadb
  exit 0
fi

if [[ "${FORCE_BUILD}" -eq 1 ]]; then
  "${COMPOSE[@]}" build --no-cache app
else
  "${COMPOSE[@]}" build app
fi

run_app() {
  "${COMPOSE[@]}" run --rm --no-deps "$@"
}

if [[ "${FORCE_INSTALL}" -eq 1 ]] || [[ ! -f vendor/autoload.php ]]; then
  echo "Running composer install..."
  run_app app composer install --no-interaction
fi

if ! grep -qE '^APP_KEY=base64:.+$' .env 2>/dev/null; then
  echo "Generating APP_KEY..."
  run_app app php artisan key:generate --force
fi

if [[ "${RUN_MIGRATE_FRESH}" -eq 1 ]]; then
  echo "migrate:fresh: dropping all tables and re-running migrations (all DB data removed)..."
  "${COMPOSE[@]}" run --rm app php artisan migrate:fresh --force
elif [[ "${RUN_MIGRATE}" -eq 1 ]]; then
  echo "Running migrations..."
  "${COMPOSE[@]}" run --rm app php artisan migrate --force
fi

# If bootstrap/cache/config.php exists, Laravel skips loading .env and ignores Compose DB_* (you get 127.0.0.1 + stale PORT).
echo "Removing Laravel config cache if present (required so the app container uses DB_HOST=mariadb)..."
rm -f "${ROOT}/bootstrap/cache/config.php"
run_app app php artisan config:clear

echo ""
echo "Starting stack (Ctrl+C stops). MariaDB runs in Docker; the app container uses DB_HOST=mariadb."
echo "  App:     http://localhost:${OTHBAR_APP_PORT}"
echo "  MariaDB: localhost:${MYSQL_PUBLISH_PORT:-3307} (host → container 3306)"
if [[ "${WITH_VITE}" -eq 1 ]]; then
  echo "  Vite:    http://localhost:${OTHBAR_VITE_PORT}"
  echo ""
  "${COMPOSE[@]}" up app vite
else
  echo ""
  "${COMPOSE[@]}" up app
fi
