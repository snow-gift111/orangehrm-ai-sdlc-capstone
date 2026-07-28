package com.orangehrm.leavealert.repository;

import com.orangehrm.leavealert.domain.DeliveryStatus;
import com.orangehrm.leavealert.entity.LeaveBalanceAlertEvent;
import java.time.Instant;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

public interface LeaveBalanceAlertEventRepository extends JpaRepository<LeaveBalanceAlertEvent, Long> {

    boolean existsByDedupKey(String dedupKey);

    @Query("""
            select e from LeaveBalanceAlertEvent e
            join fetch e.policy p
            where (:employeeId is null or e.subjectEmployeeId = :employeeId)
              and (:leaveTypeId is null or e.leaveTypeId = :leaveTypeId)
              and (:status is null or e.deliveryStatus = :status)
              and (:from is null or e.generatedAt >= :from)
              and (:to is null or e.generatedAt <= :to)
            order by e.generatedAt desc
            """)
    Page<LeaveBalanceAlertEvent> searchAlertLog(
            @Param("employeeId") String employeeId,
            @Param("leaveTypeId") String leaveTypeId,
            @Param("status") DeliveryStatus status,
            @Param("from") Instant from,
            @Param("to") Instant to,
            Pageable pageable);
}
