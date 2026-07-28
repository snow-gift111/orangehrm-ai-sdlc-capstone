<?php

declare(strict_types=1);

namespace App\LeaveAlert\Application\Dto;

use App\LeaveAlert\Domain\Support\LeaveTypeCode;
use App\Shared\Exception\ValidationException;
use App\Shared\Util\DecimalNormalizer;

/**
 * Carries leave balance maintenance input.
 *
 * Negative balances are rejected by default; the policy is configurable through
 * the application parameter `leave_alert.allow_negative_balance` because the
 * approved requirements state that negative balance support depends on business
 * leave policy.
 */
final class LeaveBalanceUpdateRequest
{
    private function __construct(
        public readonly string $currentBalance,
        public readonly string $leaveTypeCode,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws ValidationException
     */
    public static function fromArray(array $payload, bool $allowNegativeBalance): self
    {
        $fieldErrors = [];
        $currentBalance = null;

        if (!array_key_exists('currentBalance', $payload) || $payload['currentBalance'] === null || $payload['currentBalance'] === '') {
            $fieldErrors['currentBalance'] = 'Current balance is required.';
        } else {
            try {
                $currentBalance = $allowNegativeBalance
                    ? DecimalNormalizer::normalize($payload['currentBalance'], 'currentBalance')
                    : DecimalNormalizer::normalizeNonNegative($payload['currentBalance'], 'currentBalance');
            } catch (ValidationException $exception) {
                $fieldErrors += $exception->getFieldErrors();
            }
        }

        $leaveTypeCodeInput = $payload['leaveTypeCode'] ?? null;

        if ($leaveTypeCodeInput !== null && $leaveTypeCodeInput !== '' && (!is_string($leaveTypeCodeInput) || !LeaveTypeCode::isValid($leaveTypeCodeInput))) {
            $fieldErrors['leaveTypeCode'] = 'Leave type code contains unsupported characters.';
        }

        if ($fieldErrors !== []) {
            throw new ValidationException($fieldErrors);
        }

        /** @var string $currentBalance */
        return new self(
            $currentBalance,
            LeaveTypeCode::normalize(is_string($leaveTypeCodeInput) ? $leaveTypeCodeInput : null)
        );
    }
}
