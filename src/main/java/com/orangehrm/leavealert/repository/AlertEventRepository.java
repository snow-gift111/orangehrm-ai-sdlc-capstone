package com.orangehrm.leavealert.repository;

import com.orangehrm.leavealert.domain.entity.AlertEventEntity;
import java.util.UUID;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.JpaRepository;

public interface AlertEventRepository extends JpaRepository<AlertEventEntity, UUID> {

    Page<AlertEventEntity> findAllByEmployeeIdOrderByEvaluatedAtDesc(String employeeId, Pageable pageable);
}
