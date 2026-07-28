package com.orangehrm.lba.persistence.entity;

import com.orangehrm.lba.domain.model.AlertRuleStatus;
import com.orangehrm.lba.domain.model.ThresholdCondition;
import jakarta.persistence.*;
import java.math.BigDecimal;
import java.time.Instant;
import java.util.HashSet;
import java.util.Set;

@Entity
@Table(name = "lba_alert_rule")
public class AlertRuleEntity {

  @Id
  @GeneratedValue(strategy = GenerationType.IDENTITY)
  private Long id;

  @Column(name = "name", nullable = false, length = 128, unique = true)
  private String name;

  @Column(name = "description", length = 512)
  private String description;

  @Enumerated(EnumType.STRING)
  @Column(name = "status", nullable = false, length = 16)
  private AlertRuleStatus status;

  @Enumerated(EnumType.STRING)
  @Column(name = "threshold_condition", nullable = false, length = 8)
  private ThresholdCondition thresholdCondition;

  @Column(name = "threshold_value", nullable = false, precision = 10, scale = 2)
  private BigDecimal thresholdValue;

  @Column(name = "suppression_window_days", nullable = false)
  private int suppressionWindowDays;

  @Column(name = "created_by", nullable = false, length = 64)
  private String createdBy;

  @Column(name = "created_at", nullable = false)
  private Instant createdAt;

  @Column(name = "updated_by", nullable = false, length = 64)
  private String updatedBy;

  @Column(name = "updated_at", nullable = false)
  private Instant updatedAt;

  @OneToMany(mappedBy = "rule", cascade = CascadeType.ALL, orphanRemoval = true, fetch = FetchType.LAZY)
  private Set<AlertRuleLeaveTypeEntity> leaveTypes = new HashSet<>();

  @OneToMany(mappedBy = "rule", cascade = CascadeType.ALL, orphanRemoval = true, fetch = FetchType.LAZY)
  private Set<AlertRuleRecipientEntity> recipients = new HashSet<>();

  @OneToMany(mappedBy = "rule", cascade = CascadeType.ALL, orphanRemoval = true, fetch = FetchType.LAZY)
  private Set<AlertRuleChannelEntity> channels = new HashSet<>();

  public Long getId() {
    return id;
  }

  public void setId(Long id) {
    this.id = id;
  }

  public String getName() {
    return name;
  }

  public void setName(String name) {
    this.name = name;
  }

  public String getDescription() {
    return description;
  }

  public void setDescription(String description) {
    this.description = description;
  }

  public AlertRuleStatus getStatus() {
    return status;
  }

  public void setStatus(AlertRuleStatus status) {
    this.status = status;
  }

  public ThresholdCondition getThresholdCondition() {
    return thresholdCondition;
  }

  public void setThresholdCondition(ThresholdCondition thresholdCondition) {
    this.thresholdCondition = thresholdCondition;
  }

  public BigDecimal getThresholdValue() {
    return thresholdValue;
  }

  public void setThresholdValue(BigDecimal thresholdValue) {
    this.thresholdValue = thresholdValue;
  }

  public int getSuppressionWindowDays() {
    return suppressionWindowDays;
  }

  public void setSuppressionWindowDays(int suppressionWindowDays) {
    this.suppressionWindowDays = suppressionWindowDays;
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

  public Set<AlertRuleLeaveTypeEntity> getLeaveTypes() {
    return leaveTypes;
  }

  public void setLeaveTypes(Set<AlertRuleLeaveTypeEntity> leaveTypes) {
    this.leaveTypes = leaveTypes;
  }

  public Set<AlertRuleRecipientEntity> getRecipients() {
    return recipients;
  }

  public void setRecipients(Set<AlertRuleRecipientEntity> recipients) {
    this.recipients = recipients;
  }

  public Set<AlertRuleChannelEntity> getChannels() {
    return channels;
  }

  public void setChannels(Set<AlertRuleChannelEntity> channels) {
    this.channels = channels;
  }
}
