package com.orangehrm.lba.api.dto;

import java.math.BigDecimal;
import java.time.Instant;

public class AlertNotificationItem {
  private Long alertId;
  private String ruleName;
  private EmployeeRef employee;
  private LeaveTypeRef leaveType;
  private BigDecimal balance;
  private String threshold;
  private Instant generatedAt;

  public Long getAlertId() {
    return alertId;
  }

  public void setAlertId(Long alertId) {
    this.alertId = alertId;
  }

  public String getRuleName() {
    return ruleName;
  }

  public void setRuleName(String ruleName) {
    this.ruleName = ruleName;
  }

  public EmployeeRef getEmployee() {
    return employee;
  }

  public void setEmployee(EmployeeRef employee) {
    this.employee = employee;
  }

  public LeaveTypeRef getLeaveType() {
    return leaveType;
  }

  public void setLeaveType(LeaveTypeRef leaveType) {
    this.leaveType = leaveType;
  }

  public BigDecimal getBalance() {
    return balance;
  }

  public void setBalance(BigDecimal balance) {
    this.balance = balance;
  }

  public String getThreshold() {
    return threshold;
  }

  public void setThreshold(String threshold) {
    this.threshold = threshold;
  }

  public Instant getGeneratedAt() {
    return generatedAt;
  }

  public void setGeneratedAt(Instant generatedAt) {
    this.generatedAt = generatedAt;
  }

  public static class EmployeeRef {
    private Long id;
    private String displayName;

    public EmployeeRef() {}

    public EmployeeRef(Long id, String displayName) {
      this.id = id;
      this.displayName = displayName;
    }

    public Long getId() {
      return id;
    }

    public void setId(Long id) {
      this.id = id;
    }

    public String getDisplayName() {
      return displayName;
    }

    public void setDisplayName(String displayName) {
      this.displayName = displayName;
    }
  }

  public static class LeaveTypeRef {
    private Long id;
    private String name;

    public LeaveTypeRef() {}

    public LeaveTypeRef(Long id, String name) {
      this.id = id;
      this.name = name;
    }

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
  }
}
