<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Enum;

/**
 * Audit action types required by the approved Solution Design.
 */
enum AuditActionType: string
{
    case LEAVE_ALERT_RULE_CREATED = 'LEAVE_ALERT_RULE_CREATED';
    case LEAVE_ALERT_RULE_UPDATED = 'LEAVE_ALERT_RULE_UPDATED';
    case LEAVE_ALERT_RULE_ACTIVATED = 'LEAVE_ALERT_RULE_ACTIVATED';
    case LEAVE_ALERT_RULE_DEACTIVATED = 'LEAVE_ALERT_RULE_DEACTIVATED';
    case LEAVE_BALANCE_ALERT_GENERATED = 'LEAVE_BALANCE_ALERT_GENERATED';
}
