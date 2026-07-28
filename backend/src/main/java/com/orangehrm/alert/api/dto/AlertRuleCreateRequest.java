package com.orangehrm.alert.api.dto;

import com.orangehrm.alert.domain.AlertComparisonOperator;
import jakarta.validation.Valid;
import jakarta.validation.constraints.DecimalMin;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotEmpty;
import jakarta.validation.constraints.NotNull;

import java.math.BigDecimal;
import java.util.List;

public record AlertRuleCreateRequest(
        @NotBlank String name,
        @NotNull Long leaveTypeId,
        @NotNull @DecimalMin(value = "0.0", inclusive = true) BigDecimal thresholdValue,
        @NotNull AlertComparisonOperator comparisonOperator,
        @NotNull @Valid AlertRuleFrequencyDto frequency,
        @NotEmpty @Valid List<AlertRuleRecipientDto> recipients,
        Boolean isActive
) {
}
