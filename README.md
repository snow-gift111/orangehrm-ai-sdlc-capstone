# OrangeHRM Employee Management Enhancement

## Project Overview
This repository contains the bootstrap foundation for an OrangeHRM-based Employee Management System enhancement. The approved implementation milestone initializes a Symfony + React project structure that will support future employee management enhancements such as RBAC, audit history, advanced search, duplicate prevention, bulk import, and authentication security controls.

Current milestone: **Project Bootstrap**. No business features, APIs, database schema, or UI screens are implemented yet.

## Features
Implemented in this milestone:

- Symfony 6.4 backend project bootstrap
- Composer dependency configuration for PHP 8.1+, Symfony, Doctrine ORM, Security, Validator, Serializer, and YAML support
- React/Vite frontend package bootstrap
- Symfony kernel setup
- Bundle registration
- Service autowiring/autoconfiguration
- Attribute-based controller route loading
- Public front controller
- Symfony console entry point
- Example environment configuration for MySQL-compatible databases

Planned for later milestones:

- Role-based PIM access control
- Employee audit history
- Advanced employee search with sorting and pagination
- Duplicate employee prevention
- Bulk employee import with validation summary
- Session timeout, password policy, and account lockout controls

## Technology Stack

| Layer | Technology |
|---|---|
| Backend Language | PHP 8.1+ |
| Backend Framework | Symfony 6.4-based architecture |
| Frontend | React 18, Vite 5 |
| Database Target | MySQL 8.x / MariaDB-compatible |
| ORM | Doctrine ORM |
| Package Management | Composer, npm |

## Project Structure

```text
.
├── bin/console              # Symfony console entry point
├── config/
│   ├── bundles.php          # Symfony bundle registration
│   ├── routes.yaml          # Attribute-based controller route loading
│   └── services.yaml        # Service autowiring/autoconfiguration
├── public/index.php         # Public HTTP front controller
├── src/Kernel.php           # Symfony application kernel
├── composer.json            # PHP/Symfony dependencies and autoloading
├── package.json             # React/Vite frontend dependencies
└── .env.example             # Example local environment variables
```

## Setup Instructions

### Prerequisites

- PHP 8.1 or later
- Composer 2.x
- Node.js 18+ and npm
- MySQL 8.x or MariaDB-compatible database for future database-enabled milestones

### Installation

```bash
git checkout feature/employee-management-bootstrap-20260729-193800
composer install --no-interaction --prefer-dist
npm install
cp .env.example .env
```

Update `.env` with local values as needed. The values in `.env.example` are placeholders for local development only.

## Run Instructions

### Backend console check

```bash
php bin/console list
```

### PHP syntax checks

```bash
php -l src/Kernel.php
php -l public/index.php
php -l bin/console
```

### Frontend development server

```bash
npm run dev
```

Note: No React application entry files or UI screens are implemented in the current milestone, so frontend runtime behavior is limited to dependency/bootstrap readiness.

## API Summary
No API endpoints are implemented in the current Project Bootstrap milestone. Planned REST-style endpoints will be added in later implementation milestones.
