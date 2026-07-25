package com.orangehrm.leavealert.service;

import com.orangehrm.leavealert.config.AppProperties;
import com.orangehrm.leavealert.domain.SendStatus;
import com.orangehrm.leavealert.domain.entity.AlertEventEntity;
import com.orangehrm.leavealert.domain.entity.AlertRuleEntity;
import com.orangehrm.leavealert.integration.EmployeeDirectoryProvider;
import com.orangehrm.leavealert.integration.LeaveBalanceProvider;
import com.orangehrm.leavealert.integration.LeaveBalanceProvider.LeaveBalance;
import com.orangehrm.leavealert.repository.AlertEventRepository;
import com.orangehrm.leavealert.repository.AlertRuleLeaveTypeRepository;
import com.orangehrm.leavealert.repository.AlertRuleRepository;
import java.time.Instant;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
public class AlertEvaluationService {

    private static final Logger log = LoggerFactory.getLogger(AlertEvaluationService.class);

    private final AppProperties appProperties;
    private final AlertRuleRepository alertRuleRepository;
    private final AlertRuleLeaveTypeRepository leaveTypeRepository;
    private final AlertEventRepository alertEventRepository;
    private final EmployeeDirectoryProvider employeeDirectoryProvider;
    private final LeaveBalanceProvider leaveBalanceProvider;
    private final AlertNotificationService alertNotificationService;

    public AlertEvaluationService(AppProperties appProperties,
                                  AlertRuleRepository alertRuleRepository,
                                  AlertRuleLeaveTypeRepository leaveTypeRepository,
                                  AlertEventRepository alertEventRepository,
                                  EmployeeDirectoryProvider employeeDirectoryProvider,
                                  LeaveBalanceProvider leaveBalanceProvider,
                                  AlertNotificationService alertNotificationService) {
        this.appProperties = appProperties;
        this.alertRuleRepository = alertRuleRepository;
        this.leaveTypeRepository = leaveTypeRepository;
        this.alertEventRepository = alertEventRepository;
        this.employeeDirectoryProvider = employeeDirectoryProvider;
        this.leaveBalanceProvider = leaveBalanceProvider;
        this.alertNotificationService = alertNotificationService;
    }

    @Transactional
    public EvaluationResult evaluate(Instant asOf) {
        List<AlertRuleEntity> rules = alertRuleRepository.findByEnabledTrue();
        if (rules.isEmpty()) {
            return new EvaluationResult(0, 0, 0);
        }

        List<String> employeeIds = employeeDirectoryProvider.getActiveEmployeeIds();
        int batchSize = Math.max(1, appProperties.getLeaveBalanceAlert().getEmployeeBatchSize());

        int evaluated = 0;
        int breaches = 0;
        int eventsCreated = 0;

        for (AlertRuleEntity rule : rules) {
            List<String> leaveTypeIds = leaveTypeRepository.findLeaveTypeIdsByRuleId(rule.getId());
            if (leaveTypeIds.isEmpty()) {
                log.warn("Rule {} has no leave types mapped; skipping", rule.getId());
                continue;
            }

            for (int i = 0; i < employeeIds.size(); i += batchSize) {
                List<String> batch = employeeIds.subList(i, Math.min(i + batchSize, employeeIds.size()));
                List<LeaveBalance> balances = leaveBalanceProvider.getBalances(batch, leaveTypeIds, asOf);

                for (LeaveBalance balance : balances) {
                    evaluated++;
                    if (balance.balance() == null) {
                        continue;
                    }
                    if (balance.balance().compareTo(rule.getThresholdValue()) <= 0) {
                        breaches++;
                        AlertEventEntity event = new AlertEventEntity();
                        event.setId(UUID.randomUUID());
                        event.setRuleId(rule.getId());
                        event.setEmployeeId(balance.employeeId());
                        event.setLeaveTypeId(balance.leaveTypeId());
                        event.setEvaluatedBalance(balance.balance());
                        event.setThresholdValue(rule.getThresholdValue());
                        event.setEvaluatedAt(asOf);
                        event.setChannel(rule.getChannel());
                        event.setRecipients("[]");
                        event.setStatus(SendStatus.PENDING);

                        alertEventRepository.save(event);
                        eventsCreated++;

                        alertNotificationService.send(event, rule);
                    }
                }
            }
        }

        return new EvaluationResult(evaluated, breaches, eventsCreated);
    }

    public record EvaluationResult(int evaluatedPairs, int breaches, int eventsCreated) {
    }
}
