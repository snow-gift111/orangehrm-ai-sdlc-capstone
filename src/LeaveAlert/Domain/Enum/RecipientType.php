<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Enum;

/**
 * Recipient types supported for the sprint MVP.
 */
enum RecipientType: string
{
    case EMPLOYEE = 'EMPLOYEE';
    case HR_ADMIN = 'HR_ADMIN';
    case HR_USER = 'HR_USER';

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

    /**
     * Role name associated with a recipient type, when the recipient type
     * resolves to a role rather than to the alert subject employee.
     */
    public function roleName(): ?string
    {
        return match ($this) {
            self::HR_ADMIN => 'HR_ADMIN',
            self::HR_USER => 'HR_USER',
            self::EMPLOYEE => null,
        };
    }
}
