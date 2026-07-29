# Setup Guide

## Prerequisites

- PHP 8.2 or later
- Composer
- Git
- Symfony CLI recommended for local development, but not required

## Installation

Clone the repository and checkout the current working branch:

```bash
git clone https://github.com/snow-gift111/orangehrm-ai-sdlc-capstone.git
cd orangehrm-ai-sdlc-capstone
git checkout feature/employee-management-enhancement-20260729-193900
```

Install PHP dependencies:

```bash
composer install
```

Create a local environment file if needed:

```bash
cp .env.example .env
```

## Build

No separate build step is required for the current bootstrap milestone.

Validate Composer autoloading and Symfony bootstrap with:

```bash
composer dump-autoload
php bin/console
```

## Run

Using Symfony CLI:

```bash
symfony server:start
```

Using PHP's built-in server:

```bash
php -S 127.0.0.1:8000 -t public
```

Open the local application URL shown by the selected server command.
