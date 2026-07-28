<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Enum;

enum AlertType: string
{
    case LOW_BALANCE = 'LOW_BALANCE';
    case INSUFFICIENT_BALANCE = 'INSUFFICIENT_BALANCE';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }
}
