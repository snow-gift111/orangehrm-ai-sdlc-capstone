package com.orangehrm.leave.repo;

import com.orangehrm.leave.domain.LeaveType;
import java.util.Optional;
import org.springframework.data.jpa.repository.JpaRepository;

public interface LeaveTypeRepository extends JpaRepository<LeaveType, Long> {
  Optional<LeaveType> findByNameIgnoreCase(String name);
}
