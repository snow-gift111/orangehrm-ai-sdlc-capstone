package com.orangehrm.lba.persistence.entity;

import com.orangehrm.lba.domain.model.RecipientType;
import jakarta.persistence.*;
import java.time.Instant;

@Entity
@Table(name = "lba_alert_recipient")
public class AlertRecipientEntity {

  @Id
  @GeneratedValue(strategy = GenerationType.IDENTITY)
  private Long id;

  @ManyToOne(fetch = FetchType.LAZY)
  @JoinColumn(name = "alert_history_id", nullable = false)
  private AlertHistoryEntity alertHistory;

  @Column(name = "recipient_user_id", nullable = false, length = 64)
  private String recipientUserId;

  @Enumerated(EnumType.STRING)
  @Column(name = "recipient_type", nullable = false, length = 16)
  private RecipientType recipientType;

  @Column(name = "delivered_at")
  private Instant deliveredAt;

  @Column(name = "read_at")
  private Instant readAt;

  public Long getId() {
    return id;
  }

  public void setId(Long id) {
    this.id = id;
  }

  public AlertHistoryEntity getAlertHistory() {
    return alertHistory;
  }

  public void setAlertHistory(AlertHistoryEntity alertHistory) {
    this.alertHistory = alertHistory;
  }

  public String getRecipientUserId() {
    return recipientUserId;
  }

  public void setRecipientUserId(String recipientUserId) {
    this.recipientUserId = recipientUserId;
  }

  public RecipientType getRecipientType() {
    return recipientType;
  }

  public void setRecipientType(RecipientType recipientType) {
    this.recipientType = recipientType;
  }

  public Instant getDeliveredAt() {
    return deliveredAt;
  }

  public void setDeliveredAt(Instant deliveredAt) {
    this.deliveredAt = deliveredAt;
  }

  public Instant getReadAt() {
    return readAt;
  }

  public void setReadAt(Instant readAt) {
    this.readAt = readAt;
  }
}
