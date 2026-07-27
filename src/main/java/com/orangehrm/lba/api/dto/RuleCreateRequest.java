package com.orangehrm.lba.api.dto;

import com.orangehrm.lba.domain.enums.LeaveTypeScope;
import com.orangehrm.lba.domain.enums.RuleStatus;
import com.orangehrm.lba.domain.enums.ThresholdOperator;
import com.orangehrm.lba.domain.model.AlertRecipientConfig;
import jakarta.validation.Valid;
import jakarta.validation.constraints.Min;
import jakarta.validation.constraints.NotNull;

import java.math.BigDecimal;

public record RuleCreateRequest(
        @NotNull LeaveTypeScope leaveTypeScope,
        Long leaveTypeId,
        @NotNull ThresholdOperator operator,
        @NotNull @Min(0) BigDecimal thresholdValue,
        @NotNull @Min(0) Integer suppressionWindowDays,
        @NotNull @Valid AlertRecipientConfig recipients,
        RuleStatus status
) {
}
