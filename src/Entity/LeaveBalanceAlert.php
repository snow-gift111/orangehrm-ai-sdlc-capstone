<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\AlertAcknowledgementStatus;
use App\Enum\AlertCondition;
use App\Enum\AlertLifecycleStatus;
use App\Enum\AlertReadStatus;
use App\Repository\LeaveBalanceAlertRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaveBalanceAlertRepository::class)]
#[ORM\Table(name: 'leave_balance_alert')]
#[ORM\Index(name: 'idx_leave_balance_alert_employee_lifecycle', columns: ['employee_id', 'lifecycle_status'])]
#[ORM\Index(name: 'idx_leave_balance_alert_leave_type_lifecycle', columns: ['leave_type_id', 'lifecycle_status'])]
#[ORM\Index(name: 'idx_leave_balance_alert_condition_lifecycle', columns: ['alert_condition', 'lifecycle_status'])]
#[ORM\Index(name: 'idx_leave_balance_alert_date', columns: ['alert_date'])]
#[ORM\Index(name: 'idx_leave_balance_alert_ack_lifecycle', columns: ['acknowledgement_status', 'lifecycle_status'])]
class LeaveBalanceAlert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'employee_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Employee $employee;

    #[ORM\ManyToOne(targetEntity: LeaveType::class)]
    #[ORM\JoinColumn(name: 'leave_type_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    private LeaveType $leaveType;

    #[ORM\ManyToOne(targetEntity: LeaveAlertThreshold::class)]
    #[ORM\JoinColumn(name: 'threshold_id', referencedColumnName: 'id', nullable: false, onDelete: 'RESTRICT')]
    private LeaveAlertThreshold $threshold;

    #[ORM\Column(type: Types::STRING, length: 32, enumType: AlertCondition::class)]
    private AlertCondition $alertCondition;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $currentBalanceAtAlert;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $thresholdValueAtAlert;

    #[ORM\Column(type: Types::STRING, length: 32, enumType: AlertLifecycleStatus::class)]
    private AlertLifecycleStatus $lifecycleStatus = AlertLifecycleStatus::ACTIVE;

    #[ORM\Column(type: Types::STRING, length: 32, enumType: AlertReadStatus::class)]
    private AlertReadStatus $readStatus = AlertReadStatus::UNREAD;

    #[ORM\Column(type: Types::STRING, length: 32, enumType: AlertAcknowledgementStatus::class)]
    private AlertAcknowledgementStatus $acknowledgementStatus = AlertAcknowledgementStatus::UNACKNOWLEDGED;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $alertDate;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $readAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'acknowledged_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $acknowledgedBy = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $acknowledgedAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $resolvedAt = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $resolvedReason = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Employee $employee, LeaveType $leaveType, LeaveAlertThreshold $threshold, AlertCondition $condition, string $balanceSnapshot, string $thresholdSnapshot)
    {
        $now = new \DateTimeImmutable();
        $this->employee = $employee;
        $this->leaveType = $leaveType;
        $this->threshold = $threshold;
        $this->alertCondition = $condition;
        $this->currentBalanceAtAlert = $balanceSnapshot;
        $this->thresholdValueAtAlert = $thresholdSnapshot;
        $this->alertDate = $now;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getEmployee(): Employee { return $this->employee; }
    public function getLeaveType(): LeaveType { return $this->leaveType; }
    public function getThreshold(): LeaveAlertThreshold { return $this->threshold; }
    public function getAlertCondition(): AlertCondition { return $this->alertCondition; }
    public function getCurrentBalanceAtAlert(): string { return $this->currentBalanceAtAlert; }
    public function getThresholdValueAtAlert(): string { return $this->thresholdValueAtAlert; }
    public function getLifecycleStatus(): AlertLifecycleStatus { return $this->lifecycleStatus; }
    public function getReadStatus(): AlertReadStatus { return $this->readStatus; }
    public function getAcknowledgementStatus(): AlertAcknowledgementStatus { return $this->acknowledgementStatus; }
    public function getAlertDate(): \DateTimeImmutable { return $this->alertDate; }
    public function getReadAt(): ?\DateTimeImmutable { return $this->readAt; }
    public function getAcknowledgedBy(): ?User { return $this->acknowledgedBy; }
    public function getAcknowledgedAt(): ?\DateTimeImmutable { return $this->acknowledgedAt; }
    public function getResolvedAt(): ?\DateTimeImmutable { return $this->resolvedAt; }

    public function markRead(): void
    {
        if ($this->readStatus === AlertReadStatus::READ) {
            return;
        }

        $this->readStatus = AlertReadStatus::READ;
        $this->readAt = new \DateTimeImmutable();
        $this->updatedAt = $this->readAt;
    }

    public function acknowledge(User $acknowledgedBy): void
    {
        if ($this->acknowledgementStatus === AlertAcknowledgementStatus::ACKNOWLEDGED) {
            return;
        }

        $now = new \DateTimeImmutable();
        $this->acknowledgementStatus = AlertAcknowledgementStatus::ACKNOWLEDGED;
        $this->acknowledgedBy = $acknowledgedBy;
        $this->acknowledgedAt = $now;
        $this->updatedAt = $now;
    }

    public function resolve(string $reason): void
    {
        if ($this->lifecycleStatus === AlertLifecycleStatus::RESOLVED) {
            return;
        }

        $now = new \DateTimeImmutable();
        $this->lifecycleStatus = AlertLifecycleStatus::RESOLVED;
        $this->resolvedReason = $reason;
        $this->resolvedAt = $now;
        $this->updatedAt = $now;
    }
}
