package com.orangehrm.leave.repo;

import com.orangehrm.leave.domain.LeaveAlertRuleRecipient;
import org.springframework.data.jpa.repository.JpaRepository;

public interface LeaveAlertRuleRecipientRepository extends JpaRepository<LeaveAlertRuleRecipient, Long> {
  void deleteAllByRule_Id(Long ruleId);
}
