package com.orangehrm.leavealert.repository;

import com.orangehrm.leavealert.domain.entity.AlertRuleLeaveTypeEntity;
import com.orangehrm.leavealert.domain.entity.AlertRuleLeaveTypeEntity.Pk;
import java.util.List;
import java.util.UUID;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

public interface AlertRuleLeaveTypeRepository extends JpaRepository<AlertRuleLeaveTypeEntity, Pk> {

    @Query("select r.pk.leaveTypeId from AlertRuleLeaveTypeEntity r where r.pk.ruleId = :ruleId")
    List<String> findLeaveTypeIdsByRuleId(@Param("ruleId") UUID ruleId);

    void deleteByPkRuleId(UUID ruleId);
}
