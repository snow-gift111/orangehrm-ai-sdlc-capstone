<?php

declare(strict_types=1);

namespace App\Controller;

use App\Security\AuditAuthorizationService;
use App\Service\Audit\AuditQueryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class AuditController extends AbstractController
{
    public function __construct(
        private readonly AuditAuthorizationService $authorizationService,
        private readonly AuditQueryService $auditQueryService,
    ) {
    }

    #[Route('/api/v1/pim/employees/{employeeIdentifier}/audit-events', name: 'employee_audit_history', methods: ['GET'])]
    public function history(string $employeeIdentifier): JsonResponse
    {
        if (!$this->authorizationService->isAuthorized()) {
            return $this->accessDeniedResponse();
        }

        $items = array_map(
            static fn ($item): array => $item->toArray(),
            $this->auditQueryService->getEmployeeHistory($employeeIdentifier)
        );

        return $this->json([
            'data' => $items,
            'message' => $items === [] ? 'No Audit Records Found' : null,
        ]);
    }

    #[Route('/api/v1/pim/audit-events/{auditEventId}', name: 'employee_audit_event_detail', requirements: ['auditEventId' => '\\d+'], methods: ['GET'])]
    public function detail(int $auditEventId): JsonResponse
    {
        if (!$this->authorizationService->isAuthorized()) {
            return $this->accessDeniedResponse();
        }

        return $this->json(['data' => $this->auditQueryService->getEventDetail($auditEventId)->toArray()]);
    }

    private function accessDeniedResponse(): JsonResponse
    {
        return $this->json([
            'error' => [
                'code' => 'AUDIT_ACCESS_DENIED',
                'message' => 'Access denied. You are not authorized to view employee audit history.',
            ],
        ], JsonResponse::HTTP_FORBIDDEN);
    }
}
