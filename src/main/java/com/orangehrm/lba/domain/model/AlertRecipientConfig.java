package com.orangehrm.lba.domain.model;

import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotNull;

import java.util.List;

public record AlertRecipientConfig(
        @NotNull Boolean includeEmployee,
        @NotNull Boolean includeManager,
        List<Long> hrRoleIds,
        List<Long> hrGroupIds,
        List<Long> specificUserIds,
        List<@Email String> specificEmails
) {
    public AlertRecipientConfig {
        hrRoleIds = hrRoleIds == null ? List.of() : List.copyOf(hrRoleIds);
        hrGroupIds = hrGroupIds == null ? List.of() : List.copyOf(hrGroupIds);
        specificUserIds = specificUserIds == null ? List.of() : List.copyOf(specificUserIds);
        specificEmails = specificEmails == null ? List.of() : List.copyOf(specificEmails);
    }
}
