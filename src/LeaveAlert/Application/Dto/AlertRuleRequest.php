<?php

declare(strict_types=1);

namespace App\LeaveAlert\Application\Dto;

use App\LeaveAlert\Domain\Enum\RecipientType;
use App\LeaveAlert\Domain\Enum\TriggerCondition;
use App\LeaveAlert\Domain\Support\LeaveTypeCode;
use App\Shared\Exception\ValidationException;
use App\Shared\Util\DecimalNormalizer;

/**
 * Carries alert rule input for create and update operations.
 *
 * Validation implements LBA-FR-012 and LBA-FR-013:
 *  - threshold must be present, numeric and non-negative
 *  - at least one valid recipient type must be supplied
 */
final class AlertRuleRequest
{
    /**
     * @param list<RecipientType> $recipientTypes
     */
    private function __construct(
        public readonly string $ruleName,
        public readonly string $thresholdValue,
        public readonly TriggerCondition $triggerCondition,
        public readonly ?string $leaveTypeCode,
        public readonly array $recipientTypes,
        public readonly bool $active,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws ValidationException
     */
    public static function fromArray(array $payload, bool $defaultActive = true): self
    {
        $fieldErrors = [];

        $ruleName = is_string($payload['ruleName'] ?? null) ? trim((string) $payload['ruleName']) : '';

        if ($ruleName === '') {
            $fieldErrors['ruleName'] = 'Rule name is required.';
        } elseif (mb_strlen($ruleName) > 150) {
            $fieldErrors['ruleName'] = 'Rule name must not exceed 150 characters.';
        }

        $thresholdValue = null;

        if (!array_key_exists('thresholdValue', $payload) || $payload['thresholdValue'] === null || $payload['thresholdValue'] === '') {
            $fieldErrors['thresholdValue'] = 'Threshold value is required.';
        } else {
            try {
                $thresholdValue = DecimalNormalizer::normalizeNonNegative($payload['thresholdValue'], 'thresholdValue');
            } catch (ValidationException $exception) {
                $fieldErrors += $exception->getFieldErrors();
            }
        }

        $triggerCondition = TriggerCondition::tryFromLabel(
            is_string($payload['triggerCondition'] ?? null) ? (string) $payload['triggerCondition'] : null
        );

        if ($triggerCondition === null) {
            $fieldErrors['triggerCondition'] = sprintf(
                'Trigger condition must be one of: %s.',
                implode(', ', TriggerCondition::values())
            );
        }

        $leaveTypeCodeInput = $payload['leaveTypeCode'] ?? null;
        $leaveTypeCode = null;

        if ($leaveTypeCodeInput !== null && $leaveTypeCodeInput !== '') {
            if (!is_string($leaveTypeCodeInput) || !LeaveTypeCode::isValid($leaveTypeCodeInput)) {
                $fieldErrors['leaveTypeCode'] = 'Leave type code contains unsupported characters.';
            } else {
                $leaveTypeCode = LeaveTypeCode::normalize($leaveTypeCodeInput);
            }
        }

        $recipientTypes = self::parseRecipients($payload['recipientTypes'] ?? null, $fieldErrors);

        $active = $defaultActive;

        if (array_key_exists('active', $payload)) {
            $activeValue = $payload['active'];

            if (is_bool($activeValue)) {
                $active = $activeValue;
            } elseif (in_array($activeValue, ['true', 'false', 1, 0, '1', '0'], true)) {
                $active = in_array($activeValue, ['true', 1, '1'], true);
            } else {
                $fieldErrors['active'] = 'Active flag must be a boolean value.';
            }
        }

        if ($fieldErrors !== []) {
            throw new ValidationException($fieldErrors);
        }

        /** @var string $thresholdValue */
        /** @var TriggerCondition $triggerCondition */
        return new self(
            $ruleName,
            $thresholdValue,
            $triggerCondition,
            $leaveTypeCode,
            $recipientTypes,
            $active
        );
    }

    /**
     * @param array<string, string> $fieldErrors
     *
     * @return list<RecipientType>
     */
    private static function parseRecipients(mixed $input, array &$fieldErrors): array
    {
        if (!is_array($input) || $input === []) {
            $fieldErrors['recipientTypes'] = 'At least one recipient type is required.';

            return [];
        }

        $recipientTypes = [];

        foreach ($input as $value) {
            if (!is_string($value)) {
                $fieldErrors['recipientTypes'] = 'Recipient types must be provided as strings.';

                return [];
            }

            $recipientType = RecipientType::tryFromLabel($value);

            if ($recipientType === null) {
                $fieldErrors['recipientTypes'] = sprintf(
                    'Recipient types must be one of: %s.',
                    implode(', ', RecipientType::values())
                );

                return [];
            }

            $recipientTypes[$recipientType->value] = $recipientType;
        }

        if ($recipientTypes === []) {
            $fieldErrors['recipientTypes'] = 'At least one recipient type is required.';
        }

        return array_values($recipientTypes);
    }
}
