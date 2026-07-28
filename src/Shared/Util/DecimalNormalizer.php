<?php

declare(strict_types=1);

namespace App\Shared\Util;

use App\Shared\Exception\ValidationException;

/**
 * Ensures consistent decimal precision for leave balances and thresholds.
 *
 * All monetary-like leave values are stored as DECIMAL(10,2) strings so that
 * threshold comparisons and duplicate detection remain deterministic.
 */
final class DecimalNormalizer
{
    public const SCALE = 2;

    private const MAX_ABSOLUTE_VALUE = 99999999.99;

    private function __construct()
    {
    }

    /**
     * Normalizes an arbitrary scalar input into a fixed-scale decimal string.
     *
     * @throws ValidationException when the value is not a valid decimal number
     */
    public static function normalize(mixed $value, string $field): string
    {
        if (is_bool($value) || $value === null || $value === '') {
            throw ValidationException::forField($field, sprintf('%s is required.', $field));
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        if (!is_numeric($value)) {
            throw ValidationException::forField($field, sprintf('%s must be a numeric value.', $field));
        }

        $floatValue = (float) $value;

        if (!is_finite($floatValue)) {
            throw ValidationException::forField($field, sprintf('%s must be a finite numeric value.', $field));
        }

        if (abs($floatValue) > self::MAX_ABSOLUTE_VALUE) {
            throw ValidationException::forField($field, sprintf('%s exceeds the maximum supported value.', $field));
        }

        return number_format($floatValue, self::SCALE, '.', '');
    }

    /**
     * Normalizes a value that must not be negative (LBA-FR-012).
     *
     * @throws ValidationException
     */
    public static function normalizeNonNegative(mixed $value, string $field): string
    {
        $normalized = self::normalize($value, $field);

        if ((float) $normalized < 0.0) {
            throw ValidationException::forField($field, sprintf('%s must not be negative.', $field));
        }

        return $normalized;
    }

    /**
     * Compares two fixed-scale decimal strings.
     *
     * @return int -1 when $left < $right, 0 when equal, 1 when $left > $right
     */
    public static function compare(string $left, string $right): int
    {
        $leftScaled = (int) round((float) $left * (10 ** self::SCALE));
        $rightScaled = (int) round((float) $right * (10 ** self::SCALE));

        return $leftScaled <=> $rightScaled;
    }
}
