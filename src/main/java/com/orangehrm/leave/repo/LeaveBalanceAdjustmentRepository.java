package com.orangehrm.leave.repo;

import com.orangehrm.leave.domain.LeaveBalanceAdjustment;
import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;

public interface LeaveBalanceAdjustmentRepository extends JpaRepository<LeaveBalanceAdjustment, Long> {
  List<LeaveBalanceAdjustment> findTop20ByEmployeeIdAndLeaveTypeIdOrderByCreatedAtDesc(Long employeeId, Long leaveTypeId);
}
