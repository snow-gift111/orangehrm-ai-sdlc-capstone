package com.orangehrm.leavealert.repository;

import com.orangehrm.leavealert.domain.entity.AlertRuleEntity;
import java.util.List;
import java.util.UUID;
import org.springframework.data.jpa.repository.JpaRepository;

public interface AlertRuleRepository extends JpaRepository<AlertRuleEntity, UUID> {

    List<AlertRuleEntity> findByEnabledTrue();
}
