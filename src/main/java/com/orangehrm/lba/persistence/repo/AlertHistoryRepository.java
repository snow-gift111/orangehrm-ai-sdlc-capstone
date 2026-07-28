package com.orangehrm.lba.persistence.repo;

import com.orangehrm.lba.persistence.entity.AlertHistoryEntity;
import java.time.LocalDate;
import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;

public interface AlertHistoryRepository extends JpaRepository<AlertHistoryEntity, Long> {
  Optional<AlertHistoryEntity> findByDedupKeyAndSuppressionWindowStart(String dedupKey, LocalDate suppressionWindowStart);
}
