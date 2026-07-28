package com.orangehrm.leavealert.entity;

import com.orangehrm.leavealert.domain.RecipientType;
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

@Entity
@Table(name = "leave_balance_alert_recipient")
public class LeaveBalanceAlertRecipient {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY, optional = false)
    @JoinColumn(name = "alert_event_id", nullable = false)
    private LeaveBalanceAlertEvent alertEvent;

    @Column(name = "recipient_user_id", nullable = false, length = 128)
    private String recipientUserId;

    @Enumerated(EnumType.STRING)
    @Column(name = "recipient_type", nullable = false, length = 32)
    private RecipientType recipientType;

    @Column(name = "visible_from", nullable = false)
    private Instant visibleFrom;

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public LeaveBalanceAlertEvent getAlertEvent() {
        return alertEvent;
    }

    public void setAlertEvent(LeaveBalanceAlertEvent alertEvent) {
        this.alertEvent = alertEvent;
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

    public Instant getVisibleFrom() {
        return visibleFrom;
    }

    public void setVisibleFrom(Instant visibleFrom) {
        this.visibleFrom = visibleFrom;
    }
}
