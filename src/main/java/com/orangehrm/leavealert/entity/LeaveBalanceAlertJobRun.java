package com.orangehrm.leavealert.entity;

import com.orangehrm.leavealert.domain.JobRunStatus;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import java.time.Instant;
import java.util.UUID;

@Entity
@Table(name = "leave_balance_alert_job_run")
public class LeaveBalanceAlertJobRun {

    @Id
    private UUID id;

    @Column(name = "started_at", nullable = false)
    private Instant startedAt;

    @Column(name = "ended_at")
    private Instant endedAt;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 32)
    private JobRunStatus status;

    @Column(name = "processed_policies_count", nullable = false)
    private int processedPoliciesCount;

    @Column(name = "processed_employees_count", nullable = false)
    private int processedEmployeesCount;

    @Column(name = "alerts_generated_count", nullable = false)
    private int alertsGeneratedCount;

    @Column(name = "error_summary")
    private String errorSummary;

    public UUID getId() {
        return id;
    }

    public void setId(UUID id) {
        this.id = id;
    }

    public Instant getStartedAt() {
        return startedAt;
    }

    public void setStartedAt(Instant startedAt) {
        this.startedAt = startedAt;
    }

    public Instant getEndedAt() {
        return endedAt;
    }

    public void setEndedAt(Instant endedAt) {
        this.endedAt = endedAt;
    }

    public JobRunStatus getStatus() {
        return status;
    }

    public void setStatus(JobRunStatus status) {
        this.status = status;
    }

    public int getProcessedPoliciesCount() {
        return processedPoliciesCount;
    }

    public void setProcessedPoliciesCount(int processedPoliciesCount) {
        this.processedPoliciesCount = processedPoliciesCount;
    }

    public int getProcessedEmployeesCount() {
        return processedEmployeesCount;
    }

    public void setProcessedEmployeesCount(int processedEmployeesCount) {
        this.processedEmployeesCount = processedEmployeesCount;
    }

    public int getAlertsGeneratedCount() {
        return alertsGeneratedCount;
    }

    public void setAlertsGeneratedCount(int alertsGeneratedCount) {
        this.alertsGeneratedCount = alertsGeneratedCount;
    }

    public String getErrorSummary() {
        return errorSummary;
    }

    public void setErrorSummary(String errorSummary) {
        this.errorSummary = errorSummary;
    }
}
