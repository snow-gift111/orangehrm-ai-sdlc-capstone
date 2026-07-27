package com.orangehrm.lba.service;

import com.orangehrm.lba.domain.enums.AlertEventStatus;
import com.orangehrm.lba.repository.AlertEventRepository;
import org.springframework.data.domain.PageRequest;
import org.springframework.stereotype.Service;

import java.time.Instant;
import java.time.temporal.ChronoUnit;

@Service
public class SuppressionService {

    private final AlertEventRepository alertEventRepository;

    public SuppressionService(AlertEventRepository alertEventRepository) {
        this.alertEventRepository = alertEventRepository;
    }

    public boolean isSuppressed(long ruleId, long employeeId, Long leaveTypeId, int suppressionWindowDays) {
        if (suppressionWindowDays <= 0) {
            return false;
        }

        Instant cutoff = Instant.now().minus(suppressionWindowDays, ChronoUnit.DAYS);

        return alertEventRepository
                .findLatestSentWithinWindow(ruleId, employeeId, leaveTypeId, AlertEventStatus.SENT, cutoff, PageRequest.of(0, 1))
                .isPresent();
    }
}
