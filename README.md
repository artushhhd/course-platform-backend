# Course Platform API

Laravel REST API for a learning platform, focused on authentication, role-based access control, course workflows, moderation, media handling, and feature testing.

**Frontend:** [course-platform-frontend](https://github.com/artushhhd/course-platform-frontend)

## Features

- Laravel Sanctum authentication
- Role-based access control: user, moderator, admin, superadmin
- Policy-based authorization and ownership checks
- Form Request validation
- Course creation, updates, deletion, and moderation
- Image uploads and storage integration
- Likes, comments, and pagination
- Administrative user and course workflows
- PHPUnit feature tests

## Technology

PHP 8.3+ · Laravel 13 · Sanctum · Eloquent ORM · MySQL / SQLite · PHPUnit

## Request Flow

```text
HTTP request
  -> routing and middleware
  -> authentication
  -> request validation
  -> policy / authorization
  -> controller and response
  -> Eloquent
  -> database or storage
```

The backend is the security boundary. Client-side visibility does not replace server-side authorization.

## Main API Routes

| Method | Endpoint | Purpose |
|---|---|---|
| POST | `/api/register` | Register |
| POST | `/api/login` | Authenticate |
| POST | `/api/logout` | End session |
| GET | `/api/profile` | Current profile |
| GET | `/api/courses` | Browse courses |
| GET | `/api/courses/{course}` | View course |
| POST | `/api/courses` | Create course |
| PUT | `/api/courses/{course}` | Update course |
| DELETE | `/api/courses/{course}` | Delete course |
| POST | `/api/courses/{course}/like` | Like course |
| POST | `/api/courses/{course}/comment` | Comment on course |
| GET | `/api/admin/courses` | Admin course workflow |
| POST | `/api/admin/courses/{course}/approve` | Approve course |
| GET | `/api/admin/users` | List users |
| POST | `/api/admin/users/{user}/toggle-block` | Toggle user block |
| DELETE | `/api/admin/users/{user}` | Delete user |

Verify route definitions in the application if you need an exhaustive or version-specific API reference.

## Run Locally

Requirements: PHP, Composer, and a configured MySQL or SQLite database.

```bash
git clone https://github.com/artushhhd/course-platform-backend.git
cd course-platform-backend
composer install
```

Copy `.env.example` to `.env` (use `cp` on macOS/Linux or `copy` in Windows Command Prompt), configure the database, and then run:

```bash
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

The API is served at `http://127.0.0.1:8000` by default. Keep credentials and secrets in your local `.env` file.

## Development-only seed data

The database seeder creates demo accounts with short, predictable passwords for local testing. Use these accounts only in a disposable local database. Do not seed them into a public or production environment; replace or remove demo credentials before deployment.

## Tests

```bash
php artisan test
```

See the [frontend README](https://github.com/artushhhd/course-platform-frontend) for client setup.
