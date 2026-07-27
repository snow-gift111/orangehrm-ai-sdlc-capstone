package com.orangehrm.lba.api.dto;

import com.orangehrm.lba.domain.enums.LeaveTypeScope;
import com.orangehrm.lba.domain.enums.RuleStatus;
import com.orangehrm.lba.domain.enums.ThresholdOperator;
import com.orangehrm.lba.domain.model.AlertRecipientConfig;

import java.math.BigDecimal;
import java.time.Instant;

public record RuleResponse(
        Long id,
        LeaveTypeScope leaveTypeScope,
        Long leaveTypeId,
        ThresholdOperator operator,
        BigDecimal thresholdValue,
        Integer suppressionWindowDays,
        AlertRecipientConfig recipients,
        RuleStatus status,
        Instant createdAt,
        String createdBy,
        Instant updatedAt,
        String updatedBy
) {
}
