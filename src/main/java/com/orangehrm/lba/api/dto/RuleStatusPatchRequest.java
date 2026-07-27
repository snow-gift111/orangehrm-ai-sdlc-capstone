package com.orangehrm.lba.api.dto;

import com.orangehrm.lba.domain.enums.RuleStatus;
import jakarta.validation.constraints.NotNull;

public record RuleStatusPatchRequest(@NotNull RuleStatus status) {
}
