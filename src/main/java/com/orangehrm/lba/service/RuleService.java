package com.orangehrm.lba.service;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.orangehrm.lba.api.ResourceNotFoundException;
import com.orangehrm.lba.api.dto.RuleCreateRequest;
import com.orangehrm.lba.api.dto.RuleResponse;
import com.orangehrm.lba.api.dto.RuleUpdateRequest;
import com.orangehrm.lba.domain.entity.AlertRuleEntity;
import com.orangehrm.lba.domain.enums.AuditActionType;
import com.orangehrm.lba.domain.enums.LeaveTypeScope;
import com.orangehrm.lba.domain.enums.RuleStatus;
import com.orangehrm.lba.domain.model.AlertRecipientConfig;
import com.orangehrm.lba.repository.AlertRuleRepository;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Service
public class RuleService {

    private final AlertRuleRepository ruleRepository;
    private final ObjectMapper objectMapper;
    private final AuditService auditService;

    public RuleService(AlertRuleRepository ruleRepository, ObjectMapper objectMapper, AuditService auditService) {
        this.ruleRepository = ruleRepository;
        this.objectMapper = objectMapper;
        this.auditService = auditService;
    }

    @Transactional
    public RuleResponse create(RuleCreateRequest req) {
        validateLeaveType(req.leaveTypeScope(), req.leaveTypeId());

        AlertRuleEntity entity = new AlertRuleEntity();
        entity.setLeaveTypeScope(req.leaveTypeScope());
        entity.setLeaveTypeId(req.leaveTypeId());
        entity.setOperator(req.operator());
        entity.setThresholdValue(req.thresholdValue());
        entity.setSuppressionWindowDays(req.suppressionWindowDays());
        entity.setRecipientsJson(toJson(req.recipients()));
        entity.setStatus(req.status() == null ? RuleStatus.INACTIVE : req.status());
        entity.setCreatedBy(currentActor());
        entity.setUpdatedBy(currentActor());

        AlertRuleEntity saved = ruleRepository.save(entity);
        auditService.record(saved, AuditActionType.CREATE, null);
        return toResponse(saved);
    }

    @Transactional
    public RuleResponse update(long ruleId, RuleUpdateRequest req) {
        validateLeaveType(req.leaveTypeScope(), req.leaveTypeId());

        AlertRuleEntity entity = ruleRepository.findById(ruleId)
                .orElseThrow(() -> new ResourceNotFoundException("Rule not found: " + ruleId));

        entity.setLeaveTypeScope(req.leaveTypeScope());
        entity.setLeaveTypeId(req.leaveTypeId());
        entity.setOperator(req.operator());
        entity.setThresholdValue(req.thresholdValue());
        entity.setSuppressionWindowDays(req.suppressionWindowDays());
        entity.setRecipientsJson(toJson(req.recipients()));
        entity.setStatus(req.status());
        entity.setUpdatedBy(currentActor());

        AlertRuleEntity saved = ruleRepository.save(entity);
        auditService.record(saved, AuditActionType.UPDATE, null);
        return toResponse(saved);
    }

    @Transactional(readOnly = true)
    public RuleResponse get(long ruleId) {
        AlertRuleEntity entity = ruleRepository.findById(ruleId)
                .orElseThrow(() -> new ResourceNotFoundException("Rule not found: " + ruleId));
        return toResponse(entity);
    }

    @Transactional(readOnly = true)
    public List<RuleResponse> list() {
        return ruleRepository.findAll().stream().map(this::toResponse).toList();
    }

    @Transactional
    public RuleResponse setStatus(long ruleId, RuleStatus status) {
        AlertRuleEntity entity = ruleRepository.findById(ruleId)
                .orElseThrow(() -> new ResourceNotFoundException("Rule not found: " + ruleId));

        entity.setStatus(status);
        entity.setUpdatedBy(currentActor());
        AlertRuleEntity saved = ruleRepository.save(entity);

        auditService.record(saved,
                status == RuleStatus.ACTIVE ? AuditActionType.ACTIVATE : AuditActionType.DEACTIVATE,
                null);

        return toResponse(saved);
    }

    @Transactional
    public void delete(long ruleId) {
        AlertRuleEntity entity = ruleRepository.findById(ruleId)
                .orElseThrow(() -> new ResourceNotFoundException("Rule not found: " + ruleId));
        ruleRepository.delete(entity);
        auditService.record(entity, AuditActionType.DELETE, null);
    }

    private void validateLeaveType(LeaveTypeScope scope, Long leaveTypeId) {
        if (scope == LeaveTypeScope.SPECIFIC && leaveTypeId == null) {
            throw new IllegalArgumentException("leaveTypeId is required when leaveTypeScope is SPECIFIC");
        }
        if (scope == LeaveTypeScope.ALL && leaveTypeId != null) {
            throw new IllegalArgumentException("leaveTypeId must be null when leaveTypeScope is ALL");
        }
    }

    private RuleResponse toResponse(AlertRuleEntity e) {
        return new RuleResponse(
                e.getId(),
                e.getLeaveTypeScope(),
                e.getLeaveTypeId(),
                e.getOperator(),
                e.getThresholdValue(),
                e.getSuppressionWindowDays(),
                fromJson(e.getRecipientsJson()),
                e.getStatus(),
                e.getCreatedAt(),
                e.getCreatedBy(),
                e.getUpdatedAt(),
                e.getUpdatedBy()
        );
    }

    private String toJson(AlertRecipientConfig cfg) {
        try {
            return objectMapper.writeValueAsString(cfg);
        } catch (JsonProcessingException e) {
            throw new IllegalArgumentException("Invalid recipients configuration");
        }
    }

    private AlertRecipientConfig fromJson(String json) {
        try {
            return objectMapper.readValue(json, AlertRecipientConfig.class);
        } catch (Exception e) {
            // Corrupted data should not break read operations.
            return new AlertRecipientConfig(false, false, List.of(), List.of(), List.of(), List.of());
        }
    }

    private String currentActor() {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        if (auth == null) return null;
        return auth.getName();
    }
}
