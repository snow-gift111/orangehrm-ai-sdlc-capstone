package com.orangehrm.lba.domain.entity;

import com.orangehrm.lba.domain.enums.AlertEventStatus;
import jakarta.persistence.*;
import lombok.Getter;
import lombok.Setter;

import java.math.BigDecimal;
import java.time.Instant;

@Entity
@Table(name = "lba_alert_event")
@Getter
@Setter
public class AlertEventEntity {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY, optional = false)
    @JoinColumn(name = "rule_id", nullable = false)
    private AlertRuleEntity rule;

    @Column(name = "employee_id", nullable = false)
    private Long employeeId;

    @Column(name = "leave_type_id")
    private Long leaveTypeId;

    @Column(name = "balance_snapshot", nullable = false, precision = 10, scale = 2)
    private BigDecimal balanceSnapshot;

    @Column(name = "evaluated_at", nullable = false)
    private Instant evaluatedAt;

    @Enumerated(EnumType.STRING)
    @Column(name = "status", nullable = false, length = 24)
    private AlertEventStatus status;

    @Column(name = "failure_reason", columnDefinition = "TEXT")
    private String failureReason;

    @Column(name = "sent_at")
    private Instant sentAt;

    @Column(name = "job_run_id", length = 64)
    private String jobRunId;

    @PrePersist
    void prePersist() {
        if (evaluatedAt == null) evaluatedAt = Instant.now();
        if (status == null) status = AlertEventStatus.PENDING;
    }
}
