package com.orangehrm.lba.integration.leave;

import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.Collection;
import java.util.List;
import org.springframework.boot.autoconfigure.condition.ConditionalOnProperty;
import org.springframework.stereotype.Component;

/**
 * Development stub.
 *
 * <p>Enabled when lba.integration.leave.stub=true.
 */
@Component
@ConditionalOnProperty(name = "lba.integration.leave.stub", havingValue = "true", matchIfMissing = true)
public class InMemoryLeaveBalanceProvider implements LeaveBalanceProvider {

  @Override
  public List<LeaveBalanceRecord> getBalancesForLeaveTypes(Collection<Long> leaveTypeIds) {
    // Minimal deterministic seed data for demo/dev.
    // In production, replace with DB/service integration.
    List<LeaveBalanceRecord> out = new ArrayList<>();
    for (Long leaveTypeId : leaveTypeIds) {
      out.add(new LeaveBalanceRecord(1001L, leaveTypeId, new BigDecimal("3.00")));
      out.add(new LeaveBalanceRecord(1002L, leaveTypeId, new BigDecimal("0.00")));
    }
    return out;
  }
}
