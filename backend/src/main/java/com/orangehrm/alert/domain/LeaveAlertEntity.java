package com.orangehrm.alert.domain;

import jakarta.persistence.*;

import java.math.BigDecimal;
import java.time.Instant;

@Entity
@Table(name = "leave_alert")
public class LeaveAlertEntity {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "rule_id", nullable = false)
    private Long ruleId;

    @Column(name = "employee_id", nullable = false)
    private Long employeeId;

    @Column(name = "leave_type_id", nullable = false)
    private Long leaveTypeId;

    @Column(name = "evaluated_balance_value", nullable = false, precision = 10, scale = 2)
    private BigDecimal evaluatedBalanceValue;

    @Column(name = "triggered_at", nullable = false)
    private Instant triggeredAt;

    @Enumerated(EnumType.STRING)
    @Column(name = "delivery_status", nullable = false, length = 16)
    private AlertDeliveryStatus deliveryStatus;

    @Lob
    @Column(name = "delivery_error_reason")
    private String deliveryErrorReason;

    @Lob
    @Column(name = "recipient_targets", nullable = false)
    private String recipientTargets;

    @Column(name = "correlation_id", length = 64)
    private String correlationId;

    @Column(name = "created_at", nullable = false)
    private Instant createdAt;

    @PrePersist
    void prePersist() {
        this.createdAt = Instant.now();
    }

    public Long getId() {
        return id;
    }

    public Long getRuleId() {
        return ruleId;
    }

    public void setRuleId(Long ruleId) {
        this.ruleId = ruleId;
    }

    public Long getEmployeeId() {
        return employeeId;
    }

    public void setEmployeeId(Long employeeId) {
        this.employeeId = employeeId;
    }

    public Long getLeaveTypeId() {
        return leaveTypeId;
    }

    public void setLeaveTypeId(Long leaveTypeId) {
        this.leaveTypeId = leaveTypeId;
    }

    public BigDecimal getEvaluatedBalanceValue() {
        return evaluatedBalanceValue;
    }

    public void setEvaluatedBalanceValue(BigDecimal evaluatedBalanceValue) {
        this.evaluatedBalanceValue = evaluatedBalanceValue;
    }

    public Instant getTriggeredAt() {
        return triggeredAt;
    }

    public void setTriggeredAt(Instant triggeredAt) {
        this.triggeredAt = triggeredAt;
    }

    public AlertDeliveryStatus getDeliveryStatus() {
        return deliveryStatus;
    }

    public void setDeliveryStatus(AlertDeliveryStatus deliveryStatus) {
        this.deliveryStatus = deliveryStatus;
    }

    public String getDeliveryErrorReason() {
        return deliveryErrorReason;
    }

    public void setDeliveryErrorReason(String deliveryErrorReason) {
        this.deliveryErrorReason = deliveryErrorReason;
    }

    public String getRecipientTargets() {
        return recipientTargets;
    }

    public void setRecipientTargets(String recipientTargets) {
        this.recipientTargets = recipientTargets;
    }

    public String getCorrelationId() {
        return correlationId;
    }

    public void setCorrelationId(String correlationId) {
        this.correlationId = correlationId;
    }

    public Instant getCreatedAt() {
        return createdAt;
    }
}
