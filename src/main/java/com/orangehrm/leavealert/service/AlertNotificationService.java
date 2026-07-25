package com.orangehrm.leavealert.service;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.orangehrm.leavealert.domain.SendStatus;
import com.orangehrm.leavealert.domain.entity.AlertEventEntity;
import com.orangehrm.leavealert.domain.entity.AlertRuleEntity;
import com.orangehrm.leavealert.integration.NotificationChannel;
import com.orangehrm.leavealert.repository.AlertEventRepository;
import java.time.Instant;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
public class AlertNotificationService {

    private static final Logger log = LoggerFactory.getLogger(AlertNotificationService.class);

    private final NotificationChannel notificationChannel;
    private final TemplateService templateService;
    private final AlertEventRepository alertEventRepository;
    private final ObjectMapper objectMapper;

    public AlertNotificationService(NotificationChannel notificationChannel,
                                    TemplateService templateService,
                                    AlertEventRepository alertEventRepository,
                                    ObjectMapper objectMapper) {
        this.notificationChannel = notificationChannel;
        this.templateService = templateService;
        this.alertEventRepository = alertEventRepository;
        this.objectMapper = objectMapper;
    }

    @Transactional
    public void send(AlertEventEntity event, AlertRuleEntity rule) {
        // Determine recipients (Sprint 1): employee + HR roles. Manager excluded.
        List<String> recipientIds = new ArrayList<>();
        if (rule.isIncludeEmployee()) {
            recipientIds.add(event.getEmployeeId());
        }

        // HR roles recipients are stored as role identifiers; for in-app logging channel
        // we emit one notification per role as an audit placeholder.
        List<String> hrRoleIds = deserializeRoleIds(rule.getHrRoleIds());
        for (String roleId : hrRoleIds) {
            recipientIds.add("ROLE:" + roleId);
        }

        event.setRecipients(serializeRecipients(recipientIds));
        alertEventRepository.save(event);

        TemplateService.RenderedMessage msg = templateService.renderDefault(
                rule.getChannel(),
                event.getEmployeeId(),
                event.getLeaveTypeId(),
                event.getEvaluatedBalance(),
                event.getThresholdValue(),
                event.getEvaluatedAt()
        );

        Map<String, String> metadata = new HashMap<>();
        metadata.put("ruleId", rule.getId().toString());
        metadata.put("eventId", event.getId().toString());
        metadata.put("leaveTypeId", event.getLeaveTypeId());

        try {
            for (String recipientId : recipientIds) {
                notificationChannel.send(recipientId, msg.subject(), msg.body(), metadata);
            }
            event.setStatus(SendStatus.SENT);
            event.setSentAt(Instant.now());
            event.setFailureReason(null);
        } catch (Exception ex) {
            log.warn("Notification send failed eventId={} ruleId={} reason={}", event.getId(), rule.getId(), ex.toString());
            event.setStatus(SendStatus.FAILED);
            event.setFailureReason(ex.getMessage());
        }

        alertEventRepository.save(event);
    }

    private List<String> deserializeRoleIds(String json) {
        try {
            return objectMapper.readValue(json, objectMapper.getTypeFactory().constructCollectionType(List.class, String.class));
        } catch (Exception e) {
            return List.of();
        }
    }

    private String serializeRecipients(List<String> recipients) {
        try {
            return objectMapper.writeValueAsString(recipients);
        } catch (JsonProcessingException e) {
            return "[]";
        }
    }
}
