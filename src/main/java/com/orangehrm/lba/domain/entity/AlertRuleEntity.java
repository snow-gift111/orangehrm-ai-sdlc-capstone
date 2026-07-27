package com.orangehrm.lba.domain.entity;

import com.orangehrm.lba.domain.enums.LeaveTypeScope;
import com.orangehrm.lba.domain.enums.RuleStatus;
import com.orangehrm.lba.domain.enums.ThresholdOperator;
import jakarta.persistence.*;
import lombok.Getter;
import lombok.Setter;

import java.math.BigDecimal;
import java.time.Instant;

@Entity
@Table(name = "lba_alert_rule")
@Getter
@Setter
public class AlertRuleEntity {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Enumerated(EnumType.STRING)
    @Column(name = "leave_type_scope", nullable = false, length = 16)
    private LeaveTypeScope leaveTypeScope;

    @Column(name = "leave_type_id")
    private Long leaveTypeId;

    @Enumerated(EnumType.STRING)
    @Column(name = "operator", nullable = false, length = 4)
    private ThresholdOperator operator;

    @Column(name = "threshold_value", nullable = false, precision = 10, scale = 2)
    private BigDecimal thresholdValue;

    @Column(name = "suppression_window_days", nullable = false)
    private Integer suppressionWindowDays;

    @Column(name = "recipients_json", nullable = false, columnDefinition = "TEXT")
    private String recipientsJson;

    @Enumerated(EnumType.STRING)
    @Column(name = "status", nullable = false, length = 16)
    private RuleStatus status;

    @Column(name = "created_at", nullable = false)
    private Instant createdAt;

    @Column(name = "created_by", length = 128)
    private String createdBy;

    @Column(name = "updated_at", nullable = false)
    private Instant updatedAt;

    @Column(name = "updated_by", length = 128)
    private String updatedBy;

    @PrePersist
    void prePersist() {
        Instant now = Instant.now();
        if (createdAt == null) createdAt = now;
        if (updatedAt == null) updatedAt = now;
        if (suppressionWindowDays == null) suppressionWindowDays = 0;
        if (status == null) status = RuleStatus.INACTIVE;
    }

    @PreUpdate
    void preUpdate() {
        updatedAt = Instant.now();
    }
}
