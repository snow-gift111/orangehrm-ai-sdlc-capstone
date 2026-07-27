package com.orangehrm.lba.integration;

import org.springframework.context.annotation.Profile;
import org.springframework.stereotype.Component;

import java.math.BigDecimal;
import java.util.List;
import java.util.Map;

/**
 * Demo provider for local runs.
 *
 * In real OrangeHRM, replace with an implementation backed by the Leave module (DB queries or service calls).
 */
@Component
@Profile("demo")
public class InMemoryLeaveBalanceProvider implements LeaveBalanceProvider {

    private final List<EmployeeRef> employees = List.of(
            new EmployeeRef(1L, "Alice Employee", "alice@example.com", 2L),
            new EmployeeRef(2L, "Mark Manager", "mark.manager@example.com", null)
    );

    private final List<LeaveTypeRef> leaveTypes = List.of(
            new LeaveTypeRef(10L, "Annual Leave"),
            new LeaveTypeRef(20L, "Sick Leave")
    );

    private final Map<String, BigDecimal> balances = Map.of(
            key(1L, 10L), new BigDecimal("1.50"),
            key(1L, 20L), new BigDecimal("5.00"),
            key(2L, 10L), new BigDecimal("10.00"),
            key(2L, 20L), new BigDecimal("3.00")
    );

    @Override
    public List<EmployeeRef> listEmployees() {
        return employees;
    }

    @Override
    public List<LeaveTypeRef> listLeaveTypes() {
        return leaveTypes;
    }

    @Override
    public BigDecimal getBalance(long employeeId, long leaveTypeId) {
        return balances.getOrDefault(key(employeeId, leaveTypeId), BigDecimal.ZERO);
    }

    private static String key(long employeeId, long leaveTypeId) {
        return employeeId + ":" + leaveTypeId;
    }
}
