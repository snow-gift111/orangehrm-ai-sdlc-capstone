package com.orangehrm.alert.api.dto;

import com.orangehrm.alert.domain.AlertComparisonOperator;
import com.orangehrm.alert.domain.AlertFrequencyUnit;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.List;

public record AlertRuleResponse(
        Long id,
        String name,
        Long leaveTypeId,
        BigDecimal thresholdValue,
        AlertComparisonOperator comparisonOperator,
        int frequencyValue,
        AlertFrequencyUnit frequencyUnit,
        List<AlertRuleRecipientDto> recipients,
        boolean isActive,
        Instant createdAt,
        Instant updatedAt
) {
}
