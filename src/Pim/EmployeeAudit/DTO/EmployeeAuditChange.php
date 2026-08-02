<?php

declare(strict_types=1);

namespace App\Pim\EmployeeAudit\DTO;

use InvalidArgumentException;

final readonly class EmployeeAuditChange
{
    public function __construct(
        public ?string $fieldName,
        public ?string $previousValue,
        public ?string $newValue,
    ) {
        $fieldName = $this->fieldName === null ? null : trim($this->fieldName);

        if ($fieldName === '') {
            throw new InvalidArgumentException('Audit change field name cannot be blank.');
        }
    }
}
