package com.orangehrm.lba.persistence.repo;

import com.orangehrm.lba.domain.model.AlertRuleStatus;
import com.orangehrm.lba.persistence.entity.AlertRuleEntity;
import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;

public interface AlertRuleRepository extends JpaRepository<AlertRuleEntity, Long> {
  List<AlertRuleEntity> findAllByStatus(AlertRuleStatus status);
}
