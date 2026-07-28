package com.orangehrm.leavealert.repository;

import com.orangehrm.leavealert.entity.LeaveBalanceAlertRecipient;
import java.time.Instant;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

public interface LeaveBalanceAlertRecipientRepository extends JpaRepository<LeaveBalanceAlertRecipient, Long> {

    @Query("""
            select r from LeaveBalanceAlertRecipient r
            join fetch r.alertEvent e
            join fetch e.policy p
            where r.recipientUserId = :recipientUserId
              and (:from is null or e.generatedAt >= :from)
              and (:to is null or e.generatedAt <= :to)
            order by e.generatedAt desc
            """)
    Page<LeaveBalanceAlertRecipient> findVisibleNotifications(
            @Param("recipientUserId") String recipientUserId,
            @Param("from") Instant from,
            @Param("to") Instant to,
            Pageable pageable);
}
