package com.orangehrm.leave.service;

import com.orangehrm.common.NotFoundException;
import com.orangehrm.leave.domain.AlertStatus;
import com.orangehrm.leave.domain.LeaveAlertRecipient;
import com.orangehrm.leave.repo.LeaveAlertRecipientRepository;
import java.time.Clock;
import java.time.Instant;
import lombok.RequiredArgsConstructor;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@RequiredArgsConstructor
public class NotificationService {

  private final LeaveAlertRecipientRepository recipientRepository;
  private final Clock clock;

  @Transactional(readOnly = true)
  public Page<LeaveAlertRecipient> listMyAlerts(String username, AlertStatus status, int page, int pageSize) {
    Pageable pageable = PageRequest.of(Math.max(0, page), Math.min(Math.max(1, pageSize), 100));
    if (status == null) {
      return recipientRepository.findAllByRecipientUser_UsernameOrderByNewAtDesc(username, pageable);
    }
    return recipientRepository.findAllByRecipientUser_UsernameAndStatusOrderByNewAtDesc(username, status, pageable);
  }

  @Transactional
  public LeaveAlertRecipient acknowledge(Long alertRecipientId, String username) {
    LeaveAlertRecipient recipient = recipientRepository.findByIdAndRecipientUser_Username(alertRecipientId, username)
        .orElseThrow(() -> new NotFoundException("Alert not found"));

    if (recipient.getStatus() == AlertStatus.ACKNOWLEDGED) {
      return recipient;
    }

    recipient.setStatus(AlertStatus.ACKNOWLEDGED);
    recipient.setAcknowledgedAt(Instant.now(clock));
    return recipientRepository.save(recipient);
  }
}
