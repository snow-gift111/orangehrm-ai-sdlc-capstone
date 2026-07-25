package com.orangehrm.leavealert.api.dto;

import jakarta.validation.Valid;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.PositiveOrZero;
import jakarta.validation.constraints.Size;
import java.math.BigDecimal;
import java.util.List;

public record CreateAlertRuleRequest(
        @NotBlank String name,
        @NotNull @PositiveOrZero BigDecimal thresholdValue,
        @NotNull @Size(min = 1) List<@NotBlank String> leaveTypeIds,
        @NotNull @Valid RecipientsConfigDto recipients,
        @NotBlank String channel
) {
}
