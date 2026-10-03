# Course Platform API

Laravel 13 REST API for a learning platform. This project is centered on **RBAC, moderation, administration, media handling, and relational API workflows**.

**Frontend:** https://github.com/artushhhd/course-platform-frontend

## What this project demonstrates

- Laravel REST API architecture
- Sanctum authentication
- Four-role RBAC
- Policy-based resource authorization
- Course ownership and moderation
- Image upload handling
- Likes and comments
- Administrative user management
- Feature tests

## Tech Stack

- PHP 8.3+
- Laravel 13
- Laravel Sanctum 4
- Eloquent ORM
- MySQL / SQLite
- PHPUnit

## Roles & Authorization

The application supports:

user, moderator, admin, and superadmin.

Authorization is enforced on the server through middleware and Policies. Frontend visibility is only a UX concern and is never treated as a security boundary.

Typical rules include:

- Users manage their own courses.
- Moderators can perform moderation workflows.
- Administrators manage users and courses.
- Protected resources validate both authentication and authorization.

## Course Workflow

The backend supports a course lifecycle around:

~~~text
Create
  |
  v
Course data + image
  |
  v
Moderation
  |
  +-- Approved
  +-- Not approved / controlled by status
  |
  v
Published course
~~~

Features include:

- Course CRUD
- Ownership checks
- Image uploads
- Status/moderation
- Likes
- Comments
- Pagination

## Administration

- Course moderation and approval
- User listing
- Account blocking
- User deletion
- Administrative course management

## API Surface

### Public

~~~http
POST /api/register
POST /api/login
GET  /api/courses
GET  /api/courses/{course}
~~~

### Authenticated

~~~http
GET /api/user
GET /api/profile
POST /api/logout

POST   /api/courses
PUT    /api/courses/{course}
PATCH  /api/courses/{course}
DELETE /api/courses/{course}

POST /api/courses/{course}/like
POST /api/courses/{course}/comment
~~~

### Administration

~~~http
GET    /api/admin/courses
POST   /api/admin/courses/{course}/approve
GET    /api/admin/users
POST   /api/admin/users/{user}/toggle-block
DELETE /api/admin/users/{user}
~~~

All administrative operations are protected by server-side authorization.

## Architecture

~~~text
HTTP Request
    |
    v
Middleware
    |
    +-- Authentication
    +-- Role / access checks
    |
    v
Controller
    |
    +-- Form Request
    +-- Policy
    |
    v
Eloquent Models
    |
    +-- Relationships
    +-- Persistence
    |
    v
Database / Storage
~~~

## Testing

~~~bash
php artisan test
~~~

The feature suite covers authentication, course operations, validation, authorization, and API behavior.

## Local Development

### Requirements

- PHP 8.3+
- Composer
- MySQL or SQLite

### Installation

~~~bash
git clone https://github.com/artushhhd/course-platform-backend.git
cd course-platform-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
~~~

The API runs at http://127.0.0.1:8000.

## Related Repository

**Next.js frontend:** https://github.com/artushhhd/course-platform-frontend
