<?php

declare(strict_types=1);

namespace App\Audit\Application\Service;

use App\Audit\Domain\Entity\AuditEvent;
use App\Audit\Infrastructure\Repository\AuditEventRepository;
use App\LeaveAlert\Domain\Enum\AuditActionType;
use App\Security\Domain\Entity\User;
use Psr\Log\LoggerInterface;

/**
 * Records audit events for auditable Leave Balance Alert operations.
 *
 * Requirements: LBA-FR-039, LBA-FR-040, LBA-NFR-009.
 *
 * Audit events participate in the caller's transaction: a failure to record an
 * audit event fails the business operation, as required by the approved error
 * handling design.
 */
final class AuditService
{
    public function __construct(
        private readonly AuditEventRepository $auditEventRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string, mixed> $metadata
     */
    public function record(
        ?User $actor,
        AuditActionType $actionType,
        string $entityType,
        string $entityId,
        string $summary,
        array $metadata = [],
    ): AuditEvent {
        $auditEvent = new AuditEvent(
            $actor,
            $actionType,
            $entityType,
            $entityId,
            $summary,
            $metadata === [] ? null : $metadata
        );

        $this->auditEventRepository->save($auditEvent);

        $this->logger->info('Audit event recorded.', [
            'action_type' => $actionType->value,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'actor_user_id' => $actor?->getId(),
        ]);

        return $auditEvent;
    }
}
