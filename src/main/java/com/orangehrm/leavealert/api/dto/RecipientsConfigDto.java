package com.orangehrm.leavealert.api.dto;

import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.Size;
import java.util.List;

public record RecipientsConfigDto(
        @NotNull Boolean includeEmployee,
        @NotNull @Size(min = 1) List<@NotNull String> hrRoleIds
) {
}
