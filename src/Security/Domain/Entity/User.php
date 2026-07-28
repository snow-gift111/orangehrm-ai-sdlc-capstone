<?php

declare(strict_types=1);

namespace App\Security\Domain\Entity;

use App\Pim\Domain\Entity\Employee;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Existing authenticated application user.
 *
 * A user may optionally be mapped to a PIM employee record, which enables
 * employee self-view of leave balances and leave balance alerts.
 */
#[ORM\Entity(repositoryClass: \App\Security\Infrastructure\Repository\UserRepository::class)]
#[ORM\Table(name: 'app_user')]
#[ORM\UniqueConstraint(name: 'uq_user_username', columns: ['username'])]
#[ORM\Index(name: 'idx_user_employee', columns: ['employee_id'])]
class User implements UserInterface
{
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'username', type: 'string', length: 100, unique: true)]
    private string $username;

    #[ORM\Column(name: 'password_hash', type: 'string', length: 255)]
    private string $passwordHash;

    #[ORM\Column(name: 'status', type: 'string', length: 20)]
    private string $status = self::STATUS_ACTIVE;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'employee_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Employee $employee = null;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class)]
    #[ORM\JoinTable(name: 'user_role')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'role_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $roles;

    public function __construct(string $username, string $passwordHash)
    {
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): void
    {
        $this->employee = $employee;
    }

    public function addRole(Role $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
    }

    public function removeRole(Role $role): void
    {
        $this->roles->removeElement($role);
    }

    /**
     * @return list<string> Domain role names, e.g. ['HR_ADMIN']
     */
    public function getRoleNames(): array
    {
        return array_values(
            array_map(static fn (Role $role): string => $role->getName(), $this->roles->toArray())
        );
    }

    public function hasRole(string $roleName): bool
    {
        return in_array($roleName, $this->getRoleNames(), true);
    }

    /**
     * @return list<string> Security component roles, prefixed with ROLE_
     */
    public function getRoles(): array
    {
        $roles = array_map(
            static fn (string $name): string => 'ROLE_' . $name,
            $this->getRoleNames()
        );

        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    public function eraseCredentials(): void
    {
        // No temporary sensitive credential data is held on this entity.
    }
}
