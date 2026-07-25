package com.orangehrm.leavealert.api.dto;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.List;
import java.util.UUID;

public record AlertRuleResponse(
        UUID id,
        String name,
        BigDecimal thresholdValue,
        String thresholdUnit,
        boolean enabled,
        List<String> leaveTypeIds,
        RecipientsConfigDto recipients,
        String channel,
        Instant createdAt,
        Instant updatedAt
) {
}
