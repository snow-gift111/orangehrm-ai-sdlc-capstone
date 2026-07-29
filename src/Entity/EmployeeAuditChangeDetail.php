<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmployeeAuditChangeDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeAuditChangeDetailRepository::class)]
#[ORM\Table(name: 'employee_audit_change_detail')]
#[ORM\Index(columns: ['audit_event_id'], name: 'idx_employee_audit_detail_event')]
#[ORM\Index(columns: ['audit_event_id', 'display_order'], name: 'idx_employee_audit_detail_event_order')]
#[ORM\Index(columns: ['field_name'], name: 'idx_employee_audit_detail_field')]
class EmployeeAuditChangeDetail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'audit_change_detail_id', type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: EmployeeAuditEvent::class, inversedBy: 'details')]
    #[ORM\JoinColumn(name: 'audit_event_id', referencedColumnName: 'audit_event_id', nullable: false, onDelete: 'RESTRICT')]
    private EmployeeAuditEvent $auditEvent;

    #[ORM\Column(name: 'field_name', type: 'string', length: 150)]
    private string $fieldName;

    #[ORM\Column(name: 'field_label', type: 'string', length: 255)]
    private string $fieldLabel;

    #[ORM\Column(name: 'previous_value', type: 'text', nullable: true)]
    private ?string $previousValue;

    #[ORM\Column(name: 'new_value', type: 'text', nullable: true)]
    private ?string $newValue;

    #[ORM\Column(name: 'display_order', type: 'integer', nullable: true)]
    private ?int $displayOrder;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        EmployeeAuditEvent $auditEvent,
        string $fieldName,
        string $fieldLabel,
        ?string $previousValue,
        ?string $newValue,
        ?int $displayOrder = null
    ) {
        $this->auditEvent = $auditEvent;
        $this->fieldName = $fieldName;
        $this->fieldLabel = $fieldLabel;
        $this->previousValue = $previousValue;
        $this->newValue = $newValue;
        $this->displayOrder = $displayOrder;
        $this->createdAt = new \DateTimeImmutable();
        $auditEvent->addDetail($this);
    }

    public function getId(): ?int { return $this->id; }
    public function getAuditEvent(): EmployeeAuditEvent { return $this->auditEvent; }
    public function getFieldName(): string { return $this->fieldName; }
    public function getFieldLabel(): string { return $this->fieldLabel; }
    public function getPreviousValue(): ?string { return $this->previousValue; }
    public function getNewValue(): ?string { return $this->newValue; }
    public function getDisplayOrder(): ?int { return $this->displayOrder; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
