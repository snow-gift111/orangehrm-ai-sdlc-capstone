package com.orangehrm.leave.domain;

import jakarta.persistence.*;

import java.math.BigDecimal;
import java.time.Instant;

@Entity
@Table(name = "leave_balance",
        uniqueConstraints = {
                @UniqueConstraint(name = "uq_leave_balance_employee_type", columnNames = {"employee_id", "leave_type_id"})
        })
public class LeaveBalanceEntity {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(name = "employee_id", nullable = false)
    private Long employeeId;

    @Column(name = "leave_type_id", nullable = false)
    private Long leaveTypeId;

    @Column(name = "balance_value", nullable = false, precision = 10, scale = 2)
    private BigDecimal balanceValue;

    @Column(name = "last_updated_at", nullable = false)
    private Instant lastUpdatedAt;

    @Column(name = "updated_by_user_id")
    private Long updatedByUserId;

    @Column(name = "created_at", nullable = false)
    private Instant createdAt;

    @PrePersist
    void prePersist() {
        this.createdAt = Instant.now();
        if (this.lastUpdatedAt == null) {
            this.lastUpdatedAt = this.createdAt;
        }
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

    public Long getLeaveTypeId() {
        return leaveTypeId;
    }

    public void setLeaveTypeId(Long leaveTypeId) {
        this.leaveTypeId = leaveTypeId;
    }

    public BigDecimal getBalanceValue() {
        return balanceValue;
    }

    public void setBalanceValue(BigDecimal balanceValue) {
        this.balanceValue = balanceValue;
    }

    public Instant getLastUpdatedAt() {
        return lastUpdatedAt;
    }

    public void setLastUpdatedAt(Instant lastUpdatedAt) {
        this.lastUpdatedAt = lastUpdatedAt;
    }

    public Long getUpdatedByUserId() {
        return updatedByUserId;
    }

    public void setUpdatedByUserId(Long updatedByUserId) {
        this.updatedByUserId = updatedByUserId;
    }

    public Instant getCreatedAt() {
        return createdAt;
    }
}
