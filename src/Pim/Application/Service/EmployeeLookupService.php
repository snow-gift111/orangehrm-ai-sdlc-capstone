<?php

declare(strict_types=1);

namespace App\Pim\Application\Service;

use App\Pim\Domain\Entity\Employee;
use App\Pim\Infrastructure\Repository\EmployeeRepository;
use App\Shared\Exception\BusinessRuleException;
use App\Shared\Exception\NotFoundException;
use App\Shared\Exception\ValidationException;

/**
 * Encapsulates access to existing PIM employee master data.
 *
 * Requirements: LBA-FR-002, LBA-FR-004.
 */
final class EmployeeLookupService
{
    public function __construct(private readonly EmployeeRepository $employeeRepository)
    {
    }

    /**
     * @throws ValidationException when the supplied Employee ID is empty
     * @throws NotFoundException   when no matching employee exists
     */
    public function getByEmployeeId(string $employeeId): Employee
    {
        $normalized = trim($employeeId);

        if ($normalized === '') {
            throw ValidationException::forField('employeeId', 'Employee ID is required.');
        }

        $employee = $this->employeeRepository->findOneByEmployeeId($normalized);

        if ($employee === null) {
            throw NotFoundException::employee();
        }

        return $employee;
    }

    /**
     * Returns an employee that is guaranteed to be non-deleted, so that
     * downstream leave balance and alert processing never operates on deleted
     * employee records.
     *
     * @throws NotFoundException     when no matching employee exists
     * @throws BusinessRuleException when the employee has been deleted in PIM
     */
    public function getActiveByEmployeeId(string $employeeId): Employee
    {
        $employee = $this->getByEmployeeId($employeeId);

        if ($employee->isDeleted()) {
            throw BusinessRuleException::employeeDeleted();
        }

        return $employee;
    }
}
