<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\ThresholdScopeType;
use App\Repository\AlertThresholdRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlertThresholdRepository::class)]
#[ORM\Table(name: 'leave_alert_threshold')]
#[ORM\Index(name: 'idx_leave_alert_threshold_scope_active', columns: ['scope_type', 'active'])]
#[ORM\Index(name: 'idx_leave_alert_threshold_leave_type', columns: ['leave_type_id'])]
class LeaveAlertThreshold
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 32, enumType: ThresholdScopeType::class)]
    private ThresholdScopeType $scopeType;

    #[ORM\ManyToOne(targetEntity: LeaveType::class)]
    #[ORM\JoinColumn(name: 'leave_type_id', referencedColumnName: 'id', nullable: true, onDelete: 'RESTRICT')]
    private ?LeaveType $leaveType = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $thresholdValue;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $active = true;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $createdBy = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $updatedBy = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(ThresholdScopeType $scopeType, string $thresholdValue, ?User $createdBy = null, ?LeaveType $leaveType = null)
    {
        $now = new \DateTimeImmutable();
        $this->scopeType = $scopeType;
        $this->thresholdValue = $thresholdValue;
        $this->createdBy = $createdBy;
        $this->updatedBy = $createdBy;
        $this->leaveType = $leaveType;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getScopeType(): ThresholdScopeType { return $this->scopeType; }
    public function getLeaveType(): ?LeaveType { return $this->leaveType; }
    public function getThresholdValue(): string { return $this->thresholdValue; }
    public function isActive(): bool { return $this->active; }
    public function getCreatedBy(): ?User { return $this->createdBy; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedBy(): ?User { return $this->updatedBy; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function updateThresholdValue(string $thresholdValue, ?User $updatedBy): void
    {
        $this->thresholdValue = $thresholdValue;
        $this->updatedBy = $updatedBy;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function deactivate(?User $updatedBy): void
    {
        $this->active = false;
        $this->updatedBy = $updatedBy;
        $this->updatedAt = new \DateTimeImmutable();
    }
}
