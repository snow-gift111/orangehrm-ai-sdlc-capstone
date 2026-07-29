<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class AuditHistoryItemDto
{
    public function __construct(
        public int $auditEventId,
        public string $employeeId,
        public ?string $employeeName,
        public string $actionType,
        public string $changedBy,
        public string $timestamp,
        public ?string $summary,
    ) {
    }

    /** @return array<string, int|string|null> */
    public function toArray(): array
    {
        return [
            'auditEventId' => $this->auditEventId,
            'employeeId' => $this->employeeId,
            'employeeName' => $this->employeeName,
            'actionType' => $this->actionType,
            'changedBy' => $this->changedBy,
            'timestamp' => $this->timestamp,
            'summary' => $this->summary,
        ];
    }
}
