<?php

declare(strict_types=1);

namespace App\Pim\EmployeeAudit\Entity;

use App\Pim\EmployeeAudit\Model\EmployeeAuditEventType;
use App\Pim\EmployeeAudit\Repository\EmployeeAuditRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeAuditRepository::class)]
#[ORM\Table(name: 'employee_audit_record')]
#[ORM\Index(columns: ['employee_id', 'occurred_at'], name: 'idx_employee_audit_employee_occurred')]
#[ORM\Index(columns: ['event_type'], name: 'idx_employee_audit_event_type')]
#[ORM\Index(columns: ['actor_user_id'], name: 'idx_employee_audit_actor')]
#[ORM\Index(columns: ['occurred_at'], name: 'idx_employee_audit_occurred_at')]
final class EmployeeAuditRecord
{
    public const SOURCE_MODULE_PIM = 'PIM';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'audit_id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $auditId = null;

    #[ORM\Column(name: 'employee_id', type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
    private ?int $employeeId;

    #[ORM\Column(name: 'employee_reference', type: Types::STRING, length: 100)]
    private string $employeeReference;

    #[ORM\Column(name: 'event_type', type: Types::STRING, length: 20, enumType: EmployeeAuditEventType::class)]
    private EmployeeAuditEventType $eventType;

    #[ORM\Column(name: 'field_name', type: Types::STRING, length: 100, nullable: true)]
    private ?string $fieldName;

    #[ORM\Column(name: 'previous_value', type: Types::TEXT, nullable: true)]
    private ?string $previousValue;

    #[ORM\Column(name: 'new_value', type: Types::TEXT, nullable: true)]
    private ?string $newValue;

    #[ORM\Column(name: 'is_sensitive', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $sensitive;

    #[ORM\Column(name: 'actor_user_id', type: Types::INTEGER, nullable: true, options: ['unsigned' => true])]
    private ?int $actorUserId;

    #[ORM\Column(name: 'actor_display_name', type: Types::STRING, length: 150)]
    private string $actorDisplayName;

    #[ORM\Column(name: 'source_module', type: Types::STRING, length: 50)]
    private string $sourceModule;

    #[ORM\Column(name: 'occurred_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $occurredAt;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    private function __construct(
        ?int $employeeId,
        string $employeeReference,
        EmployeeAuditEventType $eventType,
        ?string $fieldName,
        ?string $previousValue,
        ?string $newValue,
        bool $sensitive,
        ?int $actorUserId,
        string $actorDisplayName,
        string $sourceModule,
        \DateTimeImmutable $occurredAt,
    ) {
        $employeeReference = trim($employeeReference);
        $actorDisplayName = trim($actorDisplayName);
        $sourceModule = trim($sourceModule);
        $fieldName = $fieldName === null ? null : trim($fieldName);

        if ($employeeReference === '') {
            throw new \InvalidArgumentException('Employee audit record requires an employee reference.');
        }

        if ($actorDisplayName === '') {
            throw new \InvalidArgumentException('Employee audit record requires an actor display name.');
        }

        if ($sourceModule === '') {
            throw new \InvalidArgumentException('Employee audit record requires a source module.');
        }

        if ($eventType === EmployeeAuditEventType::Update && $fieldName === null) {
            throw new \InvalidArgumentException('Employee update audit records require a changed field name.');
        }

        $this->employeeId = $employeeId;
        $this->employeeReference = mb_substr($employeeReference, 0, 100);
        $this->eventType = $eventType;
        $this->fieldName = $fieldName === null ? null : mb_substr($fieldName, 0, 100);
        $this->previousValue = $previousValue;
        $this->newValue = $newValue;
        $this->sensitive = $sensitive;
        $this->actorUserId = $actorUserId;
        $this->actorDisplayName = mb_substr($actorDisplayName, 0, 150);
        $this->sourceModule = mb_substr($sourceModule, 0, 50);
        $this->occurredAt = $occurredAt;
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public static function create(
        ?int $employeeId,
        string $employeeReference,
        ?int $actorUserId,
        string $actorDisplayName,
        \DateTimeImmutable $occurredAt,
        string $sourceModule = self::SOURCE_MODULE_PIM,
    ): self {
        return new self(
            $employeeId,
            $employeeReference,
            EmployeeAuditEventType::Create,
            null,
            null,
            null,
            false,
            $actorUserId,
            $actorDisplayName,
            $sourceModule,
            $occurredAt,
        );
    }

    public static function update(
        ?int $employeeId,
        string $employeeReference,
        string $fieldName,
        ?string $previousValue,
        ?string $newValue,
        bool $sensitive,
        ?int $actorUserId,
        string $actorDisplayName,
        \DateTimeImmutable $occurredAt,
        string $sourceModule = self::SOURCE_MODULE_PIM,
    ): self {
        return new self(
            $employeeId,
            $employeeReference,
            EmployeeAuditEventType::Update,
            $fieldName,
            $previousValue,
            $newValue,
            $sensitive,
            $actorUserId,
            $actorDisplayName,
            $sourceModule,
            $occurredAt,
        );
    }

    public static function delete(
        ?int $employeeId,
        string $employeeReference,
        ?int $actorUserId,
        string $actorDisplayName,
        \DateTimeImmutable $occurredAt,
        string $sourceModule = self::SOURCE_MODULE_PIM,
    ): self {
        return new self(
            $employeeId,
            $employeeReference,
            EmployeeAuditEventType::Delete,
            null,
            null,
            null,
            false,
            $actorUserId,
            $actorDisplayName,
            $sourceModule,
            $occurredAt,
        );
    }

    public function getAuditId(): ?int
    {
        return $this->auditId;
    }

    public function getEmployeeId(): ?int
    {
        return $this->employeeId;
    }

    public function getEmployeeReference(): string
    {
        return $this->employeeReference;
    }

    public function getEventType(): EmployeeAuditEventType
    {
        return $this->eventType;
    }

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    public function getPreviousValue(): ?string
    {
        return $this->previousValue;
    }

    public function getNewValue(): ?string
    {
        return $this->newValue;
    }

    public function isSensitive(): bool
    {
        return $this->sensitive;
    }

    public function getActorUserId(): ?int
    {
        return $this->actorUserId;
    }

    public function getActorDisplayName(): string
    {
        return $this->actorDisplayName;
    }

    public function getSourceModule(): string
    {
        return $this->sourceModule;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
