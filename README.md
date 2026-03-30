# ServiceHub

A service order management system built with **Laravel 13**, **Inertia.js v3**, **Vue 3**, and **Pest** (TDD).

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13 / PHP 8.4 |
| Frontend | Vue 3 + TypeScript + Inertia.js v3 |
| Styling | Tailwind CSS v4 + Reka UI |
| Database | SQLite (swappable to MySQL/Postgres) |
| Queue | Laravel database queue |
| Testing | Pest v3 (TDD) |
| Container | Docker + Supervisor (php-fpm + nginx + queue worker) |

## Features

- **Companies** — CRUD with cascade to projects
- **Projects** — CRUD, belong to a company
- **Tickets** — CRUD with optional file attachment (`.json` / `.txt`); background job enriches ticket details on upload
- **User Profile** — 1:1 profile (phone, position) auto-created on registration
- **Auth** — Registration, login, logout, password reset (Laravel Fortify)
- **Notifications** — Email notification when ticket attachment processing completes
- **API Docs** — Swagger UI at `/api-docs`

## Quick Start (Docker)

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Generate an app key and set it in .env
php -r "echo 'APP_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
# Then add the output to .env as APP_KEY=...

# 3. Build and start
docker compose up --build
```

The app will be available at **http://localhost:8080**.

Migrations run automatically on container start.

## Local Development

### Requirements

- PHP 8.4 + Composer
- Node.js 20+ + npm
- SQLite

### Setup

```bash
# Install dependencies
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate

# Storage symlink
php artisan storage:link

# Start Vite dev server (in a separate terminal)
npm run dev

# Start Laravel dev server
php artisan serve

# Start queue worker (in a separate terminal)
php artisan queue:work
```

App runs at **http://localhost:8000**.

## Running Tests

```bash
php artisan test
# or
./vendor/bin/pest
```

All tests use an in-memory SQLite database (no external services required).

## Architecture

### Domain Model

```
Company
  └── has many Projects
        └── has many Tickets
              ├── belongs to User
              └── has one TicketDetail  ← auto-created on Ticket::created

User
  └── has one UserProfile  ← auto-created on User::created
```

### Background Job

When a ticket is created with a `.json` or `.txt` attachment, `ProcessTicketAttachment` is dispatched to the `database` queue. It:

1. Reads the attachment from storage
2. Stores the raw content as `enriched_data` on `TicketDetail`
3. Sends a `TicketDetailEnriched` mail notification to the ticket owner

### Shared Inertia Props

Every page receives the following props via `HandleInertiaRequests`:

| Prop | Description |
|------|-------------|
| `auth.user` | `{ id, name, email }` — sensitive fields excluded |
| `flash.success` | Success message from the last redirect |
| `flash.error` | Error message from the last redirect |
| `name` | Application name |
| `sidebarOpen` | Sidebar state from cookie |

## API Documentation

Swagger UI is available at:

```
http://localhost:8080/api-docs
```

The raw OpenAPI 3.0 spec is at `public/api-docs/openapi.yaml`.

## Environment Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_KEY` | — | **Required.** 32-byte base64 key |
| `APP_ENV` | `local` | `local` / `production` |
| `APP_DEBUG` | `true` | Show detailed errors |
| `DB_CONNECTION` | `sqlite` | Database driver |
| `DB_DATABASE` | `database/database.sqlite` | SQLite path (ignored for other drivers) |
| `QUEUE_CONNECTION` | `database` | `sync` / `database` / `redis` |
| `MAIL_MAILER` | `log` | Mail driver (`log` writes to `storage/logs`) |

## Security

- Passwords hashed with **bcrypt** via Laravel's default hasher
- Only `id`, `name`, `email` are shared to the frontend — no tokens or sensitive fields
- All DB queries go through Eloquent (parameterised, no raw interpolation)
- CSRF protection on all state-changing routes
- File uploads restricted to `.json` and `.txt` mime types

## Project Structure

```
app/
  Http/Controllers/     # CompanyController, ProjectController, TicketController, UserProfileController
  Jobs/                 # ProcessTicketAttachment
  Models/               # Company, Project, Ticket, TicketDetail, User, UserProfile
  Notifications/        # TicketDetailEnriched
resources/
  js/
    pages/              # Companies/, Projects/, Tickets/, Profile/
    components/         # AppSidebar, AppToast, ui/
    layouts/            # AppSidebarLayout
  views/
    errors/             # 403, 404, 419, 500 friendly error pages
database/
  migrations/
docker/
  supervisord.conf
  start.sh
public/
  api-docs/             # openapi.yaml + Swagger UI
tests/
  Feature/              # CompanyTest, ProjectTest, TicketTest, UserProfileTest,
                        # ProcessTicketAttachmentTest, RelationshipTest
```
