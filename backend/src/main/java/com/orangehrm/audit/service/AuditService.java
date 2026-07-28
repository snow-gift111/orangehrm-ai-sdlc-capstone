package com.orangehrm.audit.service;

import com.orangehrm.audit.domain.AuditLogEntity;
import com.orangehrm.audit.repo.AuditLogRepository;
import org.springframework.stereotype.Service;

import java.time.Instant;

@Service
public class AuditService {

    private final AuditLogRepository auditLogRepository;

    public AuditService(AuditLogRepository auditLogRepository) {
        this.auditLogRepository = auditLogRepository;
    }

    public void audit(Long actorUserId,
                      String actionType,
                      String entityType,
                      String entityId,
                      String beforeSnapshot,
                      String afterSnapshot,
                      String correlationId) {
        AuditLogEntity entity = new AuditLogEntity();
        entity.setActorUserId(actorUserId);
        entity.setActionType(actionType);
        entity.setEntityType(entityType);
        entity.setEntityId(entityId);
        entity.setBeforeSnapshot(beforeSnapshot);
        entity.setAfterSnapshot(afterSnapshot);
        entity.setOccurredAt(Instant.now());
        entity.setCorrelationId(correlationId);
        auditLogRepository.save(entity);
    }
}
