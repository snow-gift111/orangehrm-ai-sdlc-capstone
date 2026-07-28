package com.orangehrm.lba.api.dto;

import com.orangehrm.lba.domain.model.AlertRuleStatus;
import com.orangehrm.lba.domain.model.ChannelType;
import com.orangehrm.lba.domain.model.ThresholdCondition;
import java.math.BigDecimal;
import java.time.Instant;
import java.util.List;

public class AlertRuleResponse {
  private Long id;
  private String name;
  private String description;
  private AlertRuleStatus status;
  private ThresholdCondition thresholdCondition;
  private BigDecimal thresholdValue;
  private int suppressionWindowDays;
  private List<Long> leaveTypeIds;
  private List<RecipientDto> recipients;
  private List<ChannelType> channels;
  private Instant createdAt;
  private Instant updatedAt;

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

  public List<Long> getLeaveTypeIds() {
    return leaveTypeIds;
  }

  public void setLeaveTypeIds(List<Long> leaveTypeIds) {
    this.leaveTypeIds = leaveTypeIds;
  }

  public List<RecipientDto> getRecipients() {
    return recipients;
  }

  public void setRecipients(List<RecipientDto> recipients) {
    this.recipients = recipients;
  }

  public List<ChannelType> getChannels() {
    return channels;
  }

  public void setChannels(List<ChannelType> channels) {
    this.channels = channels;
  }

  public Instant getCreatedAt() {
    return createdAt;
  }

  public void setCreatedAt(Instant createdAt) {
    this.createdAt = createdAt;
  }

  public Instant getUpdatedAt() {
    return updatedAt;
  }

  public void setUpdatedAt(Instant updatedAt) {
    this.updatedAt = updatedAt;
  }
}
