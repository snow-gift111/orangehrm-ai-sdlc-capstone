<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\Table(name: 'employee')]
#[ORM\UniqueConstraint(name: 'uniq_employee_employee_id', columns: ['employee_id'])]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(name: 'employee_id', type: 'string', length: 100)]
    private string $employeeId;

    #[ORM\Column(name: 'first_name', type: 'string', length: 100)]
    private string $firstName;

    #[ORM\Column(name: 'middle_name', type: 'string', length: 100, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(name: 'last_name', type: 'string', length: 100)]
    private string $lastName;

    #[ORM\Column(name: 'job_title', type: 'string', length: 150, nullable: true)]
    private ?string $jobTitle = null;

    #[ORM\Column(name: 'employment_status', type: 'string', length: 100, nullable: true)]
    private ?string $employmentStatus = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(string $employeeId, string $firstName, string $lastName)
    {
        $now = new \DateTimeImmutable();
        $this->employeeId = $employeeId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployeeId(): string
    {
        return $this->employeeId;
    }

    public function setEmployeeId(string $employeeId): void
    {
        $this->employeeId = $employeeId;
        $this->touch();
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
        $this->touch();
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): void
    {
        $this->middleName = $middleName;
        $this->touch();
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
        $this->touch();
    }

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): void
    {
        $this->jobTitle = $jobTitle;
        $this->touch();
    }

    public function getEmploymentStatus(): ?string
    {
        return $this->employmentStatus;
    }

    public function setEmploymentStatus(?string $employmentStatus): void
    {
        $this->employmentStatus = $employmentStatus;
        $this->touch();
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDisplayName(): string
    {
        return trim(implode(' ', array_filter([$this->firstName, $this->middleName, $this->lastName])));
    }

    /**
     * @return array<string, mixed>
     */
    public function toAuditSnapshot(): array
    {
        return [
            'internalId' => $this->id,
            'employeeId' => $this->employeeId,
            'employeeName' => $this->getDisplayName(),
            'firstName' => $this->firstName,
            'middleName' => $this->middleName,
            'lastName' => $this->lastName,
            'jobTitle' => $this->jobTitle,
            'employmentStatus' => $this->employmentStatus,
        ];
    }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
