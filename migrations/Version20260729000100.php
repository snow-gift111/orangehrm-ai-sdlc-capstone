<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260729000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create employee and employee audit history tables for PIM audit events.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
CREATE TABLE employee (
    id BIGINT AUTO_INCREMENT NOT NULL,
    employee_id VARCHAR(100) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) DEFAULT NULL,
    last_name VARCHAR(100) NOT NULL,
    job_title VARCHAR(150) DEFAULT NULL,
    employment_status VARCHAR(100) DEFAULT NULL,
    created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    UNIQUE INDEX uniq_employee_employee_id (employee_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);

        $this->addSql(<<<'SQL'
CREATE TABLE employee_audit_event (
    audit_event_id BIGINT AUTO_INCREMENT NOT NULL,
    employee_internal_id BIGINT DEFAULT NULL,
    employee_id_snapshot VARCHAR(100) NOT NULL,
    employee_name_snapshot VARCHAR(255) DEFAULT NULL,
    action_type VARCHAR(20) NOT NULL,
    changed_by_user_id BIGINT DEFAULT NULL,
    changed_by_username_snapshot VARCHAR(255) NOT NULL,
    occurred_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    summary VARCHAR(1000) DEFAULT NULL,
    request_correlation_id VARCHAR(100) DEFAULT NULL,
    created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    INDEX idx_employee_audit_event_employee_id_occurred (employee_id_snapshot, occurred_at),
    INDEX idx_employee_audit_event_internal_occurred (employee_internal_id, occurred_at),
    INDEX idx_employee_audit_event_action_type (action_type),
    INDEX idx_employee_audit_event_changed_by (changed_by_user_id),
    INDEX idx_employee_audit_event_occurred_at (occurred_at),
    CONSTRAINT chk_employee_audit_event_action_type CHECK (action_type IN ('Create', 'Update', 'Delete')),
    PRIMARY KEY(audit_event_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);

        $this->addSql(<<<'SQL'
CREATE TABLE employee_audit_change_detail (
    audit_change_detail_id BIGINT AUTO_INCREMENT NOT NULL,
    audit_event_id BIGINT NOT NULL,
    field_name VARCHAR(150) NOT NULL,
    field_label VARCHAR(255) NOT NULL,
    previous_value LONGTEXT DEFAULT NULL,
    new_value LONGTEXT DEFAULT NULL,
    display_order INT DEFAULT NULL,
    created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    INDEX idx_employee_audit_detail_event (audit_event_id),
    INDEX idx_employee_audit_detail_event_order (audit_event_id, display_order),
    INDEX idx_employee_audit_detail_field (field_name),
    PRIMARY KEY(audit_change_detail_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);

        $this->addSql('ALTER TABLE employee_audit_change_detail ADD CONSTRAINT fk_employee_audit_detail_event FOREIGN KEY (audit_event_id) REFERENCES employee_audit_event (audit_event_id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE employee_audit_change_detail DROP FOREIGN KEY fk_employee_audit_detail_event');
        $this->addSql('DROP TABLE employee_audit_change_detail');
        $this->addSql('DROP TABLE employee_audit_event');
        $this->addSql('DROP TABLE employee');
    }
}
