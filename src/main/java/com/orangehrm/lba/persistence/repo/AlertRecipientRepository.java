package com.orangehrm.lba.persistence.repo;

import com.orangehrm.lba.persistence.entity.AlertRecipientEntity;
import java.util.List;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

public interface AlertRecipientRepository extends JpaRepository<AlertRecipientEntity, Long> {

  @Query(
      "select ar.alertHistory.id from AlertRecipientEntity ar "
          + "where ar.recipientUserId = :userId order by ar.alertHistory.generatedAt desc")
  Page<Long> findAlertHistoryIdsForRecipient(@Param("userId") String userId, Pageable pageable);

  List<AlertRecipientEntity> findAllByAlertHistoryId(Long alertHistoryId);
}
