package com.orangehrm.leave.domain;

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
import jakarta.persistence.OneToMany;
import jakarta.persistence.Table;
import java.math.BigDecimal;
import java.time.Instant;
import java.util.HashSet;
import java.util.Set;
import lombok.Getter;
import lombok.Setter;

@Entity
@Table(name = "leave_alert_rule")
@Getter
@Setter
public class LeaveAlertRule {
  @Id
  @GeneratedValue(strategy = GenerationType.IDENTITY)
  private Long id;

  @Enumerated(EnumType.STRING)
  @Column(name = "scope_type", nullable = false)
  private RuleScopeType scopeType;

  @ManyToOne(fetch = FetchType.LAZY)
  @JoinColumn(name = "leave_type_id")
  private LeaveType leaveType; // nullable when scopeType=ALL

  @Enumerated(EnumType.STRING)
  @Column(name = "threshold_operator", nullable = false)
  private ThresholdOperator thresholdOperator;

  @Column(name = "threshold_value", nullable = false, precision = 10, scale = 2)
  private BigDecimal thresholdValue;

  @Enumerated(EnumType.STRING)
  @Column(name = "frequency", nullable = false)
  private RuleFrequency frequency;

  @Column(nullable = false)
  private boolean active = true;

  @Column(name = "created_by_user_id", nullable = false)
  private Long createdByUserId;

  @Column(name = "created_at", nullable = false)
  private Instant createdAt;

  @Column(name = "updated_at", nullable = false)
  private Instant updatedAt;

  @OneToMany(mappedBy = "rule", fetch = FetchType.LAZY)
  private Set<LeaveAlertRuleRecipient> recipients = new HashSet<>();
}
