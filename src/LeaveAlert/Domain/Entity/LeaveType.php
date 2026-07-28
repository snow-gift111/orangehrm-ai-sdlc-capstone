<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Entity;

use App\LeaveAlert\Infrastructure\Repository\LeaveTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeaveTypeRepository::class)]
#[ORM\Table(name: 'leave_type')]
#[ORM\UniqueConstraint(name: 'uq_leave_type_name', columns: ['name'])]
#[ORM\Index(name: 'idx_leave_type_active', columns: ['is_active'])]
class LeaveType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'leave_type_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(name: 'unit_of_measure', type: 'string', length: 20)]
    private string $unitOfMeasure;

    #[ORM\Column(name: 'is_active', type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct(string $name, string $unitOfMeasure, ?\DateTimeImmutable $now = null)
    {
        $timestamp = $now ?? new \DateTimeImmutable();
        $this->name = trim($name);
        $this->unitOfMeasure = strtoupper(trim($unitOfMeasure));
        $this->createdAt = $timestamp;
        $this->updatedAt = $timestamp;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUnitOfMeasure(): string
    {
        return $this->unitOfMeasure;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function rename(string $name, string $unitOfMeasure, ?\DateTimeImmutable $now = null): void
    {
        $this->name = trim($name);
        $this->unitOfMeasure = strtoupper(trim($unitOfMeasure));
        $this->updatedAt = $now ?? new \DateTimeImmutable();
    }

    public function activate(?\DateTimeImmutable $now = null): void
    {
        $this->active = true;
        $this->updatedAt = $now ?? new \DateTimeImmutable();
    }

    public function deactivate(?\DateTimeImmutable $now = null): void
    {
        $this->active = false;
        $this->updatedAt = $now ?? new \DateTimeImmutable();
    }
}
