package com.orangehrm.leavealert.integration.impl;

import com.orangehrm.leavealert.integration.LeaveBalanceProvider;
import java.math.BigDecimal;
import java.time.Instant;
import java.util.ArrayList;
import java.util.List;
import java.util.Random;
import org.springframework.context.annotation.Primary;
import org.springframework.stereotype.Component;

/**
 * Stub leave balance provider.
 *
 * The approved requirements state the leave balance data source is not confirmed.
 * This provider enables local execution and demo of the evaluation pipeline.
 * Replace with real integration (DB/service) in production.
 */
@Component
@Primary
public class StubLeaveBalanceProvider implements LeaveBalanceProvider {

    private final Random random = new Random(42);

    @Override
    public List<LeaveBalance> getBalances(List<String> employeeIds, List<String> leaveTypeIds, Instant asOf) {
        List<LeaveBalance> out = new ArrayList<>();
        for (String empId : employeeIds) {
            for (String leaveTypeId : leaveTypeIds) {
                // Deterministic-ish balances for demo.
                BigDecimal balance;
                if ("e001".equals(empId)) {
                    balance = new BigDecimal("1.00");
                } else {
                    balance = BigDecimal.valueOf(0.5 + (random.nextInt(20) / 2.0));
                }
                out.add(new LeaveBalance(empId, leaveTypeId, balance));
            }
        }
        return out;
    }
}
