package com.orangehrm.alert.repo;

import com.orangehrm.alert.domain.LeaveAlertRecipientEntity;
import com.orangehrm.alert.domain.LeaveAlertRecipientEntity.Pk;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

public interface LeaveAlertRecipientRepository extends JpaRepository<LeaveAlertRecipientEntity, Pk> {

    @Query("select ar.pk.alertId from LeaveAlertRecipientEntity ar where ar.pk.userId = :userId")
    Page<Long> findAlertIdsByUserId(@Param("userId") long userId, Pageable pageable);
}
