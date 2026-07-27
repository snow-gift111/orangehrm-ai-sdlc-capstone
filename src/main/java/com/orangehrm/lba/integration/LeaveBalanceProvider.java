package com.orangehrm.lba.integration;

import java.math.BigDecimal;
import java.util.List;

/**
 * Abstraction for retrieving leave balances. Implementation must be adapted to the real OrangeHRM Leave module.
 */
public interface LeaveBalanceProvider {

    List<EmployeeRef> listEmployees();

    /**
     * Returns available leave type ids for evaluation when rules target ALL leave types.
     */
    List<LeaveTypeRef> listLeaveTypes();

    /**
     * Retrieve balance for a given employee and leave type.
     */
    BigDecimal getBalance(long employeeId, long leaveTypeId);

    record EmployeeRef(long employeeId, String displayName, String email, Long managerEmployeeId) {}

    record LeaveTypeRef(long leaveTypeId, String name) {}
}
