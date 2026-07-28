package com.orangehrm.leave.api.dto;

import com.orangehrm.leave.domain.LeaveUnit;

import java.time.Instant;

public record LeaveTypeResponse(
        Long id,
        String name,
        LeaveUnit unit,
        boolean isActive,
        Instant createdAt,
        Instant updatedAt
) {
}
