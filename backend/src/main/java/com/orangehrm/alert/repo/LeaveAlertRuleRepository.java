package com.orangehrm.alert.repo;

import com.orangehrm.alert.domain.LeaveAlertRuleEntity;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface LeaveAlertRuleRepository extends JpaRepository<LeaveAlertRuleEntity, Long> {
    List<LeaveAlertRuleEntity> findAllByActiveTrueOrderByIdAsc();
}
