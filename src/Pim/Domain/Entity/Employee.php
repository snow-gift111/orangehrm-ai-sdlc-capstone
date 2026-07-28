<?php

declare(strict_types=1);

namespace App\Pim\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Existing PIM employee master record.
 *
 * Acts as the authoritative employee source for leave balances and leave
 * balance alerts. Deleted employees are excluded from alert evaluation
 * (LBA-FR-004).
 */
#[ORM\Entity]
#[ORM\Table(name: 'employee')]
#[ORM\UniqueConstraint(name: 'uq_employee_employee_id', columns: ['employee_id'])]
#[ORM\Index(name: 'idx_employee_deleted_at', columns: ['deleted_at'])]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'employee_id', type: 'string', length: 64, unique: true)]
    private string $employeeId;

    #[ORM\Column(name: 'first_name', type: 'string', length: 100)]
    private string $firstName;

    #[ORM\Column(name: 'last_name', type: 'string', length: 100)]
    private string $lastName;

    #[ORM\Column(name: 'deleted_at', type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    public function __construct(string $employeeId, string $firstName, string $lastName)
    {
        $this->employeeId = $employeeId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function markDeleted(\DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
