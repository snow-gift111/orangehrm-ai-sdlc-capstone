package com.orangehrm.alert.api.dto;

import jakarta.validation.constraints.NotNull;

public record AlertRuleRecipientDto(
        @NotNull RecipientType type,
        String value
) {
}
