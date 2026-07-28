package com.orangehrm.alert.repo;

import com.orangehrm.alert.domain.LeaveAlertEntity;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

import java.time.Instant;
import java.util.Optional;

public interface LeaveAlertRepository extends JpaRepository<LeaveAlertEntity, Long> {

    @Query("select a from LeaveAlertEntity a where a.ruleId = :ruleId and a.employeeId = :employeeId and a.triggeredAt >= :since order by a.triggeredAt desc limit 1")
    Optional<LeaveAlertEntity> findLatestSince(@Param("ruleId") long ruleId,
                                              @Param("employeeId") long employeeId,
                                              @Param("since") Instant since);
}
