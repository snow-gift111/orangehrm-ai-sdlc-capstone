<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Entity;

use App\LeaveAlert\Domain\Enum\AlertStatus;
use App\LeaveAlert\Domain\Enum\TriggerCondition;
use App\Pim\Domain\Entity\Employee;
use Doctrine\ORM\Mapping as ORM;

/**
 * Generated leave balance alert (LBA-FR-017).
 *
 * Duplicate active alerts are prevented for the same employee, leave type
 * context, rule, threshold value and trigger condition (LBA-FR-018). The
 * uniqueness is enforced both by an indexed application-level check and by a
 * database unique constraint to remain safe across clustered nodes.
 */
#[ORM\Entity(repositoryClass: \App\LeaveAlert\Infrastructure\Repository\LeaveBalanceAlertRepository::class)]
#[ORM\Table(name: 'leave_balance_alert')]
#[ORM\UniqueConstraint(
    name: 'uq_active_alert_condition',
    columns: ['employee_id', 'rule_id', 'leave_type_code', 'threshold_value_at_alert', 'trigger_condition', 'duplicate_guard']
)]
#[ORM\Index(name: 'idx_leave_alert_employee_status', columns: ['employee_id', 'status'])]
#[ORM\Index(name: 'idx_leave_alert_status_date', columns: ['status', 'alert_generated_at'])]
class LeaveBalanceAlert
{
    /**
     * Guard value used while an alert is ACTIVE. Resolved alerts receive a
     * unique guard value so that the duplicate constraint applies to active
     * alerts only.
     */
    private const ACTIVE_GUARD = 'ACTIVE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'employee_id', referencedColumnName: 'id', nullable: false)]
    private Employee $employee;

    #[ORM\ManyToOne(targetEntity: LeaveBalance::class)]
    #[ORM\JoinColumn(name: 'leave_balance_id', referencedColumnName: 'id', nullable: false)]
    private LeaveBalance $leaveBalance;

    #[ORM\ManyToOne(targetEntity: LeaveAlertRule::class)]
    #[ORM\JoinColumn(name: 'rule_id', referencedColumnName: 'id', nullable: false)]
    private LeaveAlertRule $rule;

    #[ORM\Column(name: 'leave_type_code', type: 'string', length: 50)]
    private string $leaveTypeCode;

    #[ORM\Column(name: 'current_balance_at_alert', type: 'decimal', precision: 10, scale: 2)]
    private string $currentBalanceAtAlert;

    #[ORM\Column(name: 'threshold_value_at_alert', type: 'decimal', precision: 10, scale: 2)]
    private string $thresholdValueAtAlert;

    #[ORM\Column(name: 'trigger_condition', type: 'string', length: 30, enumType: TriggerCondition::class)]
    private TriggerCondition $triggerCondition;

    #[ORM\Column(name: 'status', type: 'string', length: 20, enumType: AlertStatus::class)]
    private AlertStatus $status = AlertStatus::ACTIVE;

    #[ORM\Column(name: 'duplicate_guard', type: 'string', length: 64)]
    private string $duplicateGuard = self::ACTIVE_GUARD;

    #[ORM\Column(name: 'alert_generated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $alertGeneratedAt;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        Employee $employee,
        LeaveBalance $leaveBalance,
        LeaveAlertRule $rule,
        string $leaveTypeCode,
        string $currentBalanceAtAlert,
        string $thresholdValueAtAlert,
        TriggerCondition $triggerCondition,
        ?\DateTimeImmutable $now = null,
    ) {
        $timestamp = $now ?? new \DateTimeImmutable();

        $this->employee = $employee;
        $this->leaveBalance = $leaveBalance;
        $this->rule = $rule;
        $this->leaveTypeCode = $leaveTypeCode;
        $this->currentBalanceAtAlert = $currentBalanceAtAlert;
        $this->thresholdValueAtAlert = $thresholdValueAtAlert;
        $this->triggerCondition = $triggerCondition;
        $this->status = AlertStatus::ACTIVE;
        $this->duplicateGuard = self::ACTIVE_GUARD;
        $this->alertGeneratedAt = $timestamp;
        $this->createdAt = $timestamp;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function getLeaveBalance(): LeaveBalance
    {
        return $this->leaveBalance;
    }

    public function getRule(): LeaveAlertRule
    {
        return $this->rule;
    }

    public function getLeaveTypeCode(): string
    {
        return $this->leaveTypeCode;
    }

    public function getCurrentBalanceAtAlert(): string
    {
        return $this->currentBalanceAtAlert;
    }

    public function getThresholdValueAtAlert(): string
    {
        return $this->thresholdValueAtAlert;
    }

    public function getTriggerCondition(): TriggerCondition
    {
        return $this->triggerCondition;
    }

    public function getStatus(): AlertStatus
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === AlertStatus::ACTIVE;
    }

    public function getAlertGeneratedAt(): \DateTimeImmutable
    {
        return $this->alertGeneratedAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
