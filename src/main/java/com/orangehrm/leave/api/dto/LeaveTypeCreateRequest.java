package com.orangehrm.leave.api.dto;

import jakarta.validation.constraints.NotBlank;

public record LeaveTypeCreateRequest(
    @NotBlank String name,
    String code,
    Boolean active
) {}
