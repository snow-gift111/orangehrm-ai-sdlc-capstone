package com.orangehrm.alert.api.dto;

import com.orangehrm.alert.domain.AlertDeliveryStatus;

import java.math.BigDecimal;
import java.time.Instant;

public record AlertResponse(
        Long id,
        Long ruleId,
        Long employeeId,
        Long leaveTypeId,
        BigDecimal evaluatedBalanceValue,
        Instant triggeredAt,
        AlertDeliveryStatus deliveryStatus
) {
}
