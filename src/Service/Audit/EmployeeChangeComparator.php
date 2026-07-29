<?php

declare(strict_types=1);

namespace App\Service\Audit;

final readonly class EmployeeChangeComparator
{
    public function __construct(
        private EmployeeAuditFieldRegistry $fieldRegistry,
        private AuditFieldValueFormatter $valueFormatter,
    ) {
    }

    /**
     * @param array<string, mixed> $previousState
     * @param array<string, mixed> $newState
     * @return list<array{fieldName: string, fieldLabel: string, previousValue: ?string, newValue: ?string}>
     */
    public function compare(array $previousState, array $newState): array
    {
        $changes = [];

        foreach ($this->fieldRegistry->getAuditableFields() as $fieldName => $fieldLabel) {
            if (!array_key_exists($fieldName, $previousState) && !array_key_exists($fieldName, $newState)) {
                continue;
            }

            $previousValue = $this->valueFormatter->format($previousState[$fieldName] ?? null);
            $newValue = $this->valueFormatter->format($newState[$fieldName] ?? null);

            if ($previousValue === $newValue) {
                continue;
            }

            $changes[] = [
                'fieldName' => $fieldName,
                'fieldLabel' => $fieldLabel,
                'previousValue' => $previousValue,
                'newValue' => $newValue,
            ];
        }

        return $changes;
    }
}
