<?php

declare(strict_types=1);

namespace App\Pim\EmployeeAudit\Entity;

use App\Pim\EmployeeAudit\Enum\EmployeeAuditActionType;
use App\Pim\EmployeeAudit\Repository\EmployeeAuditRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: EmployeeAuditRepository::class)]
#[ORM\Table(name: 'employee_audit_record')]
#[ORM\Index(name: 'idx_employee_audit_employee_timestamp', columns: ['employee_id', 'event_timestamp'])]
#[ORM\Index(name: 'idx_employee_audit_employee_action', columns: ['employee_id', 'action_type'])]
#[ORM\Index(name: 'idx_employee_audit_employee_field', columns: ['employee_id', 'changed_field'])]
#[ORM\Index(name: 'idx_employee_audit_employee_actor', columns: ['employee_id', 'actor_user_id'])]
#[ORM\HasLifecycleCallbacks]
class EmployeeAuditRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'employee_id', type: Types::INTEGER)]
    private int $employeeId;

    #[ORM\Column(name: 'action_type', type: Types::STRING, length: 32, enumType: EmployeeAuditActionType::class)]
    private EmployeeAuditActionType $actionType;

    #[ORM\Column(name: 'changed_field', type: Types::STRING, length: 128, nullable: true)]
    private ?string $changedField;

    #[ORM\Column(name: 'previous_value', type: Types::TEXT, nullable: true)]
    private ?string $previousValue;

    #[ORM\Column(name: 'new_value', type: Types::TEXT, nullable: true)]
    private ?string $newValue;

    #[ORM\Column(name: 'actor_user_id', type: Types::INTEGER, nullable: true)]
    private ?int $actorUserId;

    #[ORM\Column(name: 'actor_display_name', type: Types::STRING, length: 255, nullable: true)]
    private ?string $actorDisplayName;

    #[ORM\Column(name: 'event_timestamp', type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $eventTimestamp;

    #[ORM\Column(name: 'source_context', type: Types::STRING, length: 255, nullable: true)]
    private ?string $sourceContext;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    public function __construct(
        int $employeeId,
        EmployeeAuditActionType $actionType,
        ?string $changedField,
        ?string $previousValue,
        ?string $newValue,
        ?int $actorUserId,
        ?string $actorDisplayName,
        ?DateTimeImmutable $eventTimestamp = null,
        ?string $sourceContext = null,
    ) {
        if ($employeeId <= 0) {
            throw new InvalidArgumentException('Employee audit record requires a positive employee identifier.');
        }

        $this->employeeId = $employeeId;
        $this->actionType = $actionType;
        $this->changedField = self::normalizeNullableString($changedField, 128, 'Changed field');
        $this->previousValue = $previousValue;
        $this->newValue = $newValue;
        $this->actorUserId = $actorUserId;
        $this->actorDisplayName = self::normalizeNullableString($actorDisplayName, 255, 'Actor display name');
        $this->eventTimestamp = $eventTimestamp ?? new DateTimeImmutable();
        $this->sourceContext = self::normalizeNullableString($sourceContext, 255, 'Source context');
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    public function getActionType(): EmployeeAuditActionType
    {
        return $this->actionType;
    }

    public function getChangedField(): ?string
    {
        return $this->changedField;
    }

    public function getPreviousValue(): ?string
    {
        return $this->previousValue;
    }

    public function getNewValue(): ?string
    {
        return $this->newValue;
    }

    public function getActorUserId(): ?int
    {
        return $this->actorUserId;
    }

    public function getActorDisplayName(): ?string
    {
        return $this->actorDisplayName;
    }

    public function getEventTimestamp(): DateTimeImmutable
    {
        return $this->eventTimestamp;
    }

    public function getSourceContext(): ?string
    {
        return $this->sourceContext;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function ensureCreatedAt(): void
    {
        if (!isset($this->createdAt)) {
            $this->createdAt = new DateTimeImmutable();
        }
    }

    private static function normalizeNullableString(?string $value, int $maxLength, string $label): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);
        if ($normalized === '') {
            return null;
        }

        if (mb_strlen($normalized) > $maxLength) {
            throw new InvalidArgumentException(sprintf('%s cannot exceed %d characters.', $label, $maxLength));
        }

        return $normalized;
    }
}
