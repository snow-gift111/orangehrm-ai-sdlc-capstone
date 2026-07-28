package com.orangehrm.leave.api.dto;

import jakarta.validation.constraints.NotNull;

import java.math.BigDecimal;

public record LeaveBalanceUpsertRequest(
        @NotNull BigDecimal balanceValue
) {
}
