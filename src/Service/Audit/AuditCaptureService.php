<?php

declare(strict_types=1);

namespace App\Service\Audit;

use App\Domain\Audit\AuditActionType;
use App\Entity\Employee;
use App\Entity\EmployeeAuditChangeDetail;
use App\Entity\EmployeeAuditEvent;
use App\Repository\EmployeeAuditEventRepository;
use App\Security\CurrentUserContext;

final readonly class AuditCaptureService
{
    public function __construct(
        private EmployeeAuditEventRepository $eventRepository,
        private EmployeeChangeComparator $changeComparator,
        private EmployeeAuditFieldRegistry $fieldRegistry,
        private AuditFieldValueFormatter $valueFormatter,
        private AuditSummaryBuilder $summaryBuilder,
        private CurrentUserContext $currentUserContext,
    ) {
    }

    public function captureCreate(Employee $employee): EmployeeAuditEvent
    {
        $snapshot = $employee->toAuditSnapshot();
        $details = $this->buildIdentifyingDetails($snapshot, true);

        return $this->persistEvent(AuditActionType::CREATE, $snapshot, $details);
    }

    /**
     * @param array<string, mixed> $previousSnapshot
     */
    public function captureUpdate(array $previousSnapshot, Employee $savedEmployee): ?EmployeeAuditEvent
    {
        $newSnapshot = $savedEmployee->toAuditSnapshot();
        $changes = $this->changeComparator->compare($previousSnapshot, $newSnapshot);

        if ($changes === []) {
            return null;
        }

        return $this->persistEvent(AuditActionType::UPDATE, $newSnapshot, $changes);
    }

    /**
     * @param array<string, mixed> $employeeSnapshot
     */
    public function captureDelete(array $employeeSnapshot): EmployeeAuditEvent
    {
        $details = $this->buildIdentifyingDetails($employeeSnapshot, false);

        return $this->persistEvent(AuditActionType::DELETE, $employeeSnapshot, $details);
    }

    /**
     * @param array<string, mixed> $employeeSnapshot
     * @param list<array{fieldName: string, fieldLabel: string, previousValue: ?string, newValue: ?string}> $details
     */
    private function persistEvent(AuditActionType $actionType, array $employeeSnapshot, array $details): EmployeeAuditEvent
    {
        $employeeId = (string) ($employeeSnapshot['employeeId'] ?? 'unknown');
        $employeeName = isset($employeeSnapshot['employeeName']) ? (string) $employeeSnapshot['employeeName'] : null;
        $occurredAt = new \DateTimeImmutable();

        $event = new EmployeeAuditEvent(
            isset($employeeSnapshot['internalId']) ? (int) $employeeSnapshot['internalId'] : null,
            $employeeId,
            $employeeName,
            $actionType,
            $this->currentUserContext->getUserId(),
            $this->currentUserContext->getUsername(),
            $occurredAt,
            $this->summaryBuilder->build($actionType, $employeeName, $employeeId, $details),
            null
        );

        foreach ($details as $index => $detail) {
            new EmployeeAuditChangeDetail(
                $event,
                $detail['fieldName'],
                $detail['fieldLabel'],
                $detail['previousValue'],
                $detail['newValue'],
                $index + 1
            );
        }

        $this->eventRepository->save($event);

        return $event;
    }

    /**
     * @param array<string, mixed> $snapshot
     * @return list<array{fieldName: string, fieldLabel: string, previousValue: ?string, newValue: ?string}>
     */
    private function buildIdentifyingDetails(array $snapshot, bool $isCreate): array
    {
        $fields = ['employeeId', 'employeeName', 'firstName', 'middleName', 'lastName', 'jobTitle', 'employmentStatus'];
        $details = [];

        foreach ($fields as $fieldName) {
            if (!array_key_exists($fieldName, $snapshot)) {
                continue;
            }

            $value = $this->valueFormatter->format($snapshot[$fieldName]);
            if ($value === null) {
                continue;
            }

            $details[] = [
                'fieldName' => $fieldName,
                'fieldLabel' => $this->fieldRegistry->labelFor($fieldName),
                'previousValue' => $isCreate ? null : $value,
                'newValue' => $isCreate ? $value : null,
            ];
        }

        return $details;
    }
}
