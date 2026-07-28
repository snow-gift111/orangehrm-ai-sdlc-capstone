package com.orangehrm.leavealert.entity;

import com.orangehrm.leavealert.domain.ConditionType;
import com.orangehrm.leavealert.domain.Frequency;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import java.math.BigDecimal;
import java.time.Instant;
import org.springframework.data.annotation.CreatedBy;
import org.springframework.data.annotation.CreatedDate;
import org.springframework.data.annotation.LastModifiedBy;
import org.springframework.data.annotation.LastModifiedDate;
import org.springframework.data.jpa.domain.support.AuditingEntityListener;
import jakarta.persistence.EntityListeners;

@Entity
@Table(name = "leave_balance_alert_policy")
@EntityListeners(AuditingEntityListener.class)
public class LeaveBalanceAlertPolicy {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 160)
    private String name;

    @Column(name = "leave_type_id", nullable = false, length = 64)
    private String leaveTypeId;

    @Enumerated(EnumType.STRING)
    @Column(name = "condition_type", nullable = false, length = 32)
    private ConditionType conditionType;

    @Column(name = "threshold_value", precision = 12, scale = 4)
    private BigDecimal thresholdValue;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 48)
    private Frequency frequency;

    @Column(name = "effective_from")
    private Instant effectiveFrom;

    @Column(name = "effective_to")
    private Instant effectiveTo;

    @Column(nullable = false)
    private boolean active;

    @Column(name = "recipients_employee", nullable = false)
    private boolean recipientsEmployee;

    @Column(name = "recipients_manager", nullable = false)
    private boolean recipientsManager;

    @Column(name = "recipients_hr_role", nullable = false)
    private boolean recipientsHrRole;

    @Column(name = "channel_in_app", nullable = false)
    private boolean channelInApp = true;

    @CreatedDate
    @Column(name = "created_at", nullable = false, updatable = false)
    private Instant createdAt;

    @CreatedBy
    @Column(name = "created_by", nullable = false, updatable = false, length = 128)
    private String createdBy;

    @LastModifiedDate
    @Column(name = "updated_at", nullable = false)
    private Instant updatedAt;

    @LastModifiedBy
    @Column(name = "updated_by", nullable = false, length = 128)
    private String updatedBy;

    public Long getId() { return id; }
    public void setId(Long id) { this.id = id; }
    public String getName() { return name; }
    public void setName(String name) { this.name = name; }
    public String getLeaveTypeId() { return leaveTypeId; }
    public void setLeaveTypeId(String leaveTypeId) { this.leaveTypeId = leaveTypeId; }
    public ConditionType getConditionType() { return conditionType; }
    public void setConditionType(ConditionType conditionType) { this.conditionType = conditionType; }
    public BigDecimal getThresholdValue() { return thresholdValue; }
    public void setThresholdValue(BigDecimal thresholdValue) { this.thresholdValue = thresholdValue; }
    public Frequency getFrequency() { return frequency; }
    public void setFrequency(Frequency frequency) { this.frequency = frequency; }
    public Instant getEffectiveFrom() { return effectiveFrom; }
    public void setEffectiveFrom(Instant effectiveFrom) { this.effectiveFrom = effectiveFrom; }
    public Instant getEffectiveTo() { return effectiveTo; }
    public void setEffectiveTo(Instant effectiveTo) { this.effectiveTo = effectiveTo; }
    public boolean isActive() { return active; }
    public void setActive(boolean active) { this.active = active; }
    public boolean isRecipientsEmployee() { return recipientsEmployee; }
    public void setRecipientsEmployee(boolean recipientsEmployee) { this.recipientsEmployee = recipientsEmployee; }
    public boolean isRecipientsManager() { return recipientsManager; }
    public void setRecipientsManager(boolean recipientsManager) { this.recipientsManager = recipientsManager; }
    public boolean isRecipientsHrRole() { return recipientsHrRole; }
    public void setRecipientsHrRole(boolean recipientsHrRole) { this.recipientsHrRole = recipientsHrRole; }
    public boolean isChannelInApp() { return channelInApp; }
    public void setChannelInApp(boolean channelInApp) { this.channelInApp = channelInApp; }
    public Instant getCreatedAt() { return createdAt; }
    public String getCreatedBy() { return createdBy; }
    public Instant getUpdatedAt() { return updatedAt; }
    public String getUpdatedBy() { return updatedBy; }
}
