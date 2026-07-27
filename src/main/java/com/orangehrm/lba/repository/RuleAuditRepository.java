package com.orangehrm.lba.repository;

import com.orangehrm.lba.domain.entity.RuleAuditEntity;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface RuleAuditRepository extends JpaRepository<RuleAuditEntity, Long> {
    List<RuleAuditEntity> findAllByRuleIdOrderByTimestampDesc(Long ruleId);
}
