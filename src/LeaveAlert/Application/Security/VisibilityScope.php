<?php

declare(strict_types=1);

namespace App\LeaveAlert\Application\Security;

use App\Pim\Domain\Entity\Employee;

/**
 * Immutable description of the alert visibility scope permitted for a user.
 *
 * Requirements: LBA-FR-036, LBA-FR-037.
 */
final class VisibilityScope
{
    private function __construct(
        private readonly bool $allEmployees,
        private readonly ?Employee $employee,
    ) {
    }

    public static function allEmployees(): self
    {
        return new self(true, null);
    }

    public static function ownEmployeeOnly(Employee $employee): self
    {
        return new self(false, $employee);
    }

    public function isAllEmployees(): bool
    {
        return $this->allEmployees;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }
}
