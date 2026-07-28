package com.orangehrm.leave.repo;

import com.orangehrm.leave.domain.LeaveAlertEvent;
import org.springframework.data.jpa.repository.JpaRepository;

public interface LeaveAlertEventRepository extends JpaRepository<LeaveAlertEvent, Long> {}
