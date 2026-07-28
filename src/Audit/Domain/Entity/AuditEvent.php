<?php

declare(strict_types=1);

namespace App\Audit\Domain\Entity;

use App\LeaveAlert\Domain\Enum\AuditActionType;
use App\Security\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Audit trail entry (LBA-FR-039, LBA-FR-040).
 */
#[ORM\Entity(repositoryClass: \App\Audit\Infrastructure\Repository\AuditEventRepository::class)]
#[ORM\Table(name: 'audit_event')]
#[ORM\Index(name: 'idx_audit_entity', columns: ['entity_type', 'entity_id'])]
#[ORM\Index(name: 'idx_audit_action_date', columns: ['action_type', 'created_at'])]
#[ORM\Index(name: 'idx_audit_actor_date', columns: ['actor_user_id', 'created_at'])]
class AuditEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'actor_user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $actor = null;

    #[ORM\Column(name: 'action_type', type: 'string', length: 60, enumType: AuditActionType::class)]
    private AuditActionType $actionType;

    #[ORM\Column(name: 'entity_type', type: 'string', length: 60)]
    private string $entityType;

    #[ORM\Column(name: 'entity_id', type: 'string', length: 64)]
    private string $entityId;

    #[ORM\Column(name: 'event_summary', type: 'string', length: 500)]
    private string $eventSummary;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(name: 'event_metadata', type: 'json', nullable: true)]
    private ?array $eventMetadata = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /**
     * @param array<string, mixed>|null $eventMetadata
     */
    public function __construct(
        ?User $actor,
        AuditActionType $actionType,
        string $entityType,
        string $entityId,
        string $eventSummary,
        ?array $eventMetadata = null,
        ?\DateTimeImmutable $now = null,
    ) {
        $this->actor = $actor;
        $this->actionType = $actionType;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->eventSummary = $eventSummary;
        $this->eventMetadata = $eventMetadata;
        $this->createdAt = $now ?? new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActor(): ?User
    {
        return $this->actor;
    }

    public function getActionType(): AuditActionType
    {
        return $this->actionType;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function getEventSummary(): string
    {
        return $this->eventSummary;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getEventMetadata(): ?array
    {
        return $this->eventMetadata;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
