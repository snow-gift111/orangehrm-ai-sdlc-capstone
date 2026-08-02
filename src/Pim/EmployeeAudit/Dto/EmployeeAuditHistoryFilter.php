<?php

declare(strict_types=1);

namespace App\Pim\EmployeeAudit\Dto;

use App\Pim\EmployeeAudit\Model\EmployeeAuditEventType;

final readonly class EmployeeAuditHistoryFilter
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PAGE_SIZE = 25;
    private const MAX_PAGE_SIZE = 100;

    public function __construct(
        public ?EmployeeAuditEventType $eventType = null,
        public ?\DateTimeImmutable $dateFrom = null,
        public ?\DateTimeImmutable $dateTo = null,
        public ?int $actorUserId = null,
        public int $page = self::DEFAULT_PAGE,
        public int $pageSize = self::DEFAULT_PAGE_SIZE,
    ) {
        if ($this->page < 1) {
            throw new \InvalidArgumentException('Audit history page must be greater than or equal to 1.');
        }

        if ($this->pageSize < 1 || $this->pageSize > self::MAX_PAGE_SIZE) {
            throw new \InvalidArgumentException(sprintf('Audit history page size must be between 1 and %d.', self::MAX_PAGE_SIZE));
        }

        if ($this->dateFrom !== null && $this->dateTo !== null && $this->dateFrom > $this->dateTo) {
            throw new \InvalidArgumentException('Audit history dateFrom must be earlier than or equal to dateTo.');
        }
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->pageSize;
    }
}
