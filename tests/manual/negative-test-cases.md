# Negative Test Cases

| Test Case ID | Related Requirement | Preconditions | Test Steps | Expected Result | Priority |
|---|---|---|---|---|---|
| NTC-001 | Project Bootstrap | Local PHP version is below 8.2. | Run `composer install`. | Install fails or reports PHP version incompatibility. | High |
| NTC-002 | Project Bootstrap | `composer.json` is edited with invalid JSON in a test copy only. | Run `composer validate --strict` against the invalid copy. | Composer validation fails with parse/format errors. | High |
| NTC-003 | Autoloading | `vendor/` has not been installed. | Run a Symfony console command. | Command fails with dependency/autoload error until `composer install` is completed. | Medium |
| NTC-004 | Kernel Bootstrap | `config/services.yaml` is unavailable in a test copy only. | Attempt Symfony kernel boot. | Kernel boot fails because required service configuration cannot be imported. | Medium |
| NTC-005 | Kernel Bootstrap | `config/routes.yaml` is unavailable in a test copy only. | Attempt Symfony route loading/kernel boot. | Kernel reports missing route configuration. | Medium |
| NTC-006 | Runtime Bootstrap | Required Symfony packages are not installed. | Run `php bin/console about`. | Console command fails due to missing dependencies. | Medium |
| NTC-007 | Milestone Boundary | Tester attempts to access planned feature APIs. | Request planned endpoints such as `/api/pim/employees/search`. | Endpoint should not be treated as implemented in this milestone. | Low |
