package com.orangehrm.leave.domain;

import com.orangehrm.user.AppUser;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;
import java.time.Instant;
import lombok.Getter;
import lombok.Setter;

@Entity
@Table(name = "leave_alert_recipient")
@Getter
@Setter
public class LeaveAlertRecipient {
  @Id
  @GeneratedValue(strategy = GenerationType.IDENTITY)
  private Long id;

  @ManyToOne(fetch = FetchType.LAZY)
  @JoinColumn(name = "alert_event_id", nullable = false)
  private LeaveAlertEvent alertEvent;

  @ManyToOne(fetch = FetchType.LAZY)
  @JoinColumn(name = "recipient_user_id", nullable = false)
  private AppUser recipientUser;

  @Enumerated(EnumType.STRING)
  @Column(nullable = false)
  private AlertStatus status;

  @Column(name = "new_at", nullable = false)
  private Instant newAt;

  @Column(name = "acknowledged_at")
  private Instant acknowledgedAt;

  @Column(name = "resolved_at")
  private Instant resolvedAt;

  @Column(name = "closed_at")
  private Instant closedAt;
}
