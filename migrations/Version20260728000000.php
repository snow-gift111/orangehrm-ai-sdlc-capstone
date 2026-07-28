<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260728000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create foundational tables for Leave Balance Alert capability';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE app_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX uniq_app_user_username (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, employee_number VARCHAR(64) NOT NULL, first_name VARCHAR(120) NOT NULL, middle_name VARCHAR(120) DEFAULT NULL, last_name VARCHAR(120) NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX uniq_employee_number (employee_number), UNIQUE INDEX UNIQ_5D9F75A1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1A76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) ON DELETE SET NULL');

        $this->addSql("CREATE TABLE leave_type (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(64) NOT NULL, name VARCHAR(120) NOT NULL, description LONGTEXT DEFAULT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX uniq_leave_type_code (code), INDEX idx_leave_type_active (active), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE employee_leave_balance (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, leave_type_id INT NOT NULL, last_updated_by INT DEFAULT NULL, current_balance NUMERIC(10, 2) NOT NULL, last_updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', version INT DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX uniq_employee_leave_balance (employee_id, leave_type_id), INDEX idx_employee_leave_balance_leave_type (leave_type_id), INDEX idx_employee_leave_balance_last_updated_at (last_updated_at), INDEX IDX_4E56B42B16FE72E1 (last_updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE employee_leave_balance ADD CONSTRAINT FK_4E56B42B8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employee_leave_balance ADD CONSTRAINT FK_4E56B42B48CA4FAE FOREIGN KEY (leave_type_id) REFERENCES leave_type (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE employee_leave_balance ADD CONSTRAINT FK_4E56B42B16FE72E1 FOREIGN KEY (last_updated_by) REFERENCES app_user (id) ON DELETE SET NULL');

        $this->addSql("CREATE TABLE leave_alert_threshold (id INT AUTO_INCREMENT NOT NULL, leave_type_id INT DEFAULT NULL, created_by INT DEFAULT NULL, updated_by INT DEFAULT NULL, scope_type VARCHAR(32) NOT NULL, threshold_value NUMERIC(10, 2) NOT NULL, active TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', active_global_key TINYINT GENERATED ALWAYS AS (CASE WHEN active = 1 AND scope_type = 'GLOBAL' AND leave_type_id IS NULL THEN 1 ELSE NULL END) STORED, active_leave_type_key INT GENERATED ALWAYS AS (CASE WHEN active = 1 AND scope_type = 'LEAVE_TYPE' THEN leave_type_id ELSE NULL END) STORED, INDEX idx_leave_alert_threshold_scope_active (scope_type, active), INDEX idx_leave_alert_threshold_leave_type (leave_type_id), INDEX IDX_FD13D051DE12AB56 (created_by), INDEX IDX_FD13D05116FE72E1 (updated_by), UNIQUE INDEX uniq_active_global_threshold (active_global_key), UNIQUE INDEX uniq_active_leave_type_threshold (active_leave_type_key), CHECK (threshold_value >= 0), CHECK ((scope_type = 'GLOBAL' AND leave_type_id IS NULL) OR (scope_type = 'LEAVE_TYPE' AND leave_type_id IS NOT NULL)), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE leave_alert_threshold ADD CONSTRAINT FK_FD13D05148CA4FAE FOREIGN KEY (leave_type_id) REFERENCES leave_type (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE leave_alert_threshold ADD CONSTRAINT FK_FD13D051DE12AB56 FOREIGN KEY (created_by) REFERENCES app_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE leave_alert_threshold ADD CONSTRAINT FK_FD13D05116FE72E1 FOREIGN KEY (updated_by) REFERENCES app_user (id) ON DELETE SET NULL');

        $this->addSql("CREATE TABLE leave_balance_alert (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, leave_type_id INT NOT NULL, threshold_id INT NOT NULL, acknowledged_by INT DEFAULT NULL, alert_condition VARCHAR(32) NOT NULL, current_balance_at_alert NUMERIC(10, 2) NOT NULL, threshold_value_at_alert NUMERIC(10, 2) NOT NULL, lifecycle_status VARCHAR(32) NOT NULL, read_status VARCHAR(32) NOT NULL, acknowledgement_status VARCHAR(32) NOT NULL, alert_date DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', read_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', acknowledged_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', resolved_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', resolved_reason VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', active_duplicate_key VARCHAR(191) GENERATED ALWAYS AS (CASE WHEN lifecycle_status = 'ACTIVE' THEN CONCAT(employee_id, ':', leave_type_id, ':', alert_condition) ELSE NULL END) STORED, INDEX idx_leave_balance_alert_employee_lifecycle (employee_id, lifecycle_status), INDEX idx_leave_balance_alert_leave_type_lifecycle (leave_type_id, lifecycle_status), INDEX idx_leave_balance_alert_condition_lifecycle (alert_condition, lifecycle_status), INDEX idx_leave_balance_alert_date (alert_date), INDEX idx_leave_balance_alert_ack_lifecycle (acknowledgement_status, lifecycle_status), INDEX IDX_C8DF390FBF906B8F (threshold_id), INDEX IDX_C8DF390FBBB64630 (acknowledged_by), UNIQUE INDEX uniq_active_leave_balance_alert (active_duplicate_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE leave_balance_alert ADD CONSTRAINT FK_C8DF390F8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE leave_balance_alert ADD CONSTRAINT FK_C8DF390F48CA4FAE FOREIGN KEY (leave_type_id) REFERENCES leave_type (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE leave_balance_alert ADD CONSTRAINT FK_C8DF390FBF906B8F FOREIGN KEY (threshold_id) REFERENCES leave_alert_threshold (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE leave_balance_alert ADD CONSTRAINT FK_C8DF390FBBB64630 FOREIGN KEY (acknowledged_by) REFERENCES app_user (id) ON DELETE SET NULL');

        $this->addSql("CREATE TABLE audit_log (id INT AUTO_INCREMENT NOT NULL, changed_by INT NOT NULL, entity_type VARCHAR(120) NOT NULL, entity_id VARCHAR(120) NOT NULL, action VARCHAR(120) NOT NULL, previous_values JSON DEFAULT NULL, new_values JSON DEFAULT NULL, changed_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', correlation_id VARCHAR(120) NOT NULL, INDEX idx_audit_log_entity (entity_type, entity_id), INDEX idx_audit_log_changed_by (changed_by), INDEX idx_audit_log_changed_at (changed_at), INDEX idx_audit_log_action (action), INDEX idx_audit_log_correlation (correlation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE audit_log ADD CONSTRAINT FK_8FCA7F86DE12AB56 FOREIGN KEY (changed_by) REFERENCES app_user (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE audit_log DROP FOREIGN KEY FK_8FCA7F86DE12AB56');
        $this->addSql('ALTER TABLE leave_balance_alert DROP FOREIGN KEY FK_C8DF390F8C03F15C');
        $this->addSql('ALTER TABLE leave_balance_alert DROP FOREIGN KEY FK_C8DF390F48CA4FAE');
        $this->addSql('ALTER TABLE leave_balance_alert DROP FOREIGN KEY FK_C8DF390FBF906B8F');
        $this->addSql('ALTER TABLE leave_balance_alert DROP FOREIGN KEY FK_C8DF390FBBB64630');
        $this->addSql('ALTER TABLE leave_alert_threshold DROP FOREIGN KEY FK_FD13D05148CA4FAE');
        $this->addSql('ALTER TABLE leave_alert_threshold DROP FOREIGN KEY FK_FD13D051DE12AB56');
        $this->addSql('ALTER TABLE leave_alert_threshold DROP FOREIGN KEY FK_FD13D05116FE72E1');
        $this->addSql('ALTER TABLE employee_leave_balance DROP FOREIGN KEY FK_4E56B42B8C03F15C');
        $this->addSql('ALTER TABLE employee_leave_balance DROP FOREIGN KEY FK_4E56B42B48CA4FAE');
        $this->addSql('ALTER TABLE employee_leave_balance DROP FOREIGN KEY FK_4E56B42B16FE72E1');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1A76ED395');
        $this->addSql('DROP TABLE audit_log');
        $this->addSql('DROP TABLE leave_balance_alert');
        $this->addSql('DROP TABLE leave_alert_threshold');
        $this->addSql('DROP TABLE employee_leave_balance');
        $this->addSql('DROP TABLE leave_type');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE app_user');
    }
}
