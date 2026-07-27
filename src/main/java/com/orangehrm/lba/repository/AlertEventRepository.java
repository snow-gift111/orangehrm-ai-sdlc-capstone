package com.orangehrm.lba.repository;

import com.orangehrm.lba.domain.entity.AlertEventEntity;
import com.orangehrm.lba.domain.enums.AlertEventStatus;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.time.Instant;
import java.util.Optional;

public interface AlertEventRepository extends JpaRepository<AlertEventEntity, Long> {

    Page<AlertEventEntity> findAllByOrderByEvaluatedAtDesc(Pageable pageable);

    @Query("""
            select e from AlertEventEntity e
            where e.rule.id = :ruleId
              and e.employeeId = :employeeId
              and ((:leaveTypeId is null and e.leaveTypeId is null) or e.leaveTypeId = :leaveTypeId)
              and e.status = :status
              and e.sentAt >= :cutoff
            order by e.sentAt desc
            """)
    Optional<AlertEventEntity> findLatestSentWithinWindow(
            @Param("ruleId") Long ruleId,
            @Param("employeeId") Long employeeId,
            @Param("leaveTypeId") Long leaveTypeId,
            @Param("status") AlertEventStatus status,
            @Param("cutoff") Instant cutoff,
            Pageable pageable
    );
}
