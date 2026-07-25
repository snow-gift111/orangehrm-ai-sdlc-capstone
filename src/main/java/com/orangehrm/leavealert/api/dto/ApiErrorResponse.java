package com.orangehrm.leavealert.api.dto;

import java.util.List;

public record ApiErrorResponse(
        String errorCode,
        String message,
        List<FieldError> fieldErrors
) {
    public record FieldError(String field, String message) {}
}
