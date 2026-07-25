package com.orangehrm.leavealert.service;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.orangehrm.leavealert.api.dto.AlertRuleResponse;
import com.orangehrm.leavealert.api.dto.CreateAlertRuleRequest;
import com.orangehrm.leavealert.api.dto.RecipientsConfigDto;
import com.orangehrm.leavealert.domain.ChannelType;
import com.orangehrm.leavealert.domain.ThresholdUnit;
import com.orangehrm.leavealert.domain.entity.AlertRuleEntity;
import com.orangehrm.leavealert.domain.entity.AlertRuleLeaveTypeEntity;
import com.orangehrm.leavealert.repository.AlertRuleLeaveTypeRepository;
import com.orangehrm.leavealert.repository.AlertRuleRepository;
import com.orangehrm.leavealert.security.AuthContext;
import com.orangehrm.leavealert.security.AuthorizationService;
import java.time.Instant;
import java.util.List;
import java.util.UUID;
import org.springframework.dao.DataIntegrityViolationException;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
public class AlertRuleService {

    private final AuthorizationService authorizationService;
    private final AlertRuleRepository alertRuleRepository;
    private final AlertRuleLeaveTypeRepository leaveTypeRepository;
    private final ObjectMapper objectMapper;

    public AlertRuleService(AuthorizationService authorizationService,
                            AlertRuleRepository alertRuleRepository,
                            AlertRuleLeaveTypeRepository leaveTypeRepository,
                            ObjectMapper objectMapper) {
        this.authorizationService = authorizationService;
        this.alertRuleRepository = alertRuleRepository;
        this.leaveTypeRepository = leaveTypeRepository;
        this.objectMapper = objectMapper;
    }

    @Transactional
    public AlertRuleResponse create(CreateAlertRuleRequest request) {
        authorizationService.requireHrAdmin();

        ChannelType channel = parseChannel(request.channel());
        if (channel != ChannelType.IN_APP) {
            throw new IllegalArgumentException("Unsupported channel: " + request.channel());
        }

        String createdBy = AuthContext.requirePrincipalName();
        Instant now = Instant.now();

        AlertRuleEntity entity = new AlertRuleEntity();
        entity.setId(UUID.randomUUID());
        entity.setName(request.name().trim());
        entity.setThresholdValue(request.thresholdValue());
        entity.setThresholdUnit(ThresholdUnit.DAYS);
        entity.setEnabled(true);
        entity.setChannel(channel);
        entity.setIncludeEmployee(Boolean.TRUE.equals(request.recipients().includeEmployee()));
        entity.setHrRoleIds(serializeHrRoleIds(request.recipients()));
        entity.setCreatedBy(createdBy);
        entity.setCreatedAt(now);
        entity.setUpdatedBy(createdBy);
        entity.setUpdatedAt(now);

        try {
            alertRuleRepository.save(entity);
        } catch (DataIntegrityViolationException e) {
            throw new IllegalArgumentException("Rule name already exists");
        }

        for (String leaveTypeId : request.leaveTypeIds()) {
            leaveTypeRepository.save(new AlertRuleLeaveTypeEntity(entity.getId(), leaveTypeId.trim()));
        }

        return toResponse(entity, request.leaveTypeIds(), request.recipients());
    }

    public Page<AlertRuleResponse> list(Pageable pageable) {
        authorizationService.requireHrAdmin();

        return alertRuleRepository.findAll(pageable)
                .map(entity -> {
                    List<String> leaveTypeIds = leaveTypeRepository.findLeaveTypeIdsByRuleId(entity.getId());
                    RecipientsConfigDto recipients = new RecipientsConfigDto(entity.isIncludeEmployee(), deserializeHrRoleIds(entity.getHrRoleIds()));
                    return toResponse(entity, leaveTypeIds, recipients);
                });
    }

    @Transactional
    public AlertRuleResponse setEnabled(UUID ruleId, boolean enabled) {
        authorizationService.requireHrAdmin();

        AlertRuleEntity entity = alertRuleRepository.findById(ruleId)
                .orElseThrow(() -> new IllegalArgumentException("Rule not found"));

        entity.setEnabled(enabled);
        entity.setUpdatedBy(AuthContext.requirePrincipalName());
        entity.setUpdatedAt(Instant.now());

        alertRuleRepository.save(entity);

        List<String> leaveTypeIds = leaveTypeRepository.findLeaveTypeIdsByRuleId(entity.getId());
        RecipientsConfigDto recipients = new RecipientsConfigDto(entity.isIncludeEmployee(), deserializeHrRoleIds(entity.getHrRoleIds()));
        return toResponse(entity, leaveTypeIds, recipients);
    }

    private AlertRuleResponse toResponse(AlertRuleEntity entity, List<String> leaveTypeIds, RecipientsConfigDto recipients) {
        return new AlertRuleResponse(
                entity.getId(),
                entity.getName(),
                entity.getThresholdValue(),
                entity.getThresholdUnit().name(),
                entity.isEnabled(),
                leaveTypeIds,
                recipients,
                entity.getChannel().name(),
                entity.getCreatedAt(),
                entity.getUpdatedAt()
        );
    }

    private ChannelType parseChannel(String raw) {
        try {
            return ChannelType.valueOf(raw.trim());
        } catch (Exception e) {
            throw new IllegalArgumentException("Invalid channel: " + raw);
        }
    }

    private String serializeHrRoleIds(RecipientsConfigDto recipients) {
        try {
            return objectMapper.writeValueAsString(recipients.hrRoleIds());
        } catch (JsonProcessingException e) {
            throw new IllegalArgumentException("Invalid hrRoleIds");
        }
    }

    private List<String> deserializeHrRoleIds(String json) {
        try {
            return objectMapper.readValue(json, objectMapper.getTypeFactory().constructCollectionType(List.class, String.class));
        } catch (Exception e) {
            // fall back to empty list; stored data corrupted
            return List.of();
        }
    }
}
