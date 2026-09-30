# Course Platform API

Laravel 13 REST API for a course platform with authentication, role-based authorization, course management, moderation, media uploads, likes, comments, and feature tests.

**Frontend:** https://github.com/artushhhd/junior-frontend-app

## Overview

The Course Platform is a full-stack application split into independent Laravel and Next.js projects. This repository contains the backend API and its application rules.

The project focuses on REST API design, resource authorization, validation, database relationships, file handling, and automated API testing.

## Tech Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum 4
- Eloquent ORM
- MySQL / SQLite
- PHPUnit
- REST API

## Core Features

### Authentication

- Registration, login, and logout
- Sanctum bearer-token authentication
- Authenticated profile
- Login and registration rate limiting
- Blocked accounts cannot authenticate

### Authorization

The API supports four roles:

```text
user
moderator
admin
superadmin
```

Authorization is enforced server-side through middleware and Laravel Policies.

- Users can manage resources they own
- Moderators can perform authorized moderation actions
- Administrative actions are restricted by role
- Frontend role checks affect UI behavior only; the API remains the security boundary

### Courses

- Course CRUD
- Ownership validation
- Image uploads
- Course status and moderation
- Likes and unlike
- Comments
- Pagination

### Administration

- Course moderation and approval
- User listing
- Account blocking
- User deletion
- Administrative course management

## API Surface

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

Authenticated endpoints use Sanctum, while administrative endpoints require the corresponding server-side authorization.

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
| Middleware | Authentication and access control |
| Policies | Resource authorization |
| Models | Relationships and persistence |
| Migrations | Database schema |
| Seeders | Development data |
| Feature Tests | API behavior and authorization |

## Testing

Run the feature test suite with:

```bash
php artisan test
```

The tests cover authentication, course operations, validation, authorization, and other API behavior.

## Local Development

### Requirements

- PHP 8.3+
- Composer
- Node.js / npm
- MySQL or SQLite

### Installation

```bash
git clone https://github.com/artushhhd/junior-backend-api.git
cd junior-backend-api

composer install
npm install

cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

The API runs at:

```text
http://127.0.0.1:8000
```

## Frontend

The corresponding Next.js client is maintained separately:

https://github.com/artushhhd/junior-frontend-app

The frontend communicates with this API through a centralized API client and uses the backend as the source of truth for authorization.

## Project Structure

The repository follows Laravel conventions and keeps HTTP handling, validation, authorization, persistence, database changes, seed data, and feature tests in their respective layers.

This project is a portfolio application demonstrating practical Laravel backend development rather than a production SaaS platform.
