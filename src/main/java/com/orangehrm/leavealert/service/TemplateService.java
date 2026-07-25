package com.orangehrm.leavealert.service;

import com.orangehrm.leavealert.domain.ChannelType;
import java.math.BigDecimal;
import java.time.Instant;
import java.util.Map;
import org.springframework.stereotype.Service;

@Service
public class TemplateService {

    public record RenderedMessage(String subject, String body) {
    }

    public RenderedMessage renderDefault(ChannelType channel,
                                         String employeeId,
                                         String leaveTypeId,
                                         BigDecimal evaluatedBalance,
                                         BigDecimal thresholdValue,
                                         Instant asOf) {
        if (channel != ChannelType.IN_APP) {
            throw new IllegalArgumentException("Unsupported channel: " + channel);
        }

        String subject = "Low leave balance alert";
        String bodyTemplate = "Employee %s has low balance for leave type %s. Remaining=%s, Threshold=%s, AsOf=%s";
        String body = bodyTemplate.formatted(employeeId, leaveTypeId, evaluatedBalance, thresholdValue, asOf);

        // Placeholder validation is trivial for system template; kept for future extensibility.
        validateNoUnresolvedPlaceholders(body, Map.of());

        return new RenderedMessage(subject, body);
    }

    private void validateNoUnresolvedPlaceholders(String rendered, Map<String, String> values) {
        if (rendered.contains("{{") || rendered.contains("}}")) {
            throw new IllegalArgumentException("Template rendering produced unresolved placeholders");
        }
    }
}
