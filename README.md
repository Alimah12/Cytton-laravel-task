# Task Management API

This project implements a simple Task Management API (Laravel) with MySQL.

Live demo: https://cytton-laravel-task.onrender.com

Features
- Create tasks
- List tasks (filter by status, sorted by priority then due date)
- Update task status (only forward transitions)
- Delete tasks (only when status is `done`)
- Daily report endpoint

Quick setup

1. Copy your MySQL credentials into `.env` (DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD) or use the provided `.env.example`.
2. Install dependencies (if not already):

```bash
composer install
```

3. Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```

Local MySQL with Docker Compose
1. Start a local MySQL service:

```bash
docker compose -f docker-compose.mysql.yml up -d
```

2. Copy `.env.example` to `.env` and adjust if necessary:

```bash
cp .env.example .env
# then set APP_KEY (php artisan key:generate) and other vars
```

3. Install and run migrations/seeds (then start server):

```bash
composer install
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan serve --host=0.0.0.0 --port=8000
```

Deployment (Render + Clever Cloud MySQL)

This repository includes a `Dockerfile` so Render will build the project using Docker. The Docker image installs the PHP `intl` extension (required by Laravel) and runs Composer to install PHP dependencies.

Clever Cloud (MySQL)
- Create a MySQL add-on on Clever Cloud and copy the connection variables.
- In the Render service settings, add these environment variables:
  - `DB_CONNECTION=mysql`
  - `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (from Clever Cloud)
  - `APP_ENV=production`
  - `APP_KEY` (generate locally with `php artisan key:generate --show` and paste the value)

Render
- In the Render service, select the repository and choose "Docker" as the environment (the `Dockerfile` in this repo enables that).
- Start command: `php artisan serve --host=0.0.0.0 --port=$PORT`
- After deploy, run migrations and seeders via Render console or add a post-deploy command:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Note: `intl` is installed in the provided `Dockerfile` to avoid runtime errors on Render.

Notes
- Ensure `APP_KEY` is set in production. Use `php artisan key:generate --show` locally and copy the value to the host env.
- For Railway/Render automated deploys, add `php artisan migrate --force` as a post-deploy command or run via their console.


API routes (prefix `/api/v1`)

- POST `/api/v1/tasks` — create task
- GET `/api/v1/tasks` — list tasks (optional `?status=pending`)
- PATCH `/api/v1/tasks/{id}/status` — update status (body `{ "status": "in_progress" }`)
- DELETE `/api/v1/tasks/{id}` — delete task (only when `done`)
- GET `/api/v1/tasks/report?date=YYYY-MM-DD` — daily report

Registered endpoints (as deployed)

- GET|HEAD   api/test .................................... routes/api.php:7
- GET|HEAD   api/v1/tasks ........................ Api\\TaskController@index
- POST       api/v1/tasks ........................ Api\\TaskController@store
- GET|HEAD   api/v1/tasks/report ................ Api\\TaskController@report
- DELETE     api/v1/tasks/{id} ................. Api\\TaskController@destroy
- PATCH      api/v1/tasks/{id}/status ..... Api\\TaskController@updateStatus

Example curl requests

Create:
```bash
curl -X POST http://localhost:8000/api/v1/tasks \
  -H "Content-Type: application/json" \
  -d '{"title":"My Task","due_date":"2026-04-01","priority":"high"}'
```

List:
```bash
curl http://localhost:8000/api/v1/tasks
```

Update status:
```bash
curl -X PATCH http://localhost:8000/api/v1/tasks/1/status \
  -H "Content-Type: application/json" \
  -d '{"status":"in_progress"}'
```

Delete:
```bash
curl -X DELETE http://localhost:8000/api/v1/tasks/1
```

Report:
```bash
curl http://localhost:8000/api/v1/tasks/report?date=2026-04-01
```

Test the live deployment (Render):

```bash
curl https://cytton-laravel-task.onrender.com/api/test
curl https://cytton-laravel-task.onrender.com/api/v1/tasks
```

Web UI

The project includes a minimal UI to interact with the API at the site root (`/`).

- Local: http://localhost:8000/
- Live (Render): https://cytton-laravel-task.onrender.com/

The UI supports creating tasks, filtering by status, advancing a task's status, and deleting tasks (only when status is `done`).

Deploy notes
- Any host that supports Laravel + MySQL (Render, Railway, Heroku with ClearDB, etc.) will work.
- Ensure `APP_ENV`, `APP_KEY`, and DB variables are set in the deployment environment and run migrations.

# Cytton-laravel-task
