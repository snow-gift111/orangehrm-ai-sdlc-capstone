package com.orangehrm.leave.repo;

import com.orangehrm.leave.domain.LeaveAlertRule;
import com.orangehrm.leave.domain.RuleScopeType;
import java.util.List;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;

public interface LeaveAlertRuleRepository extends JpaRepository<LeaveAlertRule, Long> {

  @Query("select distinct r from LeaveAlertRule r left join fetch r.recipients where r.active = true and (r.scopeType = :allScope or (r.scopeType = :oneScope and r.leaveType.id = :leaveTypeId))")
  List<LeaveAlertRule> findActiveRulesForLeaveType(@Param("leaveTypeId") Long leaveTypeId,
                                                   @Param("allScope") RuleScopeType allScope,
                                                   @Param("oneScope") RuleScopeType oneScope);
}
