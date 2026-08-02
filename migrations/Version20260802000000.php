<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260802000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create immutable employee audit record table for PIM employee audit history.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
CREATE TABLE employee_audit_record (
    audit_id INT UNSIGNED AUTO_INCREMENT NOT NULL,
    employee_id INT UNSIGNED DEFAULT NULL,
    employee_reference VARCHAR(100) NOT NULL,
    event_type VARCHAR(20) NOT NULL,
    field_name VARCHAR(100) DEFAULT NULL,
    previous_value LONGTEXT DEFAULT NULL,
    new_value LONGTEXT DEFAULT NULL,
    is_sensitive TINYINT(1) DEFAULT 0 NOT NULL,
    actor_user_id INT UNSIGNED DEFAULT NULL,
    actor_display_name VARCHAR(150) NOT NULL,
    source_module VARCHAR(50) NOT NULL,
    occurred_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    INDEX idx_employee_audit_employee_occurred (employee_id, occurred_at),
    INDEX idx_employee_audit_event_type (event_type),
    INDEX idx_employee_audit_actor (actor_user_id),
    INDEX idx_employee_audit_occurred_at (occurred_at),
    PRIMARY KEY(audit_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE employee_audit_record');
    }
}
