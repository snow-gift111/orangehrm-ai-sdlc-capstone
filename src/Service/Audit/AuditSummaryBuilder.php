<?php

declare(strict_types=1);

namespace App\Service\Audit;

use App\Domain\Audit\AuditActionType;

final class AuditSummaryBuilder
{
    /**
     * @param list<array{fieldName: string, fieldLabel: string, previousValue: ?string, newValue: ?string}> $changes
     */
    public function build(AuditActionType $actionType, ?string $employeeName, string $employeeId, array $changes = []): string
    {
        $displayName = $employeeName !== null && trim($employeeName) !== ''
            ? sprintf('%s (%s)', $employeeName, $employeeId)
            : $employeeId;

        return match ($actionType) {
            AuditActionType::CREATE => sprintf('Employee record created for %s.', $displayName),
            AuditActionType::DELETE => sprintf('Employee record deleted for %s.', $displayName),
            AuditActionType::UPDATE => $this->buildUpdateSummary($displayName, $changes),
        };
    }

    /**
     * @param list<array{fieldName: string, fieldLabel: string, previousValue: ?string, newValue: ?string}> $changes
     */
    private function buildUpdateSummary(string $displayName, array $changes): string
    {
        $count = count($changes);
        if ($count === 0) {
            return sprintf('Employee record updated for %s.', $displayName);
        }

        $labels = array_map(static fn (array $change): string => $change['fieldLabel'], array_slice($changes, 0, 3));
        $suffix = $count > 3 ? sprintf(' and %d more field(s)', $count - 3) : '';

        return sprintf('Employee record updated for %s: %s%s.', $displayName, implode(', ', $labels), $suffix);
    }
}
