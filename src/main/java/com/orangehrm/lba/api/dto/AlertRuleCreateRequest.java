package com.orangehrm.lba.api.dto;

import com.orangehrm.lba.domain.model.ChannelType;
import com.orangehrm.lba.domain.model.ThresholdCondition;
import jakarta.validation.Valid;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotEmpty;
import jakarta.validation.constraints.NotNull;
import jakarta.validation.constraints.PositiveOrZero;
import java.math.BigDecimal;
import java.util.List;

public class AlertRuleCreateRequest {

  @NotBlank private String name;
  private String description;

  @NotEmpty private List<@NotNull Long> leaveTypeIds;

  @NotNull private ThresholdCondition thresholdCondition;

  @NotNull
  @PositiveOrZero
  private BigDecimal thresholdValue;

  private Integer suppressionWindowDays;

  @NotEmpty private List<@Valid RecipientDto> recipients;

  @NotEmpty private List<@NotNull ChannelType> channels;

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

  public List<Long> getLeaveTypeIds() {
    return leaveTypeIds;
  }

  public void setLeaveTypeIds(List<Long> leaveTypeIds) {
    this.leaveTypeIds = leaveTypeIds;
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

  public Integer getSuppressionWindowDays() {
    return suppressionWindowDays;
  }

  public void setSuppressionWindowDays(Integer suppressionWindowDays) {
    this.suppressionWindowDays = suppressionWindowDays;
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
}
