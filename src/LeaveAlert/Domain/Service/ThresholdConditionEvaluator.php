<?php

declare(strict_types=1);

namespace App\LeaveAlert\Domain\Service;

use App\LeaveAlert\Domain\Enum\TriggerCondition;
use App\Shared\Util\DecimalNormalizer;

/**
 * Evaluates a current leave balance against a rule threshold condition.
 *
 * Requirements: LBA-FR-008, LBA-FR-016, LBA-FR-017.
 */
final class ThresholdConditionEvaluator
{
    /**
     * @param string $currentBalance fixed-scale decimal string
     * @param string $thresholdValue fixed-scale decimal string
     */
    public function isTriggered(string $currentBalance, string $thresholdValue, TriggerCondition $condition): bool
    {
        $comparison = DecimalNormalizer::compare($currentBalance, $thresholdValue);

        return match ($condition) {
            TriggerCondition::EQUAL_TO => $comparison === 0,
            TriggerCondition::EQUAL_TO_OR_BELOW => $comparison <= 0,
        };
    }
}
