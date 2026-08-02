<?php

declare(strict_types=1);

namespace App\Pim\EmployeeAudit\Enum;

enum EmployeeAuditActionType: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case DEACTIVATE = 'deactivate';
}
