package com.orangehrm.lba.domain.entity;

import com.orangehrm.lba.domain.enums.RecipientDeliveryStatus;
import com.orangehrm.lba.domain.enums.RecipientType;
import jakarta.persistence.*;
import lombok.Getter;
import lombok.Setter;

@Entity
@Table(name = "lba_alert_event_recipient")
@Getter
@Setter
public class AlertEventRecipientEntity {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY, optional = false)
    @JoinColumn(name = "event_id", nullable = false)
    private AlertEventEntity event;

    @Enumerated(EnumType.STRING)
    @Column(name = "recipient_type", nullable = false, length = 24)
    private RecipientType recipientType;

    @Column(name = "recipient_user_id")
    private Long recipientUserId;

    @Column(name = "recipient_email", length = 320)
    private String recipientEmail;

    @Enumerated(EnumType.STRING)
    @Column(name = "delivery_status", nullable = false, length = 24)
    private RecipientDeliveryStatus deliveryStatus;

    @Column(name = "failure_reason", columnDefinition = "TEXT")
    private String failureReason;
}
