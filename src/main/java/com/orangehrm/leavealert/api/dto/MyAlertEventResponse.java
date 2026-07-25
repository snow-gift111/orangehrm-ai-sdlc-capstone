package com.orangehrm.leavealert.api.dto;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.UUID;

public record MyAlertEventResponse(
        UUID id,
        String leaveTypeId,
        BigDecimal evaluatedBalance,
        BigDecimal thresholdValue,
        Instant evaluatedAt,
        String channel,
        String status
) {
}
