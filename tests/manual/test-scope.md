# Test Scope

## Current Implementation Milestone
Project Bootstrap for the Symfony-based OrangeHRM Employee Management enhancement.

## Features Under Test
- PHP/Symfony project dependency bootstrap via `composer.json`.
- PSR-4 application autoloading for `App\\` under `src/`.
- Symfony kernel loading package, service, and route configuration.
- Existing front controller and console bootstrap compatibility.
- Dependency injection configuration availability for future services.

## Features Excluded
- RBAC, employee audit, duplicate detection, advanced search, authentication security, bulk import, APIs, database migrations, and UI screens.
- Browser automation and end-to-end workflows.

## Test Objectives
- Confirm the project bootstrap is syntactically valid and installable.
- Confirm Symfony kernel wiring is aligned to the approved architecture foundation.
- Confirm no feature-layer behavior is expected or tested in this milestone.
