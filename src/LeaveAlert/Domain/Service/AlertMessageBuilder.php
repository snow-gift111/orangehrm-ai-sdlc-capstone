<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Service;

use App\LeaveAlert\Domain\Entity\LeaveBalanceAlert;

/**
 * Builds standard alert notification content.
 *
 * Notification content includes employee name, Employee ID, leave type,
 * current balance, threshold and alert date (LBA-FR-026).
 */
final class AlertMessageBuilder
{
    public function buildTitle(LeaveBalanceAlert $alert): string
    {
        return sprintf(
            'Low leave balance: %s (%s)',
            $alert->getEmployee()->getFullName(),
            $alert->getEmployee()->getEmployeeId()
        );
    }

    public function buildMessage(LeaveBalanceAlert $alert): string
    {
        return sprintf(
            'Employee %s (Employee ID %s) has a %s leave balance of %s, which meets the configured alert '
            . 'threshold of %s. Alert generated on %s.',
            $alert->getEmployee()->getFullName(),
            $alert->getEmployee()->getEmployeeId(),
            $alert->getLeaveTypeCode(),
            $alert->getCurrentBalanceAtAlert(),
            $alert->getThresholdValueAtAlert(),
            $alert->getAlertGeneratedAt()->format(\DateTimeInterface::ATOM)
        );
    }
}
