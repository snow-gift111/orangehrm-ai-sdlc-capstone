<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Audit\AuditActionType;
use App\Repository\EmployeeAuditEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeAuditEventRepository::class)]
#[ORM\Table(name: 'employee_audit_event')]
#[ORM\Index(columns: ['employee_id_snapshot', 'occurred_at'], name: 'idx_employee_audit_event_employee_id_occurred')]
#[ORM\Index(columns: ['employee_internal_id', 'occurred_at'], name: 'idx_employee_audit_event_internal_occurred')]
#[ORM\Index(columns: ['action_type'], name: 'idx_employee_audit_event_action_type')]
#[ORM\Index(columns: ['changed_by_user_id'], name: 'idx_employee_audit_event_changed_by')]
#[ORM\Index(columns: ['occurred_at'], name: 'idx_employee_audit_event_occurred_at')]
class EmployeeAuditEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'audit_event_id', type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(name: 'employee_internal_id', type: 'bigint', nullable: true)]
    private ?int $employeeInternalId;

    #[ORM\Column(name: 'employee_id_snapshot', type: 'string', length: 100)]
    private string $employeeIdSnapshot;

    #[ORM\Column(name: 'employee_name_snapshot', type: 'string', length: 255, nullable: true)]
    private ?string $employeeNameSnapshot;

    #[ORM\Column(name: 'action_type', type: 'string', length: 20)]
    private string $actionType;

    #[ORM\Column(name: 'changed_by_user_id', type: 'bigint', nullable: true)]
    private ?int $changedByUserId;

    #[ORM\Column(name: 'changed_by_username_snapshot', type: 'string', length: 255)]
    private string $changedByUsernameSnapshot;

    #[ORM\Column(name: 'occurred_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $occurredAt;

    #[ORM\Column(name: 'summary', type: 'string', length: 1000, nullable: true)]
    private ?string $summary;

    #[ORM\Column(name: 'request_correlation_id', type: 'string', length: 100, nullable: true)]
    private ?string $requestCorrelationId;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, EmployeeAuditChangeDetail> */
    #[ORM\OneToMany(mappedBy: 'auditEvent', targetEntity: EmployeeAuditChangeDetail::class, cascade: ['persist'], orphanRemoval: false)]
    #[ORM\OrderBy(['displayOrder' => 'ASC', 'id' => 'ASC'])]
    private Collection $details;

    public function __construct(
        ?int $employeeInternalId,
        string $employeeIdSnapshot,
        ?string $employeeNameSnapshot,
        AuditActionType $actionType,
        ?int $changedByUserId,
        string $changedByUsernameSnapshot,
        \DateTimeImmutable $occurredAt,
        ?string $summary = null,
        ?string $requestCorrelationId = null
    ) {
        $this->employeeInternalId = $employeeInternalId;
        $this->employeeIdSnapshot = $employeeIdSnapshot;
        $this->employeeNameSnapshot = $employeeNameSnapshot;
        $this->actionType = $actionType->value;
        $this->changedByUserId = $changedByUserId;
        $this->changedByUsernameSnapshot = $changedByUsernameSnapshot;
        $this->occurredAt = $occurredAt;
        $this->summary = $summary;
        $this->requestCorrelationId = $requestCorrelationId;
        $this->createdAt = new \DateTimeImmutable();
        $this->details = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getEmployeeInternalId(): ?int { return $this->employeeInternalId; }
    public function getEmployeeIdSnapshot(): string { return $this->employeeIdSnapshot; }
    public function getEmployeeNameSnapshot(): ?string { return $this->employeeNameSnapshot; }
    public function getActionType(): string { return $this->actionType; }
    public function getChangedByUserId(): ?int { return $this->changedByUserId; }
    public function getChangedByUsernameSnapshot(): string { return $this->changedByUsernameSnapshot; }
    public function getOccurredAt(): \DateTimeImmutable { return $this->occurredAt; }
    public function getSummary(): ?string { return $this->summary; }
    public function getRequestCorrelationId(): ?string { return $this->requestCorrelationId; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    /** @return Collection<int, EmployeeAuditChangeDetail> */
    public function getDetails(): Collection { return $this->details; }

    public function addDetail(EmployeeAuditChangeDetail $detail): void
    {
        if (!$this->details->contains($detail)) {
            $this->details->add($detail);
        }
    }
}
