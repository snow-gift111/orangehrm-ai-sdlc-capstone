# User Guide

## Current Milestone
The current milestone provides project bootstrap only. No user-facing Employee Management functionality is implemented yet.

## Available Usage
Technical users can validate that the application bootstrap is available by installing dependencies and starting the local Symfony/PHP server.

```bash
composer install
php bin/console
php -S 127.0.0.1:8000 -t public
```

## Employee Management Functionality
The following user-facing capabilities are approved for future implementation but are not available in the current milestone:

- Role-based PIM access control.
- Employee audit history viewing.
- Advanced employee search.
- Duplicate employee prevention.
- Authentication security controls.
- Bulk employee import.

Until those milestones are implemented, there are no end-user workflows to document for HR administrators, HR managers, or HR users.
