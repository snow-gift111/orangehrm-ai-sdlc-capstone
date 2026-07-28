package com.orangehrm.leavealert.entity;

import com.orangehrm.leavealert.domain.EvaluationStateType;
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
import java.time.Instant;

@Entity
@Table(
        name = "leave_balance_alert_eval_state",
        uniqueConstraints = @UniqueConstraint(
                name = "uq_lba_eval_state",
                columnNames = {"policy_id", "subject_employee_id", "leave_type_id"}))
public class LeaveBalanceAlertEvaluationState {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY, optional = false)
    @JoinColumn(name = "policy_id", nullable = false)
    private LeaveBalanceAlertPolicy policy;

    @Column(name = "subject_employee_id", nullable = false, length = 64)
    private String subjectEmployeeId;

    @Column(name = "leave_type_id", nullable = false, length = 64)
    private String leaveTypeId;

    @Enumerated(EnumType.STRING)
    @Column(name = "last_state", nullable = false, length = 32)
    private EvaluationStateType lastState;

    @Column(name = "last_evaluated_at", nullable = false)
    private Instant lastEvaluatedAt;

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public LeaveBalanceAlertPolicy getPolicy() {
        return policy;
    }

    public void setPolicy(LeaveBalanceAlertPolicy policy) {
        this.policy = policy;
    }

    public String getSubjectEmployeeId() {
        return subjectEmployeeId;
    }

    public void setSubjectEmployeeId(String subjectEmployeeId) {
        this.subjectEmployeeId = subjectEmployeeId;
    }

    public String getLeaveTypeId() {
        return leaveTypeId;
    }

    public void setLeaveTypeId(String leaveTypeId) {
        this.leaveTypeId = leaveTypeId;
    }

    public EvaluationStateType getLastState() {
        return lastState;
    }

    public void setLastState(EvaluationStateType lastState) {
        this.lastState = lastState;
    }

    public Instant getLastEvaluatedAt() {
        return lastEvaluatedAt;
    }

    public void setLastEvaluatedAt(Instant lastEvaluatedAt) {
        this.lastEvaluatedAt = lastEvaluatedAt;
    }
}
