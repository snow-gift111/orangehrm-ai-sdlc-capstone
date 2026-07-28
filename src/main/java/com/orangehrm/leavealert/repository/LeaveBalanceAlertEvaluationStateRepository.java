package com.orangehrm.leavealert.repository;

import com.orangehrm.leavealert.entity.LeaveBalanceAlertEvaluationState;
import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;

public interface LeaveBalanceAlertEvaluationStateRepository extends JpaRepository<LeaveBalanceAlertEvaluationState, Long> {

    Optional<LeaveBalanceAlertEvaluationState> findByPolicyIdAndSubjectEmployeeIdAndLeaveTypeId(
            Long policyId,
            String subjectEmployeeId,
            String leaveTypeId);
}
