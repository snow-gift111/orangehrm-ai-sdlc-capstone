package com.orangehrm.leavealert.repository;

import com.orangehrm.leavealert.entity.LeaveBalanceAlertPolicy;
import java.time.Instant;
import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

public interface LeaveBalanceAlertPolicyRepository extends JpaRepository<LeaveBalanceAlertPolicy, Long> {

    List<LeaveBalanceAlertPolicy> findByActive(boolean active);

    List<LeaveBalanceAlertPolicy> findByLeaveTypeIdAndActive(String leaveTypeId, boolean active);

    @Query("""
            select p from LeaveBalanceAlertPolicy p
            where p.active = true
              and (p.effectiveFrom is null or p.effectiveFrom <= :asOf)
              and (p.effectiveTo is null or p.effectiveTo >= :asOf)
            """)
    List<LeaveBalanceAlertPolicy> findActiveEffectivePolicies(@Param("asOf") Instant asOf);
}
