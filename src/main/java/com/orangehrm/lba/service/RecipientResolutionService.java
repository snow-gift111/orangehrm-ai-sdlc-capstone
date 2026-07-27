package com.orangehrm.lba.service;

import com.orangehrm.lba.domain.enums.RecipientType;
import com.orangehrm.lba.domain.model.AlertRecipientConfig;
import com.orangehrm.lba.integration.LeaveBalanceProvider;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

@Service
public class RecipientResolutionService {

    private final LeaveBalanceProvider leaveBalanceProvider;

    public RecipientResolutionService(LeaveBalanceProvider leaveBalanceProvider) {
        this.leaveBalanceProvider = leaveBalanceProvider;
    }

    public List<ResolvedRecipient> resolve(AlertRecipientConfig config, LeaveBalanceProvider.EmployeeRef employee) {
        List<ResolvedRecipient> out = new ArrayList<>();

        if (Boolean.TRUE.equals(config.includeEmployee())) {
            out.add(new ResolvedRecipient(RecipientType.EMPLOYEE, null, employee.email(),
                    employee.email() != null ? null : "Employee email not available"));
        }

        if (Boolean.TRUE.equals(config.includeManager())) {
            if (employee.managerEmployeeId() == null) {
                out.add(new ResolvedRecipient(RecipientType.MANAGER, null, null, "Manager not set"));
            } else {
                Optional<LeaveBalanceProvider.EmployeeRef> mgr = leaveBalanceProvider.listEmployees().stream()
                        .filter(e -> e.employeeId() == employee.managerEmployeeId())
                        .findFirst();
                if (mgr.isEmpty()) {
                    out.add(new ResolvedRecipient(RecipientType.MANAGER, null, null, "Manager record not found"));
                } else {
                    out.add(new ResolvedRecipient(RecipientType.MANAGER, null, mgr.get().email(),
                            mgr.get().email() != null ? null : "Manager email not available"));
                }
            }
        }

        // HR role/group and specific user IDs are integration points with an identity system.
        // For this baseline implementation, we only support direct email recipients.

        for (String email : config.specificEmails()) {
            out.add(new ResolvedRecipient(RecipientType.SPECIFIC_EMAIL, null, email, null));
        }

        return out;
    }

    public record ResolvedRecipient(RecipientType type, Long userId, String email, String notDeliverableReason) {
    }
}
