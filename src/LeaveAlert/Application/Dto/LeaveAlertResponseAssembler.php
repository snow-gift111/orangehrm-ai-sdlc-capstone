<?php

declare(strict_types=1);

namespace App\LeaveAlert\Application\Dto;

use App\LeaveAlert\Domain\Entity\InAppNotification;
use App\LeaveAlert\Domain\Entity\LeaveAlertRule;
use App\LeaveAlert\Domain\Entity\LeaveBalance;
use App\LeaveAlert\Domain\Entity\LeaveBalanceAlert;

/**
 * Builds API response payloads for the Leave Balance Alert capability.
 *
 * Only fields approved for the sprint are exposed; no additional employee data
 * is disclosed beyond employee name, Employee ID, leave type, current balance,
 * threshold and alert date (LBA-FR-026, LBA-FR-027).
 */
final class LeaveAlertResponseAssembler
{
    /**
     * @return array<string, mixed>
     */
    public function leaveBalance(LeaveBalance $leaveBalance): array
    {
        return [
            'employeeId' => $leaveBalance->getEmployee()->getEmployeeId(),
            'employeeName' => $leaveBalance->getEmployee()->getFullName(),
            'leaveTypeCode' => $leaveBalance->getLeaveTypeCode(),
            'currentBalance' => $leaveBalance->getCurrentBalance(),
            'updatedAt' => $leaveBalance->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }

    /**
     * @param list<LeaveBalance> $leaveBalances
     *
     * @return list<array<string, mixed>>
     */
    public function leaveBalanceList(array $leaveBalances): array
    {
        return array_map(fn (LeaveBalance $balance): array => $this->leaveBalance($balance), $leaveBalances);
    }

    /**
     * @return array<string, mixed>
     */
    public function alertRule(LeaveAlertRule $rule): array
    {
        $recipientTypes = array_map(
            static fn ($recipientType): string => $recipientType->value,
            $rule->getRecipientTypes()
        );

        sort($recipientTypes);

        return [
            'id' => $rule->getId(),
            'ruleName' => $rule->getRuleName(),
            'thresholdValue' => $rule->getThresholdValue(),
            'triggerCondition' => $rule->getTriggerCondition()->value,
            'leaveTypeCode' => $rule->getLeaveTypeCode(),
            'active' => $rule->isActive(),
            'recipientTypes' => array_values($recipientTypes),
            'createdAt' => $rule->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'updatedAt' => $rule->getUpdatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }

    /**
     * @param list<LeaveAlertRule> $rules
     *
     * @return list<array<string, mixed>>
     */
    public function alertRuleList(array $rules): array
    {
        return array_map(fn (LeaveAlertRule $rule): array => $this->alertRule($rule), $rules);
    }

    /**
     * @return array<string, mixed>
     */
    public function alert(LeaveBalanceAlert $alert): array
    {
        return [
            'id' => $alert->getId(),
            'employeeId' => $alert->getEmployee()->getEmployeeId(),
            'employeeName' => $alert->getEmployee()->getFullName(),
            'leaveTypeCode' => $alert->getLeaveTypeCode(),
            'currentBalance' => $alert->getCurrentBalanceAtAlert(),
            'thresholdValue' => $alert->getThresholdValueAtAlert(),
            'triggerCondition' => $alert->getTriggerCondition()->value,
            'status' => $alert->getStatus()->value,
            'ruleId' => $alert->getRule()->getId(),
            'ruleName' => $alert->getRule()->getRuleName(),
            'alertDate' => $alert->getAlertGeneratedAt()->format(\DateTimeInterface::ATOM),
        ];
    }

    /**
     * @param list<LeaveBalanceAlert> $alerts
     *
     * @return list<array<string, mixed>>
     */
    public function alertList(array $alerts): array
    {
        return array_map(fn (LeaveBalanceAlert $alert): array => $this->alert($alert), $alerts);
    }

    /**
     * @return array<string, mixed>
     */
    public function notification(InAppNotification $notification): array
    {
        return [
            'id' => $notification->getId(),
            'notificationType' => $notification->getNotificationType(),
            'title' => $notification->getTitle(),
            'message' => $notification->getMessage(),
            'createdAt' => $notification->getCreatedAt()->format(\DateTimeInterface::ATOM),
            'alert' => $this->alert($notification->getAlert()),
        ];
    }

    /**
     * @param list<InAppNotification> $notifications
     *
     * @return list<array<string, mixed>>
     */
    public function notificationList(array $notifications): array
    {
        return array_map(
            fn (InAppNotification $notification): array => $this->notification($notification),
            $notifications
        );
    }
}
