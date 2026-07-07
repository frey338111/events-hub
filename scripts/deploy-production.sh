#!/usr/bin/env bash
set -Eeuo pipefail

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)}"
BRANCH="${DEPLOY_BRANCH:-main}"
COMPOSE_FILE="${COMPOSE_FILE:-compose.prod.yaml}"
COMPOSE_ENV_FILE="${COMPOSE_ENV_FILE:-.env}"
LOCK_FILE="${LOCK_FILE:-/tmp/events-hub-deploy.lock}"

log() {
    printf '[%s] %s\n' "$(date -u '+%Y-%m-%dT%H:%M:%SZ')" "$*"
}

cd "$APP_DIR"

exec 9>"$LOCK_FILE"
if ! flock -n 9; then
    log "Another deployment is already running."
    exit 1
fi

log "Starting deployment in $APP_DIR on branch $BRANCH"

if [ ! -f "$COMPOSE_ENV_FILE" ]; then
    log "Missing $COMPOSE_ENV_FILE file. Create it from .env.production.example before deploying."
    exit 1
fi

if ! git diff --quiet || ! git diff --cached --quiet; then
    log "Working tree has uncommitted changes. Refusing to deploy."
    git status --short
    exit 1
fi

git fetch origin "$BRANCH"
git checkout "$BRANCH"
git pull --ff-only origin "$BRANCH"

docker compose --env-file "$COMPOSE_ENV_FILE" -f "$COMPOSE_FILE" up -d --build --remove-orphans
docker compose --env-file "$COMPOSE_ENV_FILE" -f "$COMPOSE_FILE" exec -T app php artisan migrate --force
docker compose --env-file "$COMPOSE_ENV_FILE" -f "$COMPOSE_FILE" exec -T app php artisan storage:link || true
docker compose --env-file "$COMPOSE_ENV_FILE" -f "$COMPOSE_FILE" exec -T app php artisan about

log "Deployment complete."
