package com.orangehrm.leavealert.entity;

import com.orangehrm.leavealert.domain.ConditionType;
import com.orangehrm.leavealert.domain.DeliveryStatus;
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
import java.math.BigDecimal;
import java.time.Instant;

@Entity
@Table(name = "leave_balance_alert_event")
public class LeaveBalanceAlertEvent {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY, optional = false)
    @JoinColumn(name = "policy_id", nullable = false)
    private LeaveBalanceAlertPolicy policy;

    @Column(name = "subject_employee_id", nullable = false, length = 64)
    private String subjectEmployeeId;

    @Column(name = "subject_employee_user_id", length = 128)
    private String subjectEmployeeUserId;

    @Column(name = "subject_employee_display_name", length = 255)
    private String subjectEmployeeDisplayName;

    @Column(name = "leave_type_id", nullable = false, length = 64)
    private String leaveTypeId;

    @Column(name = "leave_type_name", length = 160)
    private String leaveTypeName;

    @Enumerated(EnumType.STRING)
    @Column(name = "triggered_condition_type", nullable = false, length = 32)
    private ConditionType triggeredConditionType;

    @Column(name = "balance_at_trigger", nullable = false, precision = 12, scale = 4)
    private BigDecimal balanceAtTrigger;

    @Column(name = "threshold_value", precision = 12, scale = 4)
    private BigDecimal thresholdValue;

    @Column(name = "dedup_key", nullable = false, unique = true, length = 255)
    private String dedupKey;

    @Column(name = "generated_at", nullable = false)
    private Instant generatedAt;

    @Column(name = "sent_at")
    private Instant sentAt;

    @Enumerated(EnumType.STRING)
    @Column(name = "delivery_status", nullable = false, length = 32)
    private DeliveryStatus deliveryStatus;

    @Column(name = "failure_reason", length = 512)
    private String failureReason;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public LeaveBalanceAlertPolicy getPolicy() { return policy; }
    public void setPolicy(LeaveBalanceAlertPolicy policy) { this.policy = policy; }
    public String getSubjectEmployeeId() { return subjectEmployeeId; }
    public void setSubjectEmployeeId(String subjectEmployeeId) { this.subjectEmployeeId = subjectEmployeeId; }
    public String getSubjectEmployeeUserId() { return subjectEmployeeUserId; }
    public void setSubjectEmployeeUserId(String subjectEmployeeUserId) { this.subjectEmployeeUserId = subjectEmployeeUserId; }
    public String getSubjectEmployeeDisplayName() { return subjectEmployeeDisplayName; }
    public void setSubjectEmployeeDisplayName(String subjectEmployeeDisplayName) { this.subjectEmployeeDisplayName = subjectEmployeeDisplayName; }
    public String getLeaveTypeId() { return leaveTypeId; }
    public void setLeaveTypeId(String leaveTypeId) { this.leaveTypeId = leaveTypeId; }
    public String getLeaveTypeName() { return leaveTypeName; }
    public void setLeaveTypeName(String leaveTypeName) { this.leaveTypeName = leaveTypeName; }
    public ConditionType getTriggeredConditionType() { return triggeredConditionType; }
    public void setTriggeredConditionType(ConditionType triggeredConditionType) { this.triggeredConditionType = triggeredConditionType; }
    public BigDecimal getBalanceAtTrigger() { return balanceAtTrigger; }
    public void setBalanceAtTrigger(BigDecimal balanceAtTrigger) { this.balanceAtTrigger = balanceAtTrigger; }
    public BigDecimal getThresholdValue() { return thresholdValue; }
    public void setThresholdValue(BigDecimal thresholdValue) { this.thresholdValue = thresholdValue; }
    public String getDedupKey() { return dedupKey; }
    public void setDedupKey(String dedupKey) { this.dedupKey = dedupKey; }
    public Instant getGeneratedAt() { return generatedAt; }
    public void setGeneratedAt(Instant generatedAt) { this.generatedAt = generatedAt; }
    public Instant getSentAt() { return sentAt; }
    public void setSentAt(Instant sentAt) { this.sentAt = sentAt; }
    public DeliveryStatus getDeliveryStatus() { return deliveryStatus; }
    public void setDeliveryStatus(DeliveryStatus deliveryStatus) { this.deliveryStatus = deliveryStatus; }
    public String getFailureReason() { return failureReason; }
    public void setFailureReason(String failureReason) { this.failureReason = failureReason; }
}
