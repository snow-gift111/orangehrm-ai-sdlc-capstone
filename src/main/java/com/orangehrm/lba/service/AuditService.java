package com.orangehrm.lba.service;

import com.orangehrm.lba.domain.entity.AlertRuleEntity;
import com.orangehrm.lba.domain.entity.RuleAuditEntity;
import com.orangehrm.lba.domain.enums.AuditActionType;
import com.orangehrm.lba.repository.RuleAuditRepository;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Service;

@Service
public class AuditService {

    private final RuleAuditRepository auditRepository;

    public AuditService(RuleAuditRepository auditRepository) {
        this.auditRepository = auditRepository;
    }

    public void record(AlertRuleEntity rule, AuditActionType actionType, String changeSummary) {
        RuleAuditEntity audit = new RuleAuditEntity();
        audit.setRule(rule);
        audit.setActionType(actionType);
        audit.setActorUserId(currentActor());
        audit.setChangeSummary(changeSummary);
        auditRepository.save(audit);
    }

    private String currentActor() {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        if (auth == null) return null;
        return auth.getName();
    }
}
