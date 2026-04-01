# Task Management API (Laravel & MySQL)

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-blue)](https://php.net)

A robust, production-ready Task Management API built for the Laravel Engineer Intern Take-Home Assignment. This project demonstrates strict adherence to business rules, database integrity, and modern deployment practices.

* **Live API:** [https://cytton-laravel-task.onrender.com/api/v1/tasks]
* **Live UI Dashboard:** [https://cytton-laravel-task.onrender.com]

---

##  Features & Business Rules

This API enforces specific constraints to ensure data consistency and logical workflow:

### 1. Smart Task Creation
* **Validation:** `due_date` must be today or in the future.
* **Integrity:** Unique constraint on `title` + `due_date` prevents duplicate entries.
* **Priority Levels:** Strictly enforced `low`, `medium`, and `high` enums.

### 2. Intelligent Listing
* **Sorting Logic:** Tasks are sorted by **Priority** (High → Low) and then by **Due Date** (Ascending).
* **Filtering:** Supports optional `status` query parameters (e.g., `?status=pending`).

### 3. Sequential Workflow (The "Progress-Only" Rule)
* Status transitions are strictly unidirectional: `pending` ➔ `in_progress` ➔ `done`.
* **Validation:** Skipping statuses (Pending to Done) or reverting (Done to In Progress) returns a `422 Unprocessable Entity`.

### 4. Protected Deletion
* **Safety Check:** Only tasks marked as `done` can be deleted. Attempting to delete active tasks returns a `403 Forbidden`.

### 5. Daily Insight Report (Bonus)
* Specialized endpoint providing a snapshot of task counts grouped by priority and status for a specific date.

---

##  API Endpoints (Prefix: `/api/v1`)

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `POST` | `/tasks` | Create a new task |
| `GET` | `/tasks` | List all tasks (supports `?status=`) |
| `PATCH` | `/tasks/{id}/status` | Update status (Sequential only) |
| `DELETE` | `/tasks/{id}` | Delete task (Must be `done`) |
| `GET` | `/tasks/report` | Daily summary (Requires `?date=YYYY-MM-DD`) |

---

## Local Setup

### Prerequisites
* Docker & Docker Compose
* PHP 8.2+ & Composer

### Quickstart with Docker
1.  **Clone and Enter:**
    ```bash
    git clone [https://github.com/your-username/cytton-laravel-task.git]

    cd cytton-laravel-task
    ```
2.  **Environment Setup:**
    ```bash
    cp .env.example .env
    ```

3.  **Run Containers:**
    ```bash
    docker compose up -d
    docker exec -it task-app php artisan migrate --seed
    ```
### Installation & Setup
## Manual Installation

composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve

## Deployment Architecture

## The application is containerized and deployed using a custom Docker configuration optimized for production environments.

Environment
Runtime: PHP 8.2 (FPM)
Extensions: intl, pdo_mysql, bcmath
Database
Provider: Clever Cloud
Type: Managed MySQL Instance
CI/CD Pipeline
Automated builds and deployments via Render's Docker environment

## Required Environment Variables
## Variable  	## Description
APP_KEY 	    Generated Laravel application key
APP_DEBUG	    Set to false in production
DB_HOST	        Clever Cloud MySQL host
DB_DATABASE	    Database name
DB_USERNAME	    Database username
DB_PASSWORD	    Database password

## API Usage Examples
   ## Create Task

   curl -X POST https://cytton-laravel-task.onrender.com/api/v1/tasks \
  -H "Content-Type: application/json" \
  -d '{"title":"Final Review","due_date":"2026-04-01","priority":"high"}'

  ## Validation - Past Due Date

  curl -i -X POST https://cytton-laravel-task.onrender.com/api/v1/tasks \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{"title": "Time Travel Task", "due_date": "2026-03-20", "priority": "low"}'

## Validation - Invalid Priority

curl -i -X POST https://cytton-laravel-task.onrender.com/api/v1/tasks \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{"title": "Urgent Task", "due_date": "2026-04-10", "priority": "super-high"}'

## Sorting Order

curl -i -X GET https://cytton-laravel-task.onrender.com/api/v1/tasks -H "Accept: application/json"

## Status Filtering

curl -i -X GET "https://cytton-laravel-task.onrender.com/api/v1/tasks?status=pending" -H "Accept: application/json"

## Status Transition

curl -i -X PATCH https://cytton-laravel-task.onrender.com/api/v1/tasks/5/status \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{"status": "in_progress"}'

## Forbidden Transition (Revert)

curl -i -X PATCH https://cytton-laravel-task.onrender.com/api/v1/tasks/5/status \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{"status": "pending"}'

## Forbidden Transition (Skip)

curl -i -X PATCH https://cytton-laravel-task.onrender.com/api/v1/tasks/6/status \
-H "Content-Type: application/json" \
-H "Accept: application/json" \
-d '{"status": "done"}'

## Valid Deletion (done)

curl -i -X DELETE https://cytton-laravel-task.onrender.com/api/v1/tasks/2 -H "Accept: application/json"

## Forbidden Deletion (in_progress)

curl -i -X DELETE https://cytton-laravel-task.onrender.com/api/v1/tasks/5 -H "Accept: application/json"

## The Report

curl -i -X GET "https://cytton-laravel-task.onrender.com/api/v1/tasks/report?date=2026-03-31" -H "Accept: application/json"

Author
Ali Malala Full Stack Developer