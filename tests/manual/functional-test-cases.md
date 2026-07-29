# Functional Test Cases

| Test Case ID | Related Requirement | Preconditions | Test Steps | Expected Result | Priority |
|---|---|---|---|---|---|
| FTC-001 | Project Bootstrap | Repository is checked out on the feature branch. | Open `composer.json`; verify project metadata and package type. | Composer project metadata exists and package type is `project`. | High |
| FTC-002 | Project Bootstrap | PHP is available locally. | Run `composer validate --strict`. | Composer configuration is valid or only reports environment-related dependency availability issues. | High |
| FTC-003 | Project Bootstrap | Repository files are available. | Inspect `composer.json` `require` section. | PHP `^8.2` and required Symfony packages are declared. | High |
| FTC-004 | Project Bootstrap | Repository files are available. | Inspect `composer.json` `autoload` section. | `App\\` is mapped to `src/`. | High |
| FTC-005 | Project Bootstrap | Repository files are available. | Inspect `composer.json` `autoload-dev` section. | `App\\Tests\\` is mapped to `tests/`. | Medium |
| FTC-006 | Project Bootstrap | Dependencies can be installed. | Run `composer install`. | Dependencies install successfully and autoload files are generated. | High |
| FTC-007 | Project Bootstrap | Composer install completed. | Run `composer dump-autoload`. | Autoload generation completes without errors. | High |
| FTC-008 | Kernel Bootstrap | Repository files are available. | Open `src/Kernel.php`; verify namespace and class declaration. | Kernel is declared as `App\\Kernel` and extends Symfony base kernel. | High |
| FTC-009 | Kernel Bootstrap | Repository files are available. | Inspect `configureContainer()` in `src/Kernel.php`. | Kernel imports package configuration and `config/services.yaml`. | High |
| FTC-010 | Kernel Bootstrap | Repository files are available. | Inspect `configureRoutes()` in `src/Kernel.php`. | Kernel imports `config/routes.yaml`. | High |
| FTC-011 | Runtime Bootstrap | Repository files are available. | Verify `public/index.php` exists and references `App\\Kernel`. | Front controller can bootstrap the Symfony kernel entry point. | High |
| FTC-012 | Console Bootstrap | Repository files are available. | Verify `bin/console` exists and references `App\\Kernel`. | Console bootstrap entry point is present. | Medium |
| FTC-013 | Service Configuration | Repository files are available. | Inspect `config/services.yaml`. | Service configuration file is present for future autowiring/autoconfiguration. | Medium |
| FTC-014 | Milestone Boundary | Repository files are available. | Review tree for feature controllers/entities/migrations introduced by this milestone. | No feature-layer implementation is expected in this bootstrap milestone. | Medium |
