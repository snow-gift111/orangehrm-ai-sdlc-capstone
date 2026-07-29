# OrangeHRM Employee Management Enhancement

## Project Overview
This repository contains the Symfony-based bootstrap for an OrangeHRM-inspired Employee Management System enhancement. The approved product direction is to improve Personnel Information Management (PIM) with stronger access control, auditability, employee search, duplicate prevention, authentication security, and bulk import capabilities.

The current implementation milestone is **Project Bootstrap**. It prepares the PHP/Symfony foundation only; feature-layer implementation is planned for later milestones.

## Features
Implemented in the current milestone:

- Symfony-compatible PHP dependency configuration.
- PSR-4 autoloading for application code under `src/`.
- Symfony kernel wiring for package, service, and route configuration.
- Existing web and console bootstrap entry points retained.

Planned but not implemented in this milestone:

- Role-based PIM access control.
- Employee audit history.
- Advanced employee search with pagination and sorting.
- Duplicate employee prevention.
- Authentication security controls.
- Bulk employee import foundation.

## Technology Stack

- PHP 8.2+
- Symfony Framework Bundle 7.1
- Symfony Runtime, Console, Validator, YAML, Dotenv
- Composer
- Planned architecture alignment: OrangeHRM-style Symfony backend and Vue.js UI
- Planned database: MySQL/MariaDB with Doctrine ORM

## Project Structure

```text
.
├── bin/                  # Console entry point
├── config/               # Symfony routes, services, and bundle configuration
├── public/               # Web front controller
├── src/                  # Application source code
├── tests/                # QA documentation and future test assets
├── composer.json         # PHP dependencies and autoloading
├── package.json          # Frontend/tooling placeholder
└── README.md
```

## Setup Instructions

1. Install PHP 8.2 or later.
2. Install Composer.
3. Clone the repository and checkout the working branch.
4. Install dependencies:

```bash
composer install
```

5. Copy environment defaults if needed:

```bash
cp .env.example .env
```

## Run Instructions

Run the Symfony development server if available:

```bash
symfony server:start
```

Or use PHP's built-in server for local bootstrap validation:

```bash
php -S 127.0.0.1:8000 -t public
```

Console bootstrap can be checked with:

```bash
php bin/console
```

## API Summary
No APIs are implemented in the current milestone.

See `docs/` for project, technical, setup, API, user, limitation, and future enhancement summaries.
