package com.orangehrm.lba.service;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.orangehrm.lba.domain.entity.AlertEventEntity;
import com.orangehrm.lba.domain.entity.AlertEventRecipientEntity;
import com.orangehrm.lba.domain.entity.AlertRuleEntity;
import com.orangehrm.lba.domain.enums.AlertEventStatus;
import com.orangehrm.lba.domain.enums.LeaveTypeScope;
import com.orangehrm.lba.domain.enums.RecipientDeliveryStatus;
import com.orangehrm.lba.domain.model.AlertRecipientConfig;
import com.orangehrm.lba.integration.LeaveBalanceProvider;
import com.orangehrm.lba.repository.AlertEventRecipientRepository;
import com.orangehrm.lba.repository.AlertEventRepository;
import com.orangehrm.lba.repository.AlertRuleRepository;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Propagation;
import org.springframework.transaction.annotation.Transactional;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.ArrayList;
import java.util.List;
import java.util.UUID;

@Service
public class EvaluationService {

    private static final Logger log = LoggerFactory.getLogger(EvaluationService.class);

    private final AlertRuleRepository ruleRepository;
    private final AlertEventRepository eventRepository;
    private final AlertEventRecipientRepository recipientRepository;
    private final LeaveBalanceProvider leaveBalanceProvider;
    private final RecipientResolutionService recipientResolutionService;
    private final SuppressionService suppressionService;
    private final NotificationService notificationService;
    private final ObjectMapper objectMapper;

    public EvaluationService(AlertRuleRepository ruleRepository,
                             AlertEventRepository eventRepository,
                             AlertEventRecipientRepository recipientRepository,
                             LeaveBalanceProvider leaveBalanceProvider,
                             RecipientResolutionService recipientResolutionService,
                             SuppressionService suppressionService,
                             NotificationService notificationService,
                             ObjectMapper objectMapper) {
        this.ruleRepository = ruleRepository;
        this.eventRepository = eventRepository;
        this.recipientRepository = recipientRepository;
        this.leaveBalanceProvider = leaveBalanceProvider;
        this.recipientResolutionService = recipientResolutionService;
        this.suppressionService = suppressionService;
        this.notificationService = notificationService;
        this.objectMapper = objectMapper;
    }

    @Transactional
    public EvaluationSummary run() {
        String jobRunId = UUID.randomUUID().toString();
        Instant startedAt = Instant.now();

        List<AlertRuleEntity> activeRules = ruleRepository.findAllByStatus(com.orangehrm.lba.domain.enums.RuleStatus.ACTIVE);
        if (activeRules.isEmpty()) {
            return new EvaluationSummary(jobRunId, startedAt, Instant.now(), 0, 0, 0, 0);
        }

        List<LeaveBalanceProvider.EmployeeRef> employees = leaveBalanceProvider.listEmployees();
        List<LeaveBalanceProvider.LeaveTypeRef> allLeaveTypes = leaveBalanceProvider.listLeaveTypes();

        long evaluated = 0;
        long matched = 0;
        long suppressed = 0;
        long sentOrAttempted = 0;

        for (AlertRuleEntity rule : activeRules) {
            AlertRecipientConfig recipientConfig = parseRecipients(rule.getRecipientsJson());

            List<LeaveBalanceProvider.LeaveTypeRef> targetLeaveTypes = rule.getLeaveTypeScope() == LeaveTypeScope.SPECIFIC
                    ? allLeaveTypes.stream().filter(t -> t.leaveTypeId() == rule.getLeaveTypeId()).toList()
                    : allLeaveTypes;

            for (LeaveBalanceProvider.EmployeeRef employee : employees) {
                for (LeaveBalanceProvider.LeaveTypeRef leaveType : targetLeaveTypes) {
                    evaluated++;

                    BigDecimal balance;
                    try {
                        balance = leaveBalanceProvider.getBalance(employee.employeeId(), leaveType.leaveTypeId());
                    } catch (Exception ex) {
                        log.warn("Balance retrieval failed for employeeId={} leaveTypeId={}: {}",
                                employee.employeeId(), leaveType.leaveTypeId(), ex.getMessage());
                        continue;
                    }

                    if (!rule.getOperator().matches(balance, rule.getThresholdValue())) {
                        continue;
                    }

                    matched++;

                    AlertEventEntity event = createPendingEvent(rule, employee.employeeId(), leaveType.leaveTypeId(), balance, jobRunId);

                    List<AlertEventRecipientEntity> recipients = createRecipients(event, recipientConfig, employee);

                    boolean isSuppressed = suppressionService.isSuppressed(rule.getId(), employee.employeeId(), leaveType.leaveTypeId(), rule.getSuppressionWindowDays());
                    if (isSuppressed) {
                        suppressed++;
                        markSuppressed(event, recipients);
                        continue;
                    }

                    sentOrAttempted++;
                    notificationService.sendEvent(event, employee, leaveType, recipients);
                }
            }
        }

        return new EvaluationSummary(jobRunId, startedAt, Instant.now(), evaluated, matched, suppressed, sentOrAttempted);
    }

