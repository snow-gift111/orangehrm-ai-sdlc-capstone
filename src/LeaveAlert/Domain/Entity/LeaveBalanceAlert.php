<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Entity;

use App\LeaveAlert\Domain\Enum\AlertStatus;
use App\LeaveAlert\Domain\Enum\AlertType;
use App\LeaveAlert\Infrastructure\Repository\LeaveBalanceAlertRepository;
use App\Pim\Domain\Entity\Employee;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaveBalanceAlertRepository::class)]
#[ORM\Table(name: 'leave_balance_alert')]
#[ORM\Index(name: 'idx_lba_employee_status', columns: ['employee_id', 'alert_status'])]
#[ORM\Index(name: 'idx_lba_employee_type_status', columns: ['employee_id', 'leave_type_id', 'alert_type', 'alert_status'])]
#[ORM\Index(name: 'idx_lba_condition_status', columns: ['condition_key', 'alert_status'])]
#[ORM\UniqueConstraint(name: 'uq_lba_active_condition_guard', columns: ['condition_key', 'active_guard'])]
class LeaveBalanceAlert
{
    private const ACTIVE_GUARD = 'ACTIVE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'alert_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'employee_id', referencedColumnName: 'id', nullable: false)]
    private Employee $employee;

    #[ORM\ManyToOne(targetEntity: LeaveType::class)]
    #[ORM\JoinColumn(name: 'leave_type_id', referencedColumnName: 'leave_type_id', nullable: false)]
    private LeaveType $leaveType;

    #[ORM\Column(name: 'alert_type', type: 'string', length: 40, enumType: AlertType::class)]
    private AlertType $alertType;

    #[ORM\Column(name: 'alert_status', type: 'string', length: 20, enumType: AlertStatus::class)]
    private AlertStatus $alertStatus;

    #[ORM\Column(name: 'current_balance', type: 'decimal', precision: 10, scale: 2)]
    private string $currentBalance;

    #[ORM\Column(name: 'threshold_value', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $thresholdValue;

    #[ORM\Column(name: 'requested_duration', type: 'decimal', precision: 10, scale: 2, nullable: true)]
    private ?string $requestedDuration;

    #[ORM\Column(name: 'condition_key', type: 'string', length: 128)]
    private string $conditionKey;

    #[ORM\Column(name: 'active_guard', type: 'string', length: 128)]
    private string $activeGuard;

    #[ORM\Column(name: 'generated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $generatedAt;

    #[ORM\Column(name: 'resolved_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $resolvedAt = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        Employee $employee,
        LeaveType $leaveType,
        AlertType $alertType,
        string $currentBalance,
        ?string $thresholdValue,
        ?string $requestedDuration,
        string $conditionKey,
        ?\DateTimeImmutable $now = null,
    ) {
        $timestamp = $now ?? new \DateTimeImmutable();
        $this->employee = $employee;
        $this->leaveType = $leaveType;
        $this->alertType = $alertType;
        $this->alertStatus = AlertStatus::ACTIVE;
        $this->currentBalance = $currentBalance;
        $this->thresholdValue = $thresholdValue;
        $this->requestedDuration = $requestedDuration;
        $this->conditionKey = $conditionKey;
        $this->activeGuard = self::ACTIVE_GUARD;
        $this->generatedAt = $timestamp;
        $this->createdAt = $timestamp;
        $this->updatedAt = $timestamp;
    }

    public function getId(): ?int { return $this->id; }
    public function getEmployee(): Employee { return $this->employee; }
    public function getLeaveType(): LeaveType { return $this->leaveType; }
    public function getAlertType(): AlertType { return $this->alertType; }
    public function getAlertStatus(): AlertStatus { return $this->alertStatus; }
    public function getStatus(): AlertStatus { return $this->alertStatus; }
    public function getCurrentBalance(): string { return $this->currentBalance; }
    public function getThresholdValue(): ?string { return $this->thresholdValue; }
    public function getRequestedDuration(): ?string { return $this->requestedDuration; }
    public function getConditionKey(): string { return $this->conditionKey; }
    public function getGeneratedAt(): \DateTimeImmutable { return $this->generatedAt; }
    public function getAlertGeneratedAt(): \DateTimeImmutable { return $this->generatedAt; }
    public function getResolvedAt(): ?\DateTimeImmutable { return $this->resolvedAt; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function isActive(): bool { return $this->alertStatus === AlertStatus::ACTIVE; }

    public function resolve(?\DateTimeImmutable $now = null): void
    {
        if (!$this->isActive()) {
            return;
        }

        $timestamp = $now ?? new \DateTimeImmutable();
        $this->alertStatus = AlertStatus::RESOLVED;
        $this->resolvedAt = $timestamp;
        $this->updatedAt = $timestamp;
        $this->activeGuard = 'RESOLVED-' . ($this->id ?? bin2hex(random_bytes(8))) . '-' . $timestamp->format('Uu');
    }
}