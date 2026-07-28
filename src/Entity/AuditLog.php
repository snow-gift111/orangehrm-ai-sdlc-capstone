<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AuditLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditLogRepository::class)]
#[ORM\Table(name: 'audit_log')]
#[ORM\Index(name: 'idx_audit_log_entity', columns: ['entity_type', 'entity_id'])]
#[ORM\Index(name: 'idx_audit_log_changed_by', columns: ['changed_by'])]
#[ORM\Index(name: 'idx_audit_log_changed_at', columns: ['changed_at'])]
#[ORM\Index(name: 'idx_audit_log_action', columns: ['action'])]
#[ORM\Index(name: 'idx_audit_log_correlation', columns: ['correlation_id'])]
class AuditLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 120)]
    private string $entityType;

    #[ORM\Column(type: Types::STRING, length: 120)]
    private string $entityId;

    #[ORM\Column(type: Types::STRING, length: 120)]
    private string $action;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $previousValues = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $newValues = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'changed_by', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    private User $changedBy;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $changedAt;

    #[ORM\Column(type: Types::STRING, length: 120)]
    private string $correlationId;

    public function __construct(string $entityType, string $entityId, string $action, User $changedBy, string $correlationId, ?array $previousValues = null, ?array $newValues = null)
    {
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->action = $action;
        $this->changedBy = $changedBy;
        $this->correlationId = $correlationId;
        $this->previousValues = $previousValues;
        $this->newValues = $newValues;
        $this->changedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getEntityType(): string { return $this->entityType; }
    public function getEntityId(): string { return $this->entityId; }
    public function getAction(): string { return $this->action; }
    public function getPreviousValues(): ?array { return $this->previousValues; }
    public function getNewValues(): ?array { return $this->newValues; }
    public function getChangedBy(): User { return $this->changedBy; }
    public function getChangedAt(): \DateTimeImmutable { return $this->changedAt; }
    public function getCorrelationId(): string { return $this->correlationId; }
}
