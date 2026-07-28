package com.orangehrm.leave.repo;

import com.orangehrm.leave.domain.AlertStatus;
import com.orangehrm.leave.domain.LeaveAlertRecipient;
import java.util.List;
import java.util.Optional;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.Pageable;
import org.springframework.data.jpa.repository.EntityGraph;
import org.springframework.data.jpa.repository.JpaRepository;

public interface LeaveAlertRecipientRepository extends JpaRepository<LeaveAlertRecipient, Long> {

  @EntityGraph(attributePaths = {"alertEvent", "alertEvent.leaveType", "recipientUser"})
  Page<LeaveAlertRecipient> findAllByRecipientUser_UsernameOrderByNewAtDesc(String username, Pageable pageable);

  @EntityGraph(attributePaths = {"alertEvent", "alertEvent.leaveType", "recipientUser"})
  Page<LeaveAlertRecipient> findAllByRecipientUser_UsernameAndStatusOrderByNewAtDesc(String username, AlertStatus status, Pageable pageable);

  Optional<LeaveAlertRecipient> findByIdAndRecipientUser_Username(Long id, String username);

  List<LeaveAlertRecipient> findAllByAlertEvent_Id(Long alertEventId);
}
