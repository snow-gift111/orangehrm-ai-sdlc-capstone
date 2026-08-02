<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260802120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create append-only employee audit record persistence table.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
CREATE TABLE employee_audit_record (
    id INT AUTO_INCREMENT NOT NULL,
    employee_id INT NOT NULL,
    action_type VARCHAR(32) NOT NULL,
    changed_field VARCHAR(128) DEFAULT NULL,
    previous_value LONGTEXT DEFAULT NULL,
    new_value LONGTEXT DEFAULT NULL,
    actor_user_id INT DEFAULT NULL,
    actor_display_name VARCHAR(255) DEFAULT NULL,
    event_timestamp DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    source_context VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    INDEX idx_employee_audit_employee_timestamp (employee_id, event_timestamp),
    INDEX idx_employee_audit_employee_action (employee_id, action_type),
    INDEX idx_employee_audit_employee_field (employee_id, changed_field),
    INDEX idx_employee_audit_employee_actor (employee_id, actor_user_id),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE employee_audit_record');
    }
}
