<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Enum;

/**
 * Supported alert trigger conditions.
 *
 * Approved Solution Design allows exactly two conditions:
 *  - EQUAL_TO            : alert when balance equals the threshold
 *  - EQUAL_TO_OR_BELOW   : alert when balance is equal to or below the threshold
 */
enum TriggerCondition: string
{
    case EQUAL_TO = 'EQUAL_TO';
    case EQUAL_TO_OR_BELOW = 'EQUAL_TO_OR_BELOW';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    public static function tryFromLabel(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom(strtoupper(trim($value)));
    }
}
