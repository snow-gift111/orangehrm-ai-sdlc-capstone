package com.orangehrm.leavealert.api.dto;

import jakarta.validation.constraints.NotNull;

public record UpdateRuleStatusRequest(
        @NotNull Boolean enabled
) {
}
