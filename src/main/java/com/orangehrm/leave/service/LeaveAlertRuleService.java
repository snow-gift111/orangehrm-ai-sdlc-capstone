package com.orangehrm.leave.service;

import com.orangehrm.common.NotFoundException;
import com.orangehrm.leave.domain.LeaveAlertRule;
import com.orangehrm.leave.domain.LeaveAlertRuleRecipient;
import com.orangehrm.leave.domain.RecipientType;
import com.orangehrm.leave.domain.RuleFrequency;
import com.orangehrm.leave.domain.RuleScopeType;
import com.orangehrm.leave.domain.ThresholdOperator;
import com.orangehrm.leave.repo.LeaveAlertRuleRecipientRepository;
import com.orangehrm.leave.repo.LeaveAlertRuleRepository;
import com.orangehrm.leave.repo.LeaveTypeRepository;
import com.orangehrm.user.AppUserRepository;
import java.math.BigDecimal;
import java.time.Clock;
import java.time.Instant;
import java.util.HashSet;
import java.util.List;
import java.util.Set;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@RequiredArgsConstructor
public class LeaveAlertRuleService {

  private final LeaveAlertRuleRepository repository;
  private final LeaveAlertRuleRecipientRepository recipientRepository;
  private final LeaveTypeRepository leaveTypeRepository;
  private final AppUserRepository userRepository;
  private final Clock clock;

  @Transactional
  public LeaveAlertRule create(RuleScopeType scopeType,
                               Long leaveTypeId,
                               ThresholdOperator operator,
                               BigDecimal thresholdValue,
                               Set<RecipientType> recipients,
                               RuleFrequency frequency,
                               boolean active,
                               String createdByUsername) {

    var createdBy = userRepository.findByUsername(createdByUsername)
        .orElseThrow(() -> new NotFoundException("User not found"));

    if (scopeType == RuleScopeType.ONE && leaveTypeId == null) {
      throw new IllegalArgumentException("leaveTypeId is required when scope type is ONE");
    }

    LeaveAlertRule rule = new LeaveAlertRule();
    rule.setScopeType(scopeType);
    if (scopeType == RuleScopeType.ONE) {
      rule.setLeaveType(leaveTypeRepository.findById(leaveTypeId)
          .orElseThrow(() -> new NotFoundException("Leave type not found")));
    } else {
      rule.setLeaveType(null);
    }

    rule.setThresholdOperator(operator);
    rule.setThresholdValue(thresholdValue);
    rule.setFrequency(frequency);
    rule.setActive(active);
    rule.setCreatedByUserId(createdBy.getId());

    Instant now = Instant.now(clock);
    rule.setCreatedAt(now);
    rule.setUpdatedAt(now);

    rule = repository.save(rule);

    // Ensure at least EMPLOYEE is persisted when recipients are not provided.
    Set<RecipientType> effectiveRecipients = (recipients == null || recipients.isEmpty())
        ? Set.of(RecipientType.EMPLOYEE)
        : recipients;

    Set<LeaveAlertRuleRecipient> recs = new HashSet<>();
    for (RecipientType rt : effectiveRecipients) {
      LeaveAlertRuleRecipient r = new LeaveAlertRuleRecipient();
      r.setRule(rule);
      r.setRecipientType(rt);
      recs.add(r);
    }

    // On create: save rule, then delete none, then save recipients list.
    recipientRepository.saveAll(recs);
    rule.setRecipients(recs);

    return rule;
  }

  @Transactional(readOnly = true)
  public List<LeaveAlertRule> list() {
    return repository.findAll();
  }

  @Transactional
  public LeaveAlertRule update(Long ruleId,
                               RuleScopeType scopeType,
                               Long leaveTypeId,
                               ThresholdOperator operator,
                               BigDecimal thresholdValue,
                               Set<RecipientType> recipients,
                               RuleFrequency frequency,
                               Boolean active) {

    LeaveAlertRule rule = repository.findById(ruleId).orElseThrow(() -> new NotFoundException("Rule not found"));

    if (scopeType != null) {
      rule.setScopeType(scopeType);
      if (scopeType == RuleScopeType.ONE) {
        if (leaveTypeId == null) {
          throw new IllegalArgumentException("leaveTypeId is required when scope type is ONE");
        }
        rule.setLeaveType(leaveTypeRepository.findById(leaveTypeId).orElseThrow(() -> new NotFoundException("Leave type not found")));
      } else {
        rule.setLeaveType(null);
      }
    }

    if (operator != null) {
      rule.setThresholdOperator(operator);
    }
    if (thresholdValue != null) {
      rule.setThresholdValue(thresholdValue);
    }
    if (frequency != null) {
      rule.setFrequency(frequency);
    }
    if (active != null) {
      rule.setActive(active);
    }

    // Update recipients by replacing existing recipients.
    if (recipients != null) {
      Set<RecipientType> effectiveRecipients = recipients.isEmpty()
          ? Set.of(RecipientType.EMPLOYEE)
          : recipients;

      recipientRepository.deleteAllByRule_Id(rule.getId());

      Set<LeaveAlertRuleRecipient> recs = new HashSet<>();
      for (RecipientType rt : effectiveRecipients) {
        LeaveAlertRuleRecipient r = new LeaveAlertRuleRecipient();
        r.setRule(rule);
        r.setRecipientType(rt);
        recs.add(r);
      }
      recipientRepository.saveAll(recs);
      rule.setRecipients(recs);
    }

    rule.setUpdatedAt(Instant.now(clock));
    return repository.save(rule);
  }
}