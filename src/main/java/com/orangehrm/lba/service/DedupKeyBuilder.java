package com.orangehrm.lba.service;

import com.orangehrm.lba.domain.model.ThresholdCondition;
import java.math.BigDecimal;
import java.util.Objects;

public final class DedupKeyBuilder {
  private DedupKeyBuilder() {}

  public static String build(
      long ruleId,
      long employeeId,
      long leaveTypeId,
      ThresholdCondition condition,
      BigDecimal thresholdValue) {
    Objects.requireNonNull(condition, "condition");
    Objects.requireNonNull(thresholdValue, "thresholdValue");
    return ruleId
        + "|"
        + employeeId
        + "|"
        + leaveTypeId
        + "|"
        + condition.name()
        + "|"
        + thresholdValue.stripTrailingZeros().toPlainString();
  }
}
