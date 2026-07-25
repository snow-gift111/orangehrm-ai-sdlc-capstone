package com.orangehrm.leavealert.service;

import com.orangehrm.leavealert.api.dto.AlertEventResponse;
import com.orangehrm.leavealert.api.dto.MyAlertEventResponse;
import com.orangehrm.leavealert.domain.entity.AlertEventEntity;
import com.orangehrm.leavealert.repository.AlertEventRepository;
import com.orangehrm.leavealert.security.AuthContext;
import com.orangehrm.leavealert.security.AuthorizationService;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.stereotype.Service;

@Service
public class AlertHistoryService {

    private final AuthorizationService authorizationService;
    private final AlertEventRepository alertEventRepository;

    public AlertHistoryService(AuthorizationService authorizationService, AlertEventRepository alertEventRepository) {
        this.authorizationService = authorizationService;
        this.alertEventRepository = alertEventRepository;
    }

    public Page<AlertEventResponse> listAll(Pageable pageable) {
        authorizationService.requireAlertHistoryViewer();
        return alertEventRepository.findAll(pageable).map(this::toAdminResponse);
    }

    public Page<MyAlertEventResponse> listMine(Pageable pageable) {
        String employeeId = AuthContext.requirePrincipalName();
        return alertEventRepository.findAllByEmployeeIdOrderByEvaluatedAtDesc(employeeId, pageable)
                .map(this::toMyResponse);
    }

    private AlertEventResponse toAdminResponse(AlertEventEntity e) {
        return new AlertEventResponse(
                e.getId(),
                e.getRuleId(),
                e.getEmployeeId(),
                e.getLeaveTypeId(),
                e.getEvaluatedBalance(),
                e.getThresholdValue(),
                e.getEvaluatedAt(),
                e.getChannel().name(),
                e.getStatus().name(),
                e.getFailureReason()
        );
    }

    private MyAlertEventResponse toMyResponse(AlertEventEntity e) {
        return new MyAlertEventResponse(
                e.getId(),
                e.getLeaveTypeId(),
                e.getEvaluatedBalance(),
                e.getThresholdValue(),
                e.getEvaluatedAt(),
                e.getChannel().name(),
                e.getStatus().name()
        );
    }
}
