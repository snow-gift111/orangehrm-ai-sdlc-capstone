package com.orangehrm.lba.service;

import com.orangehrm.lba.domain.model.ThresholdCondition;
import java.math.BigDecimal;
import org.springframework.stereotype.Service;

@Service
public class AlertMatchService {

  public boolean matches(ThresholdCondition condition, BigDecimal threshold, BigDecimal balance) {
    if (condition == null || threshold == null || balance == null) {
      return false;
    }
    int cmp = balance.compareTo(threshold);
    return switch (condition) {
      case LT -> cmp < 0;
      case LEQ -> cmp <= 0;
      case EQ -> cmp == 0;
      case GT -> cmp > 0;
      case GEQ -> cmp >= 0;
    };
  }

  public String buildBreachedText(ThresholdCondition condition, BigDecimal threshold) {
    return "balance " + symbol(condition) + " " + threshold.stripTrailingZeros().toPlainString();
  }

  private String symbol(ThresholdCondition condition) {
    return switch (condition) {
      case LT -> "<";
      case LEQ -> "<=";
      case EQ -> "=";
      case GT -> ">";
      case GEQ -> ">=";
    };
  }
}
