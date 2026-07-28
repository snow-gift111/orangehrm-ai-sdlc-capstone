<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Enum;

/**
 * Leave balance alert status.
 *
 * The approved sprint scope generates ACTIVE alerts only. RESOLVED is declared
 * because the alert status column is persisted, but automatic resolution is
 * explicitly excluded from this sprint.
 */
enum AlertStatus: string
{
    case ACTIVE = 'ACTIVE';
    case RESOLVED = 'RESOLVED';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }
}
