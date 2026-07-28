<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Entity;

use App\LeaveAlert\Domain\Support\LeaveTypeCode;
use App\Pim\Domain\Entity\Employee;
use App\Security\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Current leave balance for an employee and leave type context.
 *
 * Requirements: LBA-FR-001, LBA-FR-002, LBA-FR-003.
 * One current balance per employee and leave type context is enforced by a
 * unique constraint.
 */
#[ORM\Entity(repositoryClass: \App\LeaveAlert\Infrastructure\Repository\LeaveBalanceRepository::class)]
#[ORM\Table(name: 'leave_balance')]
#[ORM\UniqueConstraint(name: 'uq_leave_balance_employee_type', columns: ['employee_id', 'leave_type_code'])]
#[ORM\Index(name: 'idx_leave_balance_employee', columns: ['employee_id'])]
#[ORM\HasLifecycleCallbacks]
class LeaveBalance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'employee_id', referencedColumnName: 'id', nullable: false)]
    private Employee $employee;

    #[ORM\Column(name: 'leave_type_code', type: 'string', length: 50, options: ['default' => LeaveTypeCode::DEFAULT])]
    private string $leaveTypeCode = LeaveTypeCode::DEFAULT;

    #[ORM\Column(name: 'current_balance', type: 'decimal', precision: 10, scale: 2)]
    private string $currentBalance;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'updated_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $updatedBy = null;

    public function __construct(
        Employee $employee,
        string $leaveTypeCode,
        string $currentBalance,
        ?User $updatedBy = null,
        ?\DateTimeImmutable $now = null,
    ) {
        $timestamp = $now ?? new \DateTimeImmutable();

        $this->employee = $employee;
        $this->leaveTypeCode = LeaveTypeCode::normalize($leaveTypeCode);
        $this->currentBalance = $currentBalance;
        $this->updatedBy = $updatedBy;
        $this->createdAt = $timestamp;
        $this->updatedAt = $timestamp;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): Employee
    {
        return $this->employee;
    }

    public function getLeaveTypeCode(): string
    {
        return $this->leaveTypeCode;
    }

    public function getCurrentBalance(): string
    {
        return $this->currentBalance;
    }

    public function getCurrentBalanceAsFloat(): float
    {
        return (float) $this->currentBalance;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    /**
     * Applies a new current balance value and records the maintaining user.
     */
    public function changeBalance(string $currentBalance, ?User $updatedBy, ?\DateTimeImmutable $now = null): void
    {
        $this->currentBalance = $currentBalance;
        $this->updatedBy = $updatedBy;
        $this->updatedAt = $now ?? new \DateTimeImmutable();
    }
}
