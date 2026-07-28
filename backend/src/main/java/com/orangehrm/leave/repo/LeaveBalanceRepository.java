package com.orangehrm.leave.repo;

import com.orangehrm.leave.domain.LeaveBalanceEntity;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;
import java.util.Optional;

public interface LeaveBalanceRepository extends JpaRepository<LeaveBalanceEntity, Long> {

    List<LeaveBalanceEntity> findAllByEmployeeIdOrderByLeaveTypeIdAsc(Long employeeId);

    Optional<LeaveBalanceEntity> findByEmployeeIdAndLeaveTypeId(Long employeeId, Long leaveTypeId);

    Page<LeaveBalanceEntity> findAllByLeaveTypeId(Long leaveTypeId, Pageable pageable);
}
