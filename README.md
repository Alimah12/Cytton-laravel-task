# Task Management API

This project implements a simple Task Management API (Laravel) with MySQL.

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

Deployment (Railway / Render)

Railway (quick):
- Create a new project and connect the GitHub repo.
- Add the "MySQL" plugin and note the generated connection vars.
- In Railway project env, set `DB_CONNECTION=mysql` and the `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` from the plugin. Set `APP_KEY` and `APP_ENV=production`.
- Add a start command or run migrations from the Railway console:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Render (quick):
- Create a new Web Service (or static site with Docker) and connect the repo.
- Set build command: `composer install --no-dev --prefer-dist`
- Set start command: `php artisan serve --host=0.0.0.0 --port=$PORT`
- Add a Managed Database (Postgres/MySQL). If MySQL, use the connection details to set env variables.
- In Render dashboard, run `php artisan migrate --force` then `php artisan db:seed --force`.

Notes
- Ensure `APP_KEY` is set in production. Use `php artisan key:generate --show` locally and copy the value to the host env.
- For Railway/Render automated deploys, add `php artisan migrate --force` as a post-deploy command or run via their console.


API routes (prefix `/api/v1`)

- POST `/api/v1/tasks` — create task
- GET `/api/v1/tasks` — list tasks (optional `?status=pending`)
- PATCH `/api/v1/tasks/{id}/status` — update status (body `{ "status": "in_progress" }`)
- DELETE `/api/v1/tasks/{id}` — delete task (only when `done`)
- GET `/api/v1/tasks/report?date=YYYY-MM-DD` — daily report

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

Deploy notes
- Any host that supports Laravel + MySQL (Render, Railway, Heroku with ClearDB, etc.) will work.
- Ensure `APP_ENV`, `APP_KEY`, and DB variables are set in the deployment environment and run migrations.
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
# Cytton-laravel-task
# Cytton-laravel-task
