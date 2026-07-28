package com.orangehrm.leave.domain;

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
import java.time.LocalDate;
import lombok.Getter;
import lombok.Setter;

@Entity
@Table(name = "leave_balance_adjustment")
@Getter
@Setter
public class LeaveBalanceAdjustment {
  @Id
  @GeneratedValue(strategy = GenerationType.IDENTITY)
  private Long id;

  @Column(name = "employee_id", nullable = false)
  private Long employeeId;

  @Column(name = "leave_type_id", nullable = false)
  private Long leaveTypeId;

  @Enumerated(EnumType.STRING)
  @Column(name = "adjustment_kind", nullable = false)
  private AdjustmentKind adjustmentKind;

  @Column(precision = 10, scale = 2)
  private BigDecimal delta;

  @Column(name = "new_balance", precision = 10, scale = 2)
  private BigDecimal newBalance;

  @Column(nullable = false, columnDefinition = "TEXT")
  private String reason;

  @Column(name = "effective_date", nullable = false)
  private LocalDate effectiveDate;

  @Column(name = "created_by_user_id", nullable = false)
  private Long createdByUserId;

  @Column(name = "created_at", nullable = false)
  private Instant createdAt;
}
