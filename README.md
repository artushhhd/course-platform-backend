# Course Platform API

Laravel 13 REST API for a learning platform, focused on authentication, RBAC, server-side authorization, moderation, media handling, relational data, and feature testing.

**Frontend:** https://github.com/artushhhd/course-platform-frontend

## Highlights

- Laravel 13 REST API
- Sanctum authentication
- RBAC: user, moderator, admin, superadmin
- Policy-based authorization and ownership checks
- Form Request validation
- Course CRUD, moderation, and image uploads
- Likes, comments, and pagination
- Administrative user/course management
- PHPUnit feature tests

## Stack

PHP 8.3+ · Laravel 13 · Sanctum · Eloquent · MySQL / SQLite · PHPUnit

## Architecture

```text
Request -> Middleware/Auth -> Controller -> Form Request -> Policy -> Eloquent -> Database/Storage
```

Security is enforced on the API, not by frontend visibility.

## API

```http
POST /api/register
POST /api/login
GET  /api/courses
GET  /api/courses/{course}
GET  /api/profile
POST /api/logout
POST /api/courses
PUT /api/courses/{course}
DELETE /api/courses/{course}
POST /api/courses/{course}/like
POST /api/courses/{course}/comment
GET /api/admin/courses
POST /api/admin/courses/{course}/approve
GET /api/admin/users
POST /api/admin/users/{user}/toggle-block
DELETE /api/admin/users/{user}
```

## Testing

```bash
php artisan test
```

## Run Locally

```bash
git clone https://github.com/artushhhd/course-platform-backend.git
cd course-platform-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```
