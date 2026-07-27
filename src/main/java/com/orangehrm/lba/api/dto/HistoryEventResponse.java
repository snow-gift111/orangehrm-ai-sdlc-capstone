package com.orangehrm.lba.api.dto;

import com.orangehrm.lba.domain.enums.AlertEventStatus;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.List;

public record HistoryEventResponse(
        Long id,
        Long employeeId,
        String employeeDisplayName,
        Long leaveTypeId,
        String leaveTypeName,
        BigDecimal balanceSnapshot,
        Long ruleId,
        AlertEventStatus status,
        Instant evaluatedAt,
        Instant sentAt,
        List<String> recipients
) {
}
