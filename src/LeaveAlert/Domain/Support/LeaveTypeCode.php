<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Support;

/**
 * Leave type context helper.
 *
 * The approved sprint scope delivers a global/default leave type context.
 * Advanced leave-type-specific threshold administration is excluded from this
 * sprint, but the data model already carries the leave type code so that the
 * capability can be extended without schema redesign.
 */
final class LeaveTypeCode
{
    public const DEFAULT = 'DEFAULT';

    public const MAX_LENGTH = 50;

    private function __construct()
    {
    }

    /**
     * Normalizes a supplied leave type code to a canonical, storable value.
     */
    public static function normalize(?string $value): string
    {
        if ($value === null) {
            return self::DEFAULT;
        }

        $normalized = strtoupper(trim($value));

        if ($normalized === '') {
            return self::DEFAULT;
        }

        return $normalized;
    }

    public static function isValid(?string $value): bool
    {
        if ($value === null) {
            return true;
        }

        $normalized = strtoupper(trim($value));

        if ($normalized === '') {
            return true;
        }

        return (bool) preg_match('/^[A-Z0-9_\-]{1,' . self::MAX_LENGTH . '}$/', $normalized);
    }
}
