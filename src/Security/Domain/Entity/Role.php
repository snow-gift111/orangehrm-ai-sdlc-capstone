<?php

declare(strict_types=1);

namespace App\Security\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Application RBAC role.
 *
 * Roles relevant to the Leave Balance Alert capability:
 *  - HR_ADMIN : configure alert rules, maintain leave balances
 *  - HR_USER  : view authorized leave balances and alerts
 *  - EMPLOYEE : view own leave alert information
 */
#[ORM\Entity]
#[ORM\Table(name: 'role')]
#[ORM\UniqueConstraint(name: 'uq_role_name', columns: ['name'])]
class Role
{
    public const HR_ADMIN = 'HR_ADMIN';
    public const HR_USER = 'HR_USER';
    public const EMPLOYEE = 'EMPLOYEE';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: 'string', length: 50, unique: true)]
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
