package com.orangehrm.lba.repository;

import com.orangehrm.lba.domain.entity.AlertEventRecipientEntity;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface AlertEventRecipientRepository extends JpaRepository<AlertEventRecipientEntity, Long> {
    List<AlertEventRecipientEntity> findAllByEventId(Long eventId);
}
