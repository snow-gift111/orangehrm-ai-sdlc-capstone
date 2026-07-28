<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Entity;

use App\LeaveAlert\Infrastructure\Repository\LeaveBalanceRepository;
use App\Pim\Domain\Entity\Employee;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaveBalanceRepository::class)]
#[ORM\Table(name: 'leave_balance')]
#[ORM\UniqueConstraint(name: 'uq_leave_balance_employee_type_period_active', columns: ['employee_id', 'leave_type_id', 'balance_period', 'is_active'])]
#[ORM\Index(name: 'idx_leave_balance_employee_type', columns: ['employee_id', 'leave_type_id'])]
#[ORM\Index(name: 'idx_leave_balance_type_available', columns: ['leave_type_id', 'available_balance'])]
#[ORM\Index(name: 'idx_leave_balance_employee_active', columns: ['employee_id', 'is_active'])]
class LeaveBalance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'leave_balance_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'employee_id', referencedColumnName: 'id', nullable: false)]
    private Employee $employee;

    #[ORM\ManyToOne(targetEntity: LeaveType::class)]
    #[ORM\JoinColumn(name: 'leave_type_id', referencedColumnName: 'leave_type_id', nullable: false)]
    private LeaveType $leaveType;

    #[ORM\Column(name: 'balance_period', type: 'string', length: 32)]
    private string $balancePeriod;

    #[ORM\Column(name: 'entitled_amount', type: 'decimal', precision: 10, scale: 2, options: ['default' => '0.00'])]
    private string $entitledAmount;

    #[ORM\Column(name: 'used_amount', type: 'decimal', precision: 10, scale: 2, options: ['default' => '0.00'])]
    private string $usedAmount;

    #[ORM\Column(name: 'pending_amount', type: 'decimal', precision: 10, scale: 2, options: ['default' => '0.00'])]
    private string $pendingAmount;

    #[ORM\Column(name: 'available_balance', type: 'decimal', precision: 10, scale: 2)]
    private string $availableBalance;

    #[ORM\Column(name: 'unit_of_measure', type: 'string', length: 20)]
    private string $unitOfMeasure;

    #[ORM\Column(name: 'is_active', type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column(name: 'last_calculated_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastCalculatedAt = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(
        Employee $employee,
        LeaveType $leaveType,
        string $balancePeriod,
        string $entitledAmount,
        string $usedAmount,
        string $pendingAmount,
        string $availableBalance,
        ?\DateTimeImmutable $now = null,
    ) {
        $timestamp = $now ?? new \DateTimeImmutable();
        $this->employee = $employee;
        $this->leaveType = $leaveType;
        $this->balancePeriod = trim($balancePeriod);
        $this->entitledAmount = $entitledAmount;
        $this->usedAmount = $usedAmount;
        $this->pendingAmount = $pendingAmount;
        $this->availableBalance = $availableBalance;
        $this->unitOfMeasure = $leaveType->getUnitOfMeasure();
        $this->lastCalculatedAt = $timestamp;
        $this->createdAt = $timestamp;
        $this->updatedAt = $timestamp;
    }

    public function getId(): ?int { return $this->id; }
    public function getEmployee(): Employee { return $this->employee; }
    public function getLeaveType(): LeaveType { return $this->leaveType; }
    public function getBalancePeriod(): string { return $this->balancePeriod; }
    public function getEntitledAmount(): string { return $this->entitledAmount; }
    public function getUsedAmount(): string { return $this->usedAmount; }
    public function getPendingAmount(): string { return $this->pendingAmount; }
    public function getAvailableBalance(): string { return $this->availableBalance; }
    public function getAvailableBalanceAsFloat(): float { return (float) $this->availableBalance; }
    public function getUnitOfMeasure(): string { return $this->unitOfMeasure; }
    public function isActive(): bool { return $this->active; }
    public function getLastCalculatedAt(): ?\DateTimeImmutable { return $this->lastCalculatedAt; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    public function recalculate(string $entitledAmount, string $usedAmount, string $pendingAmount, string $availableBalance, ?\DateTimeImmutable $now = null): void
    {
        $timestamp = $now ?? new \DateTimeImmutable();
        $this->entitledAmount = $entitledAmount;
        $this->usedAmount = $usedAmount;
        $this->pendingAmount = $pendingAmount;
        $this->availableBalance = $availableBalance;
        $this->unitOfMeasure = $this->leaveType->getUnitOfMeasure();
        $this->lastCalculatedAt = $timestamp;
        $this->updatedAt = $timestamp;
    }

    public function deactivate(?\DateTimeImmutable $now = null): void
    {
        $this->active = false;
        $this->updatedAt = $now ?? new \DateTimeImmutable();
    }
}