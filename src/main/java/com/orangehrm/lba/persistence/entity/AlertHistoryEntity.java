package com.orangehrm.lba.persistence.entity;

import com.orangehrm.lba.domain.model.AckStatus;
import com.orangehrm.lba.domain.model.DeliveryStatus;
import com.orangehrm.lba.domain.model.ThresholdCondition;
import jakarta.persistence.*;
import java.math.BigDecimal;
import java.time.Instant;
import java.time.LocalDate;

@Entity
@Table(
    name = "lba_alert_history",
    uniqueConstraints = {
      @UniqueConstraint(name = "uk_lba_alert_history_dedup_suppression", columnNames = {"dedup_key", "suppression_window_start"})
    })
public class AlertHistoryEntity {

  @Id
  @GeneratedValue(strategy = GenerationType.IDENTITY)
  private Long id;

  @ManyToOne(fetch = FetchType.LAZY)
  @JoinColumn(name = "rule_id", nullable = false)
  private AlertRuleEntity rule;

  @Column(name = "rule_name_snapshot", nullable = false, length = 128)
  private String ruleNameSnapshot;

  @Column(name = "employee_id", nullable = false)
  private Long employeeId;

  @Column(name = "leave_type_id", nullable = false)
  private Long leaveTypeId;

  @Column(name = "balance_value_snapshot", nullable = false, precision = 10, scale = 2)
  private BigDecimal balanceValueSnapshot;

  @Enumerated(EnumType.STRING)
  @Column(name = "threshold_condition_snapshot", nullable = false, length = 8)
  private ThresholdCondition thresholdConditionSnapshot;

  @Column(name = "threshold_value_snapshot", nullable = false, precision = 10, scale = 2)
  private BigDecimal thresholdValueSnapshot;

  @Column(name = "threshold_breached_text", nullable = false, length = 256)
  private String thresholdBreachedText;

  @Column(name = "generated_at", nullable = false)
  private Instant generatedAt;

  @Column(name = "dedup_key", nullable = false, length = 256)
  private String dedupKey;

  @Column(name = "suppression_window_start", nullable = false)
  private LocalDate suppressionWindowStart;

  @Enumerated(EnumType.STRING)
  @Column(name = "delivery_status", nullable = false, length = 16)
  private DeliveryStatus deliveryStatus;

  @Enumerated(EnumType.STRING)
  @Column(name = "ack_status", nullable = false, length = 16)
  private AckStatus ackStatus;

  public Long getId() {
    return id;
  }

  public void setId(Long id) {
    this.id = id;
  }

  public AlertRuleEntity getRule() {
    return rule;
  }

  public void setRule(AlertRuleEntity rule) {
    this.rule = rule;
  }

  public String getRuleNameSnapshot() {
    return ruleNameSnapshot;
  }

  public void setRuleNameSnapshot(String ruleNameSnapshot) {
    this.ruleNameSnapshot = ruleNameSnapshot;
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

  public BigDecimal getBalanceValueSnapshot() {
    return balanceValueSnapshot;
  }

  public void setBalanceValueSnapshot(BigDecimal balanceValueSnapshot) {
    this.balanceValueSnapshot = balanceValueSnapshot;
  }

  public ThresholdCondition getThresholdConditionSnapshot() {
    return thresholdConditionSnapshot;
  }

  public void setThresholdConditionSnapshot(ThresholdCondition thresholdConditionSnapshot) {
    this.thresholdConditionSnapshot = thresholdConditionSnapshot;
  }

  public BigDecimal getThresholdValueSnapshot() {
    return thresholdValueSnapshot;
  }

  public void setThresholdValueSnapshot(BigDecimal thresholdValueSnapshot) {
    this.thresholdValueSnapshot = thresholdValueSnapshot;
  }

  public String getThresholdBreachedText() {
    return thresholdBreachedText;
  }

  public void setThresholdBreachedText(String thresholdBreachedText) {
    this.thresholdBreachedText = thresholdBreachedText;
  }

  public Instant getGeneratedAt() {
    return generatedAt;
  }

  public void setGeneratedAt(Instant generatedAt) {
    this.generatedAt = generatedAt;
  }

  public String getDedupKey() {
    return dedupKey;
  }

  public void setDedupKey(String dedupKey) {
    this.dedupKey = dedupKey;
  }

  public LocalDate getSuppressionWindowStart() {
    return suppressionWindowStart;
  }

  public void setSuppressionWindowStart(LocalDate suppressionWindowStart) {
    this.suppressionWindowStart = suppressionWindowStart;
  }

  public DeliveryStatus getDeliveryStatus() {
    return deliveryStatus;
  }

  public void setDeliveryStatus(DeliveryStatus deliveryStatus) {
    this.deliveryStatus = deliveryStatus;
  }

  public AckStatus getAckStatus() {
    return ackStatus;
  }

  public void setAckStatus(AckStatus ackStatus) {
    this.ackStatus = ackStatus;
  }
}