    @Transactional(propagation = Propagation.REQUIRES_NEW)
    protected AlertEventEntity createPendingEvent(AlertRuleEntity rule, long employeeId, long leaveTypeId, BigDecimal balance, String jobRunId) {
        AlertEventEntity event = new AlertEventEntity();
        event.setRule(rule);
        event.setEmployeeId(employeeId);
        event.setLeaveTypeId(leaveTypeId);
        event.setBalanceSnapshot(balance);
        event.setEvaluatedAt(Instant.now());
        event.setStatus(AlertEventStatus.PENDING);
        event.setJobRunId(jobRunId);
        return eventRepository.save(event);
    }

    @Transactional(propagation = Propagation.REQUIRES_NEW)
    protected List<AlertEventRecipientEntity> createRecipients(AlertEventEntity event,
                                                              AlertRecipientConfig config,
                                                              LeaveBalanceProvider.EmployeeRef employee) {
        List<RecipientResolutionService.ResolvedRecipient> resolved = recipientResolutionService.resolve(config, employee);
        List<AlertEventRecipientEntity> recipients = new ArrayList<>();

        for (RecipientResolutionService.ResolvedRecipient rr : resolved) {
            AlertEventRecipientEntity r = new AlertEventRecipientEntity();
            r.setEvent(event);
            r.setRecipientType(rr.type());
            r.setRecipientUserId(rr.userId());
            r.setRecipientEmail(rr.email());
            if (rr.notDeliverableReason() != null) {
                r.setDeliveryStatus(RecipientDeliveryStatus.NOT_DELIVERABLE);
                r.setFailureReason(rr.notDeliverableReason());
            } else {
                r.setDeliveryStatus(RecipientDeliveryStatus.FAILED); // temporary; will be set by send
            }
            recipients.add(r);
        }

        return recipientRepository.saveAll(recipients);
    }

    @Transactional(propagation = Propagation.REQUIRES_NEW)
    protected void markSuppressed(AlertEventEntity event, List<AlertEventRecipientEntity> recipients) {
        event.setStatus(AlertEventStatus.SUPPRESSED);
        event.setFailureReason("Suppressed within configured window");
        eventRepository.save(event);

        for (AlertEventRecipientEntity r : recipients) {
            r.setDeliveryStatus(RecipientDeliveryStatus.SUPPRESSED);
            r.setFailureReason("Suppressed within configured window");
        }
        recipientRepository.saveAll(recipients);
    }

    private AlertRecipientConfig parseRecipients(String json) {
        try {
            return objectMapper.readValue(json, AlertRecipientConfig.class);
        } catch (Exception e) {
            return new AlertRecipientConfig(false, false, List.of(), List.of(), List.of(), List.of());
        }
    }

    public record EvaluationSummary(
            String jobRunId,
            Instant startedAt,
            Instant finishedAt,
            long evaluatedCount,
            long matchedCount,
            long suppressedCount,
            long sendAttemptedCount
    ) {
    }
}
