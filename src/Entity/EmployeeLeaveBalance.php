<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LeaveBalanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaveBalanceRepository::class)]
#[ORM\Table(name: 'employee_leave_balance')]
#[ORM\UniqueConstraint(name: 'uniq_employee_leave_balance', columns: ['employee_id', 'leave_type_id'])]
#[ORM\Index(name: 'idx_employee_leave_balance_leave_type', columns: ['leave_type_id'])]
#[ORM\Index(name: 'idx_employee_leave_balance_last_updated_at', columns: ['last_updated_at'])]
class EmployeeLeaveBalance
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

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $currentBalance;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $lastUpdatedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'last_updated_by', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?User $lastUpdatedBy = null;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 1])]
    private int $version = 1;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Employee $employee, LeaveType $leaveType, string $currentBalance)
    {
        $now = new \DateTimeImmutable();
        $this->employee = $employee;
        $this->leaveType = $leaveType;
        $this->currentBalance = $currentBalance;
        $this->lastUpdatedAt = $now;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int { return $this->id; }
    public function getEmployee(): Employee { return $this->employee; }
    public function getLeaveType(): LeaveType { return $this->leaveType; }
    public function getCurrentBalance(): string { return $this->currentBalance; }
    public function getLastUpdatedAt(): \DateTimeImmutable { return $this->lastUpdatedAt; }
    public function getLastUpdatedBy(): ?User { return $this->lastUpdatedBy; }
    public function getVersion(): int { return $this->version; }

    public function updateBalance(string $currentBalance, ?User $updatedBy): void
    {
        $now = new \DateTimeImmutable();
        $this->currentBalance = $currentBalance;
        $this->lastUpdatedBy = $updatedBy;
        $this->lastUpdatedAt = $now;
        $this->updatedAt = $now;
    }
}
