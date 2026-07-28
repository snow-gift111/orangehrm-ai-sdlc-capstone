package com.orangehrm.lba.persistence.repo;

import com.orangehrm.lba.persistence.entity.AlertRuleAuditEntity;
import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;

public interface AlertRuleAuditRepository extends JpaRepository<AlertRuleAuditEntity, Long> {
  List<AlertRuleAuditEntity> findAllByRuleIdOrderByActionAtDesc(Long ruleId);
}
