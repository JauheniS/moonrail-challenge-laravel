---
name: laravel-dev
description: Expert Laravel development following the Service-Controller-Model pattern. Use when creating or modifying routes, controllers, services, models, or API resources in this project.
---

# Laravel Development Skill

## Core Workflow
Follow this sequence for new features:
1. **Route**: Define in `routes/api.php`.
2. **Request**: Create/Update validation in `app/Http/Requests`.
3. **Controller**: Handle request and call Service in `app/Http/Controllers`.
4. **Service**: Implement business logic and DB transactions in `app/Services`.
5. **Model**: Define relationships and casts in `app/Models`.
6. **Resource**: Transform output in `app/Http/Resources`.

## Standards
- **Thin Controllers**: No business logic in controllers.
- **Service Layer**: All logic must be in services.
- **Type Hinting**: Use strict typing for parameters and return values.
- **Transactions**: Wrap multi-step DB operations in `DB::transaction`.
- **Enums**: Use `App\Enums` for fixed values.

## Common Tasks
- **Add Player Skill**: Update `PlayerService` and ensure `PlayerSkill` model is used.
- **Validation**: Check `HandlesValidationErrors` trait for custom error handling.
- **Middleware**: Use `bearer_token` for sensitive operations.
