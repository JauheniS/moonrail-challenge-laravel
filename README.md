### Requirements
- PHP = 8.1
- Laravel >= 9.14
- SQLite database

 This challenge does not require any additional library. DO NOT MODIFY the composer.json or composer.lock file as that may result in a test failure.
 The project already contain a sample SQLite database at /database/database.sqlite. Please don´t change the database structure by creating a seed or migration file because this may also result in a test failure.

### Installation

- Run composer install command: `composer install`

- To serve the api run the command: `php artisan serve --port=3000`

- To run the tests use the command: `php artisan test`

---

## Project Task: Football Team Selection API

The goal of this project was to refactor and implement a robust RESTful API for managing football players and selecting optimal teams based on specific requirements.

### Objectives:
1.  **Player Management (CRUD):** Implement endpoints to create, update, delete, and list players with their associated skills.
2.  **Advanced Team Selection:** Create a sophisticated algorithm to select the best available players for a requested team composition, prioritizing specific skills and overall quality.
3.  **Enterprise Standards:** Refactor the codebase to follow SOLID principles, use modern PHP 8.1 features (Enums, Type Safety), and ensure high test coverage.
4.  **Performance & Scalability:** Optimize database queries to avoid N+1 problems and ensure the selection algorithm handles large datasets efficiently.

## Changelist (a642521b → d14d40c3)

### Core Architecture & Refactoring
- **Service-Controller-Model Pattern:** Extracted business logic from controllers into dedicated services (`PlayerService`, `TeamSelectionService`).
- **PHP 8.1 Enums:** Introduced `PlayerPosition` and `PlayerSkill` Enums for strict type safety across the application.
- **Form Request Validation:** Moved complex validation logic into specialized Request classes (`StorePlayerRequest`, `TeamProcessRequest`, etc.).
- **API Resources:** Implemented `PlayerResource` and `PlayerSkillResource` for consistent JSON transformation.
- **Custom Middleware:** Added `BearerTokenMiddleware` for secure API access.

### Features & Improvements
- **Optimized Selection Algorithm:** Implemented a multi-tier sorting strategy (Skill Match > Skill Value > Max Skill > ID) for deterministic and optimal team selection.
- **Batch Candidate Loading:** Reduced database round-trips by aggregating requirements and fetching candidates in optimized batches.
- **ACID Compliance:** Wrapped player creation and skill updates in database transactions to ensure data integrity.
- **Database Indexing:** Added migrations to include performance-boosting indexes on frequently searched columns (`position`, `skill`, `value`).
- **Intelligent Skill Syncing:** Refactored player updates to efficiently sync skills without unnecessary deletions.

### DevOps & Tooling
- **Dockerization:** Added a full Docker environment (`Dockerfile`, `docker-compose.yaml`) for consistent development across platforms.
- **Makefile:** Provided a `Makefile` for common development tasks (install, test, start).
- **AI-Enhanced Development:** Integrated Cursor rules and agent skills for persistent AI guidance and standardized development practices.

### Quality Assurance
- **Comprehensive Test Suite:** Added over 20 new feature and unit tests covering:
    - Player CRUD operations.
    - Complex team selection logic and edge cases.
    - Validation rules and error handling.
    - Performance and duplication constraints.
- **Linter & Static Analysis:** Ensured all code adheres to PSR-12 and Laravel coding standards.
