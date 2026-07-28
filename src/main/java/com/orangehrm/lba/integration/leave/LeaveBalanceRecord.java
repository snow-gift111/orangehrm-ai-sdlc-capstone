package com.orangehrm.lba.integration.leave;

import java.math.BigDecimal;

public record LeaveBalanceRecord(Long employeeId, Long leaveTypeId, BigDecimal balance) {}
