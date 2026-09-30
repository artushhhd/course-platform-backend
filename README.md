# Course Platform API

Laravel 13 REST API for a full-stack course platform with authentication, role-based authorization, course management, moderation, media uploads, likes, comments, and feature tests.

**Frontend:** https://github.com/artushhhd/course-platform-frontend

## Overview

The Course Platform is split into independent Laravel and Next.js applications. This repository contains the backend API responsible for business rules, validation, authorization, persistence, and file handling.

The project demonstrates practical backend patterns including REST API design, Laravel Policies, Form Requests, Eloquent relationships, role-based access control, media handling, and automated feature testing.

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
- Blocked-account protection

### Authorization

The API supports four roles:

`user`, `moderator`, `admin`, and `superadmin`.

Authorization is enforced server-side through middleware and Laravel Policies.

- Resource ownership checks
- Role-based administrative access
- Moderation permissions
- Backend remains the security boundary

### Courses

- Course CRUD
- Ownership validation
- Image uploads
- Course status and moderation
- Like / unlike
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
GET /api/admin/users
POST /api/admin/users/{user}/toggle-block
DELETE /api/admin/users/{user}
```

Administrative endpoints require the corresponding server-side authorization.

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

Run:

```bash
php artisan test
```

The feature suite covers authentication, course operations, validation, authorization, and API behavior.

## Local Development

### Requirements

- PHP 8.3+
- Composer
- MySQL or SQLite

### Installation

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

The API runs at:

```text
http://127.0.0.1:8000
```

## Frontend

The corresponding Next.js client is maintained separately:

https://github.com/artushhhd/course-platform-frontend

The frontend communicates with this API through a centralized API client and relies on the backend as the source of truth for authorization.

## Project Scope

This is a portfolio application focused on demonstrating practical Laravel backend development, API architecture, authorization, database relationships, file handling, and testing.