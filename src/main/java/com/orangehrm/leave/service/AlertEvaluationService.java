package com.orangehrm.leave.service;

import com.orangehrm.leave.domain.AlertStatus;
import com.orangehrm.leave.domain.LeaveAlertEvent;
import com.orangehrm.leave.domain.LeaveAlertRecipient;
import com.orangehrm.leave.domain.LeaveAlertRule;
import com.orangehrm.leave.domain.RecipientType;
import com.orangehrm.leave.domain.RuleScopeType;
import com.orangehrm.leave.repo.LeaveAlertEventRepository;
import com.orangehrm.leave.repo.LeaveAlertRecipientRepository;
import com.orangehrm.leave.repo.LeaveAlertRuleRepository;
import com.orangehrm.leave.repo.LeaveBalanceRepository;
import com.orangehrm.user.AppUser;
import java.time.Clock;
import java.time.Instant;
import java.util.Set;
import java.util.stream.Collectors;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Propagation;
import org.springframework.transaction.annotation.Transactional;

@Service
@RequiredArgsConstructor
@Slf4j
public class AlertEvaluationService {

  private static final String DEFAULT_RECOMMENDED_ACTION = "Review leave plan / contact HR";

  private final LeaveBalanceRepository balanceRepository;
  private final LeaveAlertRuleRepository ruleRepository;
  private final LeaveAlertEventRepository eventRepository;
  private final LeaveAlertRecipientRepository recipientRepository;
  private final ThresholdEvaluator thresholdEvaluator;
  private final RecipientResolver recipientResolver;
  private final Clock clock;

  /**
   * Best-effort evaluation. Runs in a new transaction so failures don't impact balance updates.
   */
  @Transactional(propagation = Propagation.REQUIRES_NEW)
  public void evaluateOnChange(Long employeeId, Long leaveTypeId) {
    try {
      var balanceOpt = balanceRepository.findByEmployeeIdAndLeaveType_Id(employeeId, leaveTypeId);
      if (balanceOpt.isEmpty()) {
        log.info("No leave balance found for employeeId={}, leaveTypeId={}", employeeId, leaveTypeId);
        return;
      }

      var balance = balanceOpt.get();
      var rules = ruleRepository.findActiveRulesForLeaveType(leaveTypeId, RuleScopeType.ALL, RuleScopeType.ONE);

      for (LeaveAlertRule rule : rules) {
        if (!thresholdEvaluator.isBreached(rule, balance.getBalance())) {
          log.info("Rule evaluation not breached: ruleId={}, employeeId={}, leaveTypeId={}, balance={}",
              rule.getId(), employeeId, leaveTypeId, balance.getBalance());
          continue;
        }

        String thresholdBreached = thresholdEvaluator.breachDisplay(rule);
        Instant now = Instant.now(clock);

        LeaveAlertEvent event = new LeaveAlertEvent();
        event.setRule(rule);
        event.setEmployeeId(employeeId);
        event.setLeaveType(balance.getLeaveType());
        event.setCurrentBalance(balance.getBalance());
        event.setThresholdBreached(thresholdBreached);
        event.setRecommendedAction(DEFAULT_RECOMMENDED_ACTION);
        event.setGeneratedAt(now);
        event = eventRepository.save(event);

        Set<RecipientType> recipientTypes = rule.getRecipients().stream()
            .map(r -> r.getRecipientType())
            .collect(Collectors.toSet());

        Set<AppUser> recipients = recipientResolver.resolveRecipients(employeeId, recipientTypes);
        for (AppUser recipientUser : recipients) {
          LeaveAlertRecipient ar = new LeaveAlertRecipient();
          ar.setAlertEvent(event);
          ar.setRecipientUser(recipientUser);
          ar.setStatus(AlertStatus.NEW);
          ar.setNewAt(now);
          recipientRepository.save(ar);
        }

        log.info("Alert created: eventId={}, ruleId={}, employeeId={}, leaveTypeId={}, recipients={}",
            event.getId(), rule.getId(), employeeId, leaveTypeId,
            recipients.stream().map(AppUser::getUsername).toList());
      }
    } catch (Exception e) {
      log.error("Alert evaluation failed for employeeId={}, leaveTypeId={}", employeeId, leaveTypeId, e);
    }
  }
}
