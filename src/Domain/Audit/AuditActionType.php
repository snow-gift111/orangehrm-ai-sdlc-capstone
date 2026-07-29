<?php

declare(strict_types=1);

namespace App\Domain\Audit;

enum AuditActionType: string
{
    case CREATE = 'Create';
    case UPDATE = 'Update';
    case DELETE = 'Delete';

    public static function isValid(string $actionType): bool
    {
        return in_array($actionType, array_column(self::cases(), 'value'), true);
    }
}
