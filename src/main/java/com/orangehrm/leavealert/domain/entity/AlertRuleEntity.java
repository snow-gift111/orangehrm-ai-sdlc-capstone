package com.orangehrm.leavealert.domain.entity;

import com.orangehrm.leavealert.domain.ChannelType;
import com.orangehrm.leavealert.domain.ThresholdUnit;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import java.math.BigDecimal;
import java.time.Instant;
import java.util.UUID;

@Entity
@Table(name = "lab_alert_rule")
public class AlertRuleEntity {

    @Id
    @Column(name = "id", nullable = false)
    private UUID id;

    @Column(name = "name", nullable = false, length = 255)
    private String name;

    @Column(name = "threshold_value", nullable = false, precision = 10, scale = 2)
    private BigDecimal thresholdValue;

    @Enumerated(EnumType.STRING)
    @Column(name = "threshold_unit", nullable = false, length = 16)
    private ThresholdUnit thresholdUnit;

    @Column(name = "enabled", nullable = false)
    private boolean enabled;

    @Enumerated(EnumType.STRING)
    @Column(name = "channel", nullable = false, length = 32)
    private ChannelType channel;

    @Column(name = "include_employee", nullable = false)
    private boolean includeEmployee;

    /**
     * Serialized JSON array of role identifiers.
     * Stored as TEXT to keep DB neutral, per design.
     */
    @Column(name = "hr_role_ids", nullable = false, columnDefinition = "TEXT")
    private String hrRoleIds;

    @Column(name = "created_by", nullable = false, length = 128)
    private String createdBy;

    @Column(name = "created_at", nullable = false)
    private Instant createdAt;

    @Column(name = "updated_by", nullable = false, length = 128)
    private String updatedBy;

    @Column(name = "updated_at", nullable = false)
    private Instant updatedAt;

    public UUID getId() {
        return id;
    }

    public void setId(UUID id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public BigDecimal getThresholdValue() {
        return thresholdValue;
    }

    public void setThresholdValue(BigDecimal thresholdValue) {
        this.thresholdValue = thresholdValue;
    }

    public ThresholdUnit getThresholdUnit() {
        return thresholdUnit;
    }

    public void setThresholdUnit(ThresholdUnit thresholdUnit) {
        this.thresholdUnit = thresholdUnit;
    }

    public boolean isEnabled() {
        return enabled;
    }

    public void setEnabled(boolean enabled) {
        this.enabled = enabled;
    }

    public ChannelType getChannel() {
        return channel;
    }

    public void setChannel(ChannelType channel) {
        this.channel = channel;
    }

    public boolean isIncludeEmployee() {
        return includeEmployee;
    }

    public void setIncludeEmployee(boolean includeEmployee) {
        this.includeEmployee = includeEmployee;
    }

    public String getHrRoleIds() {
        return hrRoleIds;
    }

    public void setHrRoleIds(String hrRoleIds) {
        this.hrRoleIds = hrRoleIds;
    }

    public String getCreatedBy() {
        return createdBy;
    }

    public void setCreatedBy(String createdBy) {
        this.createdBy = createdBy;
    }

    public Instant getCreatedAt() {
        return createdAt;
    }

    public void setCreatedAt(Instant createdAt) {
        this.createdAt = createdAt;
    }

    public String getUpdatedBy() {
        return updatedBy;
    }

    public void setUpdatedBy(String updatedBy) {
        this.updatedBy = updatedBy;
    }

    public Instant getUpdatedAt() {
        return updatedAt;
    }

    public void setUpdatedAt(Instant updatedAt) {
        this.updatedAt = updatedAt;
    }
}
