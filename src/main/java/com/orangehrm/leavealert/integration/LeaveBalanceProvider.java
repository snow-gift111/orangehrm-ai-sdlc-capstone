package com.orangehrm.leavealert.integration;

import java.math.BigDecimal;
import java.time.Instant;
import java.util.List;

/**
 * Pluggable abstraction for retrieving leave balances, per approved solution design.
 */
public interface LeaveBalanceProvider {

    record LeaveBalance(String employeeId, String leaveTypeId, BigDecimal balance) {
    }

    /**
     * Fetch balances for the given employee and leave type IDs as of a timestamp.
     */
    List<LeaveBalance> getBalances(List<String> employeeIds, List<String> leaveTypeIds, Instant asOf);
}
