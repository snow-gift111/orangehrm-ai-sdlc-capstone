package com.orangehrm.leave.service;

import com.orangehrm.leave.domain.LeaveAlertRule;
import com.orangehrm.leave.domain.ThresholdOperator;
import java.math.BigDecimal;
import org.springframework.stereotype.Component;

@Component
public class ThresholdEvaluator {

  public boolean isBreached(LeaveAlertRule rule, BigDecimal currentBalance) {
    if (rule.getThresholdOperator() == ThresholdOperator.PCT_LTE) {
      // Entitlement base is not part of Sprint 1. Persist operator/value but do not evaluate.
      return false;
    }

    int cmp = currentBalance.compareTo(rule.getThresholdValue());
    return switch (rule.getThresholdOperator()) {
      case LT -> cmp < 0;
      case LTE -> cmp <= 0;
      case PCT_LTE -> false;
    };
  }

  public String breachDisplay(LeaveAlertRule rule) {
    return rule.getThresholdOperator().name() + " " + rule.getThresholdValue().toPlainString();
  }
}
