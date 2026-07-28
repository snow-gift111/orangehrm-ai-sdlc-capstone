<?php

declare(strict_types=1);

namespace App\LeaveAlert\Application\Service;

use App\Audit\Application\Service\AuditService;
use App\LeaveAlert\Application\Dto\AlertRuleRequest;
use App\LeaveAlert\Domain\Entity\LeaveAlertRule;
use App\LeaveAlert\Domain\Enum\AuditActionType;
use App\LeaveAlert\Infrastructure\Repository\LeaveAlertRuleRepository;
use App\Security\Domain\Entity\User;
use App\Shared\Exception\NotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Alert rule administration.
 *
 * Requirements: LBA-FR-006 .. LBA-FR-013, LBA-FR-035, LBA-FR-039.
 *
 * Every mutating operation runs inside a single transaction together with its
 * audit event so that no partial rule state can remain.
 */
final class AlertRuleService
{
    private const ENTITY_TYPE = 'LEAVE_ALERT_RULE';

    public function __construct(
        private readonly LeaveAlertRuleRepository $ruleRepository,
        private readonly LeaveAlertAuthorizationService $authorizationService,
        private readonly AuditService $auditService,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return list<LeaveAlertRule>
     */
    public function listRules(User $currentUser): array
    {
        $this->authorizationService->assertCanManageAlertRules($currentUser);

        return $this->ruleRepository->findAllOrdered();
    }

    public function getRule(User $currentUser, int $ruleId): LeaveAlertRule
    {
        $this->authorizationService->assertCanManageAlertRules($currentUser);

        $rule = $this->ruleRepository->find($ruleId);

        if ($rule === null) {
            throw NotFoundException::alertRule();
        }

        return $rule;
    }

    public function createRule(User $currentUser, AlertRuleRequest $request): LeaveAlertRule
    {
        $this->authorizationService->assertCanManageAlertRules($currentUser);

        $now = new \DateTimeImmutable();

        $rule = new LeaveAlertRule(
            $request->ruleName,
            $request->thresholdValue,
            $request->triggerCondition,
            $request->leaveTypeCode,
            $request->active,
            $currentUser,
            $now
        );

        $rule->replaceRecipientTypes($request->recipientTypes, $now);

        return $this->entityManager->wrapInTransaction(function () use ($rule, $currentUser): LeaveAlertRule {
            $this->ruleRepository->save($rule);
            $this->entityManager->flush();

            $this->auditService->record(
                $currentUser,
                AuditActionType::LEAVE_ALERT_RULE_CREATED,
                self::ENTITY_TYPE,
                (string) $rule->getId(),
                sprintf('Leave balance alert rule "%s" created.', $rule->getRuleName()),
                $this->ruleMetadata($rule)
            );

            $this->logger->info('Leave alert rule created.', [
                'rule_id' => $rule->getId(),
                'actor_user_id' => $currentUser->getId(),
            ]);

            return $rule;
        });
    }

    public function updateRule(User $currentUser, int $ruleId, AlertRuleRequest $request): LeaveAlertRule
    {
        $this->authorizationService->assertCanManageAlertRules($currentUser);

        $rule = $this->ruleRepository->find($ruleId);

        if ($rule === null) {
            throw NotFoundException::alertRule();
        }

        $now = new \DateTimeImmutable();

        return $this->entityManager->wrapInTransaction(
            function () use ($rule, $request, $currentUser, $now): LeaveAlertRule {
                $rule->applyChanges(
                    $request->ruleName,
                    $request->thresholdValue,
                    $request->triggerCondition,
                    $request->leaveTypeCode,
                    $currentUser,
                    $now
                );

                $rule->replaceRecipientTypes($request->recipientTypes, $now);

                $this->entityManager->flush();

                $this->auditService->record(
                    $currentUser,
                    AuditActionType::LEAVE_ALERT_RULE_UPDATED,
                    self::ENTITY_TYPE,
                    (string) $rule->getId(),
                    sprintf('Leave balance alert rule "%s" updated.', $rule->getRuleName()),
                    $this->ruleMetadata($rule)
                );

                $this->logger->info('Leave alert rule updated.', [
                    'rule_id' => $rule->getId(),
                    'actor_user_id' => $currentUser->getId(),
                ]);

                return $rule;
            }
        );
    }

    public function activateRule(User $currentUser, int $ruleId): LeaveAlertRule
    {
        return $this->changeStatus($currentUser, $ruleId, true);
    }

    public function deactivateRule(User $currentUser, int $ruleId): LeaveAlertRule
    {
        return $this->changeStatus($currentUser, $ruleId, false);
    }

    private function changeStatus(User $currentUser, int $ruleId, bool $activate): LeaveAlertRule
    {
        $this->authorizationService->assertCanManageAlertRules($currentUser);

        $rule = $this->ruleRepository->find($ruleId);

        if ($rule === null) {
            throw NotFoundException::alertRule();
        }

        $now = new \DateTimeImmutable();

        return $this->entityManager->wrapInTransaction(
            function () use ($rule, $activate, $currentUser, $now): LeaveAlertRule {
                if ($activate) {
                    $rule->activate($currentUser, $now);
                    $actionType = AuditActionType::LEAVE_ALERT_RULE_ACTIVATED;
                    $summary = sprintf('Leave balance alert rule "%s" activated.', $rule->getRuleName());
                } else {
                    $rule->deactivate($currentUser, $now);
                    $actionType = AuditActionType::LEAVE_ALERT_RULE_DEACTIVATED;
                    $summary = sprintf('Leave balance alert rule "%s" deactivated.', $rule->getRuleName());
                }

                $this->entityManager->flush();

                $this->auditService->record(
                    $currentUser,
                    $actionType,
                    self::ENTITY_TYPE,
                    (string) $rule->getId(),
                    $summary,
                    $this->ruleMetadata($rule)
                );

                $this->logger->info('Leave alert rule status changed.', [
                    'rule_id' => $rule->getId(),
                    'active' => $rule->isActive(),
                    'actor_user_id' => $currentUser->getId(),
                ]);

                return $rule;
            }
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function ruleMetadata(LeaveAlertRule $rule): array
    {
        return [
            'ruleName' => $rule->getRuleName(),
            'thresholdValue' => $rule->getThresholdValue(),
            'triggerCondition' => $rule->getTriggerCondition()->value,
            'leaveTypeCode' => $rule->getLeaveTypeCode(),
            'active' => $rule->isActive(),
            'recipientTypes' => array_map(
                static fn ($recipientType): string => $recipientType->value,
                $rule->getRecipientTypes()
            ),
        ];
    }
}
