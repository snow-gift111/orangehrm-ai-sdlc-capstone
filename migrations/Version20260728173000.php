<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260728173000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Leave Balance Alert persistence tables, constraints, and indexes.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE leave_type (
            leave_type_id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(100) NOT NULL,
            unit_of_measure VARCHAR(20) NOT NULL,
            is_active TINYINT(1) DEFAULT 1 NOT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE INDEX uq_leave_type_name (name),
            INDEX idx_leave_type_active (is_active),
            PRIMARY KEY(leave_type_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE leave_balance (
            leave_balance_id INT AUTO_INCREMENT NOT NULL,
            employee_id INT NOT NULL,
            leave_type_id INT NOT NULL,
            balance_period VARCHAR(32) NOT NULL,
            entitled_amount NUMERIC(10, 2) DEFAULT '0.00' NOT NULL,
            used_amount NUMERIC(10, 2) DEFAULT '0.00' NOT NULL,
            pending_amount NUMERIC(10, 2) DEFAULT '0.00' NOT NULL,
            available_balance NUMERIC(10, 2) NOT NULL,
            unit_of_measure VARCHAR(20) NOT NULL,
            is_active TINYINT(1) DEFAULT 1 NOT NULL,
            last_calculated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX idx_leave_balance_employee_type (employee_id, leave_type_id),
            INDEX idx_leave_balance_type_available (leave_type_id, available_balance),
            INDEX idx_leave_balance_employee_active (employee_id, is_active),
            UNIQUE INDEX uq_leave_balance_employee_type_period_active (employee_id, leave_type_id, balance_period, is_active),
            PRIMARY KEY(leave_balance_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE leave_alert_threshold (
            threshold_id INT AUTO_INCREMENT NOT NULL,
            leave_type_id INT NOT NULL,
            threshold_value NUMERIC(10, 2) NOT NULL,
            unit_of_measure VARCHAR(20) NOT NULL,
            is_enabled TINYINT(1) DEFAULT 1 NOT NULL,
            created_by_user_id INT DEFAULT NULL,
            updated_by_user_id INT DEFAULT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            UNIQUE INDEX uq_leave_alert_threshold_leave_type (leave_type_id),
            INDEX idx_leave_alert_threshold_enabled (is_enabled),
            INDEX idx_leave_alert_threshold_type_enabled (leave_type_id, is_enabled),
            INDEX idx_leave_alert_threshold_created_by (created_by_user_id),
            INDEX idx_leave_alert_threshold_updated_by (updated_by_user_id),
            PRIMARY KEY(threshold_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE leave_balance_alert (
            alert_id INT AUTO_INCREMENT NOT NULL,
            employee_id INT NOT NULL,
            leave_type_id INT NOT NULL,
            alert_type VARCHAR(40) NOT NULL,
            alert_status VARCHAR(20) NOT NULL,
            current_balance NUMERIC(10, 2) NOT NULL,
            threshold_value NUMERIC(10, 2) DEFAULT NULL,
            requested_duration NUMERIC(10, 2) DEFAULT NULL,
            condition_key VARCHAR(128) NOT NULL,
            active_guard VARCHAR(128) NOT NULL,
            generated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            resolved_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX idx_lba_employee_status (employee_id, alert_status),
            INDEX idx_lba_employee_type_status (employee_id, leave_type_id, alert_type, alert_status),
            INDEX idx_lba_condition_status (condition_key, alert_status),
            INDEX idx_lba_leave_type (leave_type_id),
            UNIQUE INDEX uq_lba_active_condition_guard (condition_key, active_guard),
            PRIMARY KEY(alert_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE leave_alert_event (
            event_id INT AUTO_INCREMENT NOT NULL,
            alert_id INT DEFAULT NULL,
            employee_id INT NOT NULL,
            leave_type_id INT NOT NULL,
            alert_type VARCHAR(40) NOT NULL,
            balance_value NUMERIC(10, 2) NOT NULL,
            threshold_value NUMERIC(10, 2) DEFAULT NULL,
            requested_duration NUMERIC(10, 2) DEFAULT NULL,
            event_source VARCHAR(60) NOT NULL,
            generated_for_user_id INT DEFAULT NULL,
            event_timestamp DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX idx_lae_employee_timestamp (employee_id, event_timestamp),
            INDEX idx_lae_type_timestamp (leave_type_id, event_timestamp),
            INDEX idx_lae_alert_type_timestamp (alert_type, event_timestamp),
            INDEX idx_lae_generated_for_user (generated_for_user_id),
            INDEX idx_lae_alert (alert_id),
            PRIMARY KEY(event_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql("CREATE TABLE leave_threshold_audit (
            audit_id INT AUTO_INCREMENT NOT NULL,
            threshold_id INT DEFAULT NULL,
            leave_type_id INT NOT NULL,
            previous_threshold_value NUMERIC(10, 2) DEFAULT NULL,
            new_threshold_value NUMERIC(10, 2) DEFAULT NULL,
            previous_enabled TINYINT(1) DEFAULT NULL,
            new_enabled TINYINT(1) DEFAULT NULL,
            changed_by_user_id INT NOT NULL,
            changed_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            change_source VARCHAR(60) DEFAULT 'ADMIN_UI' NOT NULL,
            created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
            INDEX idx_lta_leave_type_changed_at (leave_type_id, changed_at),
            INDEX idx_lta_changed_by_changed_at (changed_by_user_id, changed_at),
            INDEX idx_lta_threshold (threshold_id),
            PRIMARY KEY(audit_id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");

        $this->addSql('ALTER TABLE leave_balance ADD CONSTRAINT FK_leave_balance_employee FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE leave_balance ADD CONSTRAINT FK_leave_balance_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type (leave_type_id)');
        $this->addSql('ALTER TABLE leave_alert_threshold ADD CONSTRAINT FK_leave_alert_threshold_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type (leave_type_id)');
        $this->addSql('ALTER TABLE leave_alert_threshold ADD CONSTRAINT FK_leave_alert_threshold_created_by FOREIGN KEY (created_by_user_id) REFERENCES app_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE leave_alert_threshold ADD CONSTRAINT FK_leave_alert_threshold_updated_by FOREIGN KEY (updated_by_user_id) REFERENCES app_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE leave_balance_alert ADD CONSTRAINT FK_lba_employee FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE leave_balance_alert ADD CONSTRAINT FK_lba_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type (leave_type_id)');
        $this->addSql('ALTER TABLE leave_alert_event ADD CONSTRAINT FK_lae_alert FOREIGN KEY (alert_id) REFERENCES leave_balance_alert (alert_id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE leave_alert_event ADD CONSTRAINT FK_lae_employee FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE leave_alert_event ADD CONSTRAINT FK_lae_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type (leave_type_id)');
        $this->addSql('ALTER TABLE leave_alert_event ADD CONSTRAINT FK_lae_generated_for_user FOREIGN KEY (generated_for_user_id) REFERENCES app_user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE leave_threshold_audit ADD CONSTRAINT FK_lta_threshold FOREIGN KEY (threshold_id) REFERENCES leave_alert_threshold (threshold_id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE leave_threshold_audit ADD CONSTRAINT FK_lta_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type (leave_type_id)');
        $this->addSql('ALTER TABLE leave_threshold_audit ADD CONSTRAINT FK_lta_changed_by FOREIGN KEY (changed_by_user_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE leave_threshold_audit DROP FOREIGN KEY FK_lta_changed_by');
        $this->addSql('ALTER TABLE leave_threshold_audit DROP FOREIGN KEY FK_lta_leave_type');
        $this->addSql('ALTER TABLE leave_threshold_audit DROP FOREIGN KEY FK_lta_threshold');
        $this->addSql('ALTER TABLE leave_alert_event DROP FOREIGN KEY FK_lae_generated_for_user');
        $this->addSql('ALTER TABLE leave_alert_event DROP FOREIGN KEY FK_lae_leave_type');
        $this->addSql('ALTER TABLE leave_alert_event DROP FOREIGN KEY FK_lae_employee');
        $this->addSql('ALTER TABLE leave_alert_event DROP FOREIGN KEY FK_lae_alert');
        $this->addSql('ALTER TABLE leave_balance_alert DROP FOREIGN KEY FK_lba_leave_type');
        $this->addSql('ALTER TABLE leave_balance_alert DROP FOREIGN KEY FK_lba_employee');
        $this->addSql('ALTER TABLE leave_alert_threshold DROP FOREIGN KEY FK_leave_alert_threshold_updated_by');
        $this->addSql('ALTER TABLE leave_alert_threshold DROP FOREIGN KEY FK_leave_alert_threshold_created_by');
        $this->addSql('ALTER TABLE leave_alert_threshold DROP FOREIGN KEY FK_leave_alert_threshold_leave_type');
        $this->addSql('ALTER TABLE leave_balance DROP FOREIGN KEY FK_leave_balance_leave_type');
        $this->addSql('ALTER TABLE leave_balance DROP FOREIGN KEY FK_leave_balance_employee');
        $this->addSql('DROP TABLE leave_threshold_audit');
        $this->addSql('DROP TABLE leave_alert_event');
        $this->addSql('DROP TABLE leave_balance_alert');
        $this->addSql('DROP TABLE leave_alert_threshold');
        $this->addSql('DROP TABLE leave_balance');
        $this->addSql('DROP TABLE leave_type');
    }
}
