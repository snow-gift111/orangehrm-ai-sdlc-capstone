<?php

declare(strict_types=1);

namespace App\Service\Audit;

use App\Dto\AuditChangeDetailDto;
use App\Dto\AuditEventDetailDto;
use App\Dto\AuditHistoryItemDto;
use App\Entity\EmployeeAuditEvent;
use App\Repository\EmployeeAuditEventRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class AuditQueryService
{
    public function __construct(private EmployeeAuditEventRepository $eventRepository)
    {
    }

    /** @return list<AuditHistoryItemDto> */
    public function getEmployeeHistory(string $employeeIdentifier): array
    {
        return array_map(
            fn (EmployeeAuditEvent $event): AuditHistoryItemDto => $this->mapHistoryItem($event),
            $this->eventRepository->findHistoryByEmployeeIdentifier($employeeIdentifier)
        );
    }

    public function getEventDetail(int $auditEventId): AuditEventDetailDto
    {
        $event = $this->eventRepository->find($auditEventId);
        if (!$event instanceof EmployeeAuditEvent) {
            throw new NotFoundHttpException('Audit event not found.');
        }

        $changes = [];
        foreach ($event->getDetails() as $detail) {
            $changes[] = new AuditChangeDetailDto(
                $detail->getFieldName(),
                $detail->getFieldLabel(),
                $detail->getPreviousValue(),
                $detail->getNewValue()
            );
        }

        return new AuditEventDetailDto(
            (int) $event->getId(),
            $event->getEmployeeIdSnapshot(),
            $event->getEmployeeNameSnapshot(),
            $event->getActionType(),
            $event->getChangedByUsernameSnapshot(),
            $event->getOccurredAt()->format(\DateTimeInterface::ATOM),
            $event->getSummary(),
            $changes
        );
    }

    private function mapHistoryItem(EmployeeAuditEvent $event): AuditHistoryItemDto
    {
        return new AuditHistoryItemDto(
            (int) $event->getId(),
            $event->getEmployeeIdSnapshot(),
            $event->getEmployeeNameSnapshot(),
            $event->getActionType(),
            $event->getChangedByUsernameSnapshot(),
            $event->getOccurredAt()->format(\DateTimeInterface::ATOM),
            $event->getSummary()
        );
    }
}
