package com.orangehrm.lba.repository;

import com.orangehrm.lba.domain.entity.AlertRuleEntity;
import com.orangehrm.lba.domain.enums.RuleStatus;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface AlertRuleRepository extends JpaRepository<AlertRuleEntity, Long> {
    List<AlertRuleEntity> findAllByStatus(RuleStatus status);
}
