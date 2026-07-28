package com.orangehrm.leavealert.domain.entity;

import com.orangehrm.leavealert.domain.enums.LeaveUnit;
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
import jakarta.persistence.Table;
import jakarta.persistence.UniqueConstraint;
import java.math.BigDecimal;
import java.time.Instant;

@Entity
@Table(
    name = "leave_balance",
    uniqueConstraints = @UniqueConstraint(name = "uk_leave_balance_employee_type_period", columnNames = {"employee_id", "leave_type_id", "period_key"})
)
public class LeaveBalance {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "employee_id", nullable = false)
    private Long employeeId;

    @ManyToOne(fetch = FetchType.LAZY, optional = false)
    @JoinColumn(name = "leave_type_id", nullable = false)
    private LeaveType leaveType;

    @Column(name = "period_key", nullable = false, length = 32)
    private String periodKey;

    @Column(name = "balance_value", nullable = false, precision = 12, scale = 2)
    private BigDecimal balanceValue;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 16)
    private LeaveUnit unit;

    @Column(name = "updated_at", nullable = false)
    private Instant updatedAt = Instant.now();

    protected LeaveBalance() {
    }

    public LeaveBalance(Long employeeId, LeaveType leaveType, String periodKey, BigDecimal balanceValue, LeaveUnit unit) {
        this.employeeId = employeeId;
        this.leaveType = leaveType;
        this.periodKey = periodKey;
        this.balanceValue = balanceValue;
        this.unit = unit;
        this.updatedAt = Instant.now();
    }

    public Long getId() {
        return id;
    }

    public Long getEmployeeId() {
        return employeeId;
    }

    public void setEmployeeId(Long employeeId) {
        this.employeeId = employeeId;
    }

    public LeaveType getLeaveType() {
        return leaveType;
    }

    public void setLeaveType(LeaveType leaveType) {
        this.leaveType = leaveType;
    }

    public String getPeriodKey() {
        return periodKey;
    }

    public void setPeriodKey(String periodKey) {
        this.periodKey = periodKey;
    }

    public BigDecimal getBalanceValue() {
        return balanceValue;
    }

    public void setBalanceValue(BigDecimal balanceValue) {
        this.balanceValue = balanceValue;
        this.updatedAt = Instant.now();
    }

    public LeaveUnit getUnit() {
        return unit;
    }

    public void setUnit(LeaveUnit unit) {
        this.unit = unit;
    }

    public Instant getUpdatedAt() {
        return updatedAt;
    }

    public void setUpdatedAt(Instant updatedAt) {
        this.updatedAt = updatedAt;
    }
}
