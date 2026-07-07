# Deployment

This project keeps the local Sail `compose.yaml` unchanged and ships a production-oriented `compose.prod.yaml` for a single EC2 host behind Traefik. The production stack reuses the MySQL and Redis services from `/home/chenf/laravel/exchange-stuff`; it does not start its own database or Redis containers.

## Services

- `nginx`: internal HTTP service for Traefik, serving static files and forwarding PHP requests to PHP-FPM.
- `app`: Laravel PHP-FPM application container.
- `queue`: Laravel queue worker for queued mail and background jobs.
- `scheduler`: Laravel scheduler process.
- shared MySQL: provided by the running `exchange-stuff` production stack.
- shared Redis: provided by the running `exchange-stuff` production stack.

## First Deploy

On the EC2 server, install Docker and the Docker Compose plugin, then clone the repository.

Create the external Docker network used by the shared Traefik proxy if it does not already exist:

```bash
docker network create proxy
```

Make sure the `exchange-stuff` production stack is already running. This app joins that stack's app network, `exchange-stuff_app`, to reach `mysql` and `redis`.

Create the production environment file:

```bash
cp .env.production.example .env
```

Edit `.env` and set:

- `APP_URL`
- `APP_HOST`
- `SHARED_APP_NETWORK` if the `exchange-stuff` Compose project uses a non-default network name
- `APP_KEY`
- `DB_PASSWORD`
- mail settings

The configured `DB_DATABASE` and `DB_USERNAME` must already exist in the shared MySQL server. If they do not, create them from the `exchange-stuff` MySQL container before running this app's migrations.

Generate an application key if you do not already have one:

```bash
docker compose --env-file .env -f compose.prod.yaml run --rm app php artisan key:generate --show
```

Copy the printed key into `.env` as `APP_KEY`.

Build and start the stack:

```bash
docker compose --env-file .env -f compose.prod.yaml up -d --build
```

Run database migrations:

```bash
docker compose --env-file .env -f compose.prod.yaml exec app php artisan migrate --force
```

Seed required lookup/demo data only when this production environment should start with the bundled event data:

```bash
docker compose --env-file .env -f compose.prod.yaml exec app php artisan db:seed --force
docker compose --env-file .env -f compose.prod.yaml exec app php artisan tickets:generate
```

Create the public storage symlink:

```bash
docker compose --env-file .env -f compose.prod.yaml exec app php artisan storage:link
```

## Updates

Pull the latest code, rebuild the image, restart services, run migrations, refresh the storage symlink, and check the app:

```bash
./scripts/deploy-production.sh
```

The app image builds Composer dependencies and Vite assets during `docker compose -f compose.prod.yaml build`; you do not need Node.js, Composer, `vendor`, or `node_modules` on the EC2 host.

## Automatic Deployment

The `.github/workflows/deploy-production.yml` workflow runs after every push to `main`. It connects to the EC2 instance over SSH and runs `./scripts/deploy-production.sh` inside the server clone.

Configure these GitHub repository secrets:

- `EC2_HOST`: public IP or hostname for the EC2 instance.
- `EC2_USER`: SSH user, for example `ubuntu`.
- `EC2_SSH_KEY`: private SSH key with access to the EC2 instance.
- `EC2_APP_DIR`: absolute path to this repository on EC2.
- `EC2_SSH_PORT`: optional SSH port. Use `22` if not customized.

## Traefik

This project expects Traefik to run separately on the external Docker network named `proxy`. The production Compose file does not publish Nginx directly to the host. Instead, Traefik discovers the `nginx` service through Docker labels and routes requests for `APP_HOST` to port `80` inside the Nginx container.

## Shared MySQL and Redis

The Laravel containers join an external Docker network named by `SHARED_APP_NETWORK`, defaulting to `exchange-stuff_app`. On that network, `DB_HOST=mysql` and `REDIS_HOST=redis` resolve to the services from `/home/chenf/laravel/exchange-stuff`.

If the `exchange-stuff` stack was started with a custom Compose project name, find the actual network name on EC2:

```bash
docker network ls
```

Then set it in `.env`:

```env
SHARED_APP_NETWORK=actual_exchange_stuff_network_name
DB_HOST=mysql
REDIS_HOST=redis
```

Set these values in `.env`:

```env
APP_URL=https://example.com
APP_HOST=example.com
SESSION_SECURE_COOKIE=true
```

If Traefik is configured for plain HTTP only, use:

```env
APP_URL=http://example.com
APP_HOST=example.com
SESSION_SECURE_COOKIE=false
```

## Environment Changes

The production Compose file mounts the server `.env` file into the Laravel containers at `/var/www/html/.env`.

After edits to `.env` on the server, restart the Laravel services so the production config cache is rebuilt with the new values:

```bash
docker compose --env-file .env -f compose.prod.yaml restart app queue scheduler
```

## Useful Commands

View logs:

```bash
docker compose --env-file .env -f compose.prod.yaml logs -f nginx app queue scheduler
```

Restart the queue worker after code or environment changes:

```bash
docker compose --env-file .env -f compose.prod.yaml restart queue
```

Run an Artisan command:

```bash
docker compose --env-file .env -f compose.prod.yaml exec app php artisan about
```

Open a shell inside the app container:

```bash
docker compose --env-file .env -f compose.prod.yaml exec app sh
```

## Backups

The database lives in the `mysql-data` Docker volume. Back it up before deployments that change schema or data:

```bash
docker compose --env-file .env -f compose.prod.yaml exec mysql sh -c 'mysqldump -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' > backup.sql
```
