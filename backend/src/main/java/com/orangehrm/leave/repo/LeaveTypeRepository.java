package com.orangehrm.leave.repo;

import com.orangehrm.leave.domain.LeaveTypeEntity;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;
import java.util.Optional;

public interface LeaveTypeRepository extends JpaRepository<LeaveTypeEntity, Long> {
    Optional<LeaveTypeEntity> findByNameIgnoreCase(String name);

    List<LeaveTypeEntity> findAllByActiveTrueOrderByNameAsc();
}
