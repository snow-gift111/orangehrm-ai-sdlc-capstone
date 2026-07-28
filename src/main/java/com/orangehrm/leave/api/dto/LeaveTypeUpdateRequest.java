package com.orangehrm.leave.api.dto;

public record LeaveTypeUpdateRequest(
    String name,
    String code,
    Boolean active
) {}
