package com.orangehrm.lba.integration.leave;

import java.util.Collection;
import java.util.List;

/**
 * Adapter to retrieve current leave balances.
 *
 * <p>Integration with the actual OrangeHRM leave subsystem is implementation-specific.
 */
public interface LeaveBalanceProvider {

  /**
   * Fetch balances for the provided leave type ids.
   *
   * <p>Returned list may contain many employees.
   */
  List<LeaveBalanceRecord> getBalancesForLeaveTypes(Collection<Long> leaveTypeIds);
}
