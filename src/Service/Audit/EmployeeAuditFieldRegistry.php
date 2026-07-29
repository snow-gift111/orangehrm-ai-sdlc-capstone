<?php

declare(strict_types=1);

namespace App\Service\Audit;

final class EmployeeAuditFieldRegistry
{
    /**
     * @return array<string, string>
     */
    public function getAuditableFields(): array
    {
        return [
            'employeeId' => 'Employee ID',
            'firstName' => 'First Name',
            'middleName' => 'Middle Name',
            'lastName' => 'Last Name',
            'employeeName' => 'Employee Name',
            'jobTitle' => 'Job Title',
            'employmentStatus' => 'Employment Status',
        ];
    }

    public function labelFor(string $fieldName): string
    {
        return $this->getAuditableFields()[$fieldName] ?? $fieldName;
    }
}
