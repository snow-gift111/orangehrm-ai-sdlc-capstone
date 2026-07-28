package com.orangehrm.alert.api.dto;

import com.orangehrm.alert.domain.AlertFrequencyUnit;
import jakarta.validation.constraints.Min;
import jakarta.validation.constraints.NotNull;

public record AlertRuleFrequencyDto(
        @Min(1) int value,
        @NotNull AlertFrequencyUnit unit
) {
}
