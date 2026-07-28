package com.orangehrm.leave.api.dto;

import java.math.BigDecimal;
import java.time.Instant;

public record LeaveBalanceResponse(
        Long id,
        Long employeeId,
        Long leaveTypeId,
        BigDecimal balanceValue,
        Instant lastUpdatedAt,
        Long updatedByUserId,
        Instant createdAt
) {
}
