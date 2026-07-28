package com.orangehrm.leave.repo;

import com.orangehrm.leave.domain.LeaveBalance;
import java.util.List;
import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;

public interface LeaveBalanceRepository extends JpaRepository<LeaveBalance, Long> {
  Optional<LeaveBalance> findByEmployeeIdAndLeaveType_Id(Long employeeId, Long leaveTypeId);

  List<LeaveBalance> findAllByEmployeeId(Long employeeId);
}
