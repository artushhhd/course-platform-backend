# Course Platform API

Laravel 13 REST API for a course platform with authentication, role-based authorization, course management, moderation, likes, comments, file uploads, and automated tests.

**Frontend:** https://github.com/artushhhd/junior-frontend-app

## Tech Stack

- PHP 8.3+
- Laravel 13.8
- Laravel Sanctum 4
- Eloquent ORM
- MySQL / SQLite
- PHPUnit
- REST API

## Key Features

### Authentication

- Registration, login, logout
- Sanctum bearer-token authentication
- Authenticated profile
- Rate limiting on registration and login
- Blocked accounts cannot authenticate

### Authorization

Four roles are supported:

```text
user
moderator
admin
superadmin
```

Authorization is enforced on the API using middleware and Laravel Policies.

- Users can manage their own courses.
- Moderators can moderate courses within their authorization scope.
- Admin actions are restricted by role hierarchy.
- Frontend role checks are only for UI behavior; the API remains the source of truth.

### Courses

- CRUD operations
- Ownership validation
- Image uploads
- Course status and moderation
- Likes / unlike
- Comments
- Pagination

### Administration

- Course moderation and approval
- User listing
- Account blocking
- User deletion
- Administrative course management

## API

### Public

```http
POST /api/register
POST /api/login

GET /api/courses
GET /api/courses/{course}
```

### Authenticated

```http
GET /api/user
GET /api/profile
POST /api/logout

POST /api/courses
PUT /api/courses/{course}
PATCH /api/courses/{course}
DELETE /api/courses/{course}

POST /api/courses/{course}/like
POST /api/courses/{course}/comment
```

### Administration

```http
GET /api/admin/courses
POST /api/admin/courses/{course}/approve
POST /api/admin/courses/{course}
DELETE /api/admin/courses/{course}

GET /api/admin/users
POST /api/admin/users/{user}/toggle-block
DELETE /api/admin/users/{user}
```

Authenticated endpoints use Sanctum. Administrative endpoints additionally require the appropriate staff authorization.

## Architecture

Responsibilities are separated between HTTP handling, validation, authorization, persistence, and routing.

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
└── Policies/

database/
├── factories/
├── migrations/
└── seeders/

routes/
└── api.php

tests/
└── Feature/
```

| Layer | Responsibility |
|---|---|
| Controllers | HTTP request orchestration |
| Form Requests | Input validation |
| Middleware | Authentication / staff access |
| Policies | Resource authorization |
| Models | Database relationships and persistence |
| Migrations | Database schema |
| Seeders | Development data |
| Feature Tests | API behavior and authorization |

## Testing

Run:

```bash
php artisan test
```

Feature tests cover authentication, course operations, validation, authorization, and other API behavior.

## Installation

### Requirements

- PHP 8.3+
- Composer
- Node.js / npm
- SQLite or MySQL

### 1. Clone

```bash
git clone https://github.com/artushhhd/junior-backend-api.git
cd junior-backend-api
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Prepare the database

For a fresh development database with seed data:

```bash
php artisan migrate:fresh --seed
```

### 5. Link storage

```bash
php artisan storage:link
```

### 6. Start the API

```bash
php artisan serve
```

Default local API:

```text
http://127.0.0.1:8000
```

## Frontend

The matching Next.js application:

https://github.com/artushhhd/junior-frontend-app

The frontend consumes this API through a centralized API client.

## Project Purpose

This portfolio project demonstrates practical Laravel backend development:

- REST API design
- Authentication and authorization
- Database relationships
- Validation
- File storage
- Moderation
- Automated testing
- Separation of application responsibilities

The project is intended as a portfolio demonstration rather than a production SaaS application.
