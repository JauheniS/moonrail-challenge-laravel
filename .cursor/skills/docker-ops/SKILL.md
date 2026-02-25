---
name: docker-ops
description: Manage and execute commands within the project's Docker environment. Use when running artisan commands, composer, migrations, or tests.
---

# Docker Operations Skill

## Critical Rule
**NEVER** run PHP, Composer, or Artisan commands directly on the host. **ALWAYS** use the Docker container.

## Execution Patterns
- **Artisan**: `docker compose exec app php artisan <command>`
- **Composer**: `docker compose exec app composer <command>`
- **Tests**: `docker compose exec app php artisan test`
- **Shell**: `docker compose exec app bash`

## Makefile Shortcuts
Prefer these shortcuts when available:
- `make optimize`: Refresh Laravel cache.
- `make composer-install`: Install dependencies.
- `make rebuild-restart`: Full environment reset.

## Environment Info
- **App Container**: `app` (PHP 8.1 FPM)
- **Redis Container**: `redis` (Port 6389)
- **Working Directory**: `/var/www` inside the container.
