package com.orangehrm.leave.domain;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;
import java.math.BigDecimal;
import java.time.Instant;
import lombok.Getter;
import lombok.Setter;

@Entity
@Table(name = "leave_alert_event")
@Getter
@Setter
public class LeaveAlertEvent {
  @Id
  @GeneratedValue(strategy = GenerationType.IDENTITY)
  private Long id;

  @ManyToOne(fetch = FetchType.LAZY)
  @JoinColumn(name = "rule_id", nullable = false)
  private LeaveAlertRule rule;

  @Column(name = "employee_id", nullable = false)
  private Long employeeId;

  @ManyToOne(fetch = FetchType.LAZY)
  @JoinColumn(name = "leave_type_id", nullable = false)
  private LeaveType leaveType;

  @Column(name = "current_balance", nullable = false, precision = 10, scale = 2)
  private BigDecimal currentBalance;

  @Column(name = "threshold_breached", nullable = false, columnDefinition = "TEXT")
  private String thresholdBreached;

  @Column(name = "recommended_action", nullable = false, columnDefinition = "TEXT")
  private String recommendedAction;

  @Column(name = "generated_at", nullable = false)
  private Instant generatedAt;
}
