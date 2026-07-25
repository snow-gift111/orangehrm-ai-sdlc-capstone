package com.orangehrm.leavealert.api.dto;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.UUID;

public record AlertEventResponse(
        UUID id,
        UUID ruleId,
        String employeeId,
        String leaveTypeId,
        BigDecimal evaluatedBalance,
        BigDecimal thresholdValue,
        Instant evaluatedAt,
        String channel,
        String status,
        String failureReason
) {
}
