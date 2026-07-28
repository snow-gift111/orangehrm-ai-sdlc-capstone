package com.orangehrm.leave.api.dto;

import com.orangehrm.leave.domain.LeaveUnit;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;

public record LeaveTypeCreateRequest(
        @NotBlank String name,
        @NotNull LeaveUnit unit,
        Boolean isActive
) {
}
