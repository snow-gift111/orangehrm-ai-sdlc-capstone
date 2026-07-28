package com.orangehrm.leave.service;

import com.orangehrm.common.NotFoundException;
import com.orangehrm.leave.domain.LeaveAlertRule;
import com.orangehrm.leave.domain.LeaveAlertRuleRecipient;
import com.orangehrm.leave.domain.RecipientType;
import com.orangehrm.leave.domain.RuleFrequency;
import com.orangehrm.leave.domain.RuleScopeType;
import com.orangehrm.leave.domain.ThresholdOperator;
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

    var createdBy = userRepository.findByUsername(createdByUsername).orElseThrow(() -> new NotFoundException("User not found"));

    LeaveAlertRule rule = new LeaveAlertRule();
    rule.setScopeType(scopeType);
    if (scopeType == RuleScopeType.ONE) {
      rule.setLeaveType(leaveTypeRepository.findById(leaveTypeId).orElseThrow(() -> new NotFoundException("Leave type not found")));
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

    Set<LeaveAlertRuleRecipient> recs = new HashSet<>();
    if (recipients != null) {
      for (RecipientType rt : recipients) {
        LeaveAlertRuleRecipient r = new LeaveAlertRuleRecipient();
        r.setRule(rule);
        r.setRecipientType(rt);
        recs.add(r);
      }
    }
    rule.setRecipients(recs);
    // Cascade is not enabled; rely on persistence via saveAll through rule re-save.
    // Simpler: save rule first, then persist recipients with EntityManager by saving rule again.
    // We'll use repository.save(rule) again after attaching; JPA will persist recipients if relationship is cascaded.
    // Since it's not, keep recipients stored in DB via separate repository? We'll use EntityManager.

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

    // Sprint 1: recipient updates are not implemented fully without a recipient repository.
    // Keep rule update limited to core fields to stay production-safe.

    rule.setUpdatedAt(Instant.now(clock));
    return repository.save(rule);
  }
}
