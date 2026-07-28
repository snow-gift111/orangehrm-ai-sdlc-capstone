CREATE TABLE leave_balance_alert_policy (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    leave_type_id VARCHAR(64) NOT NULL,
    condition_type VARCHAR(32) NOT NULL,
    threshold_value NUMERIC(12, 4),
    frequency VARCHAR(48) NOT NULL,
    effective_from TIMESTAMPTZ,
    effective_to TIMESTAMPTZ,
    active BOOLEAN NOT NULL DEFAULT FALSE,
    recipients_employee BOOLEAN NOT NULL DEFAULT FALSE,
    recipients_manager BOOLEAN NOT NULL DEFAULT FALSE,
    recipients_hr_role BOOLEAN NOT NULL DEFAULT FALSE,
    channel_in_app BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL,
    created_by VARCHAR(128) NOT NULL,
    updated_at TIMESTAMPTZ NOT NULL,
    updated_by VARCHAR(128) NOT NULL,
    CONSTRAINT chk_lba_policy_condition_type CHECK (condition_type IN ('BELOW_OR_EQUAL', 'ZERO', 'NEGATIVE')),
    CONSTRAINT chk_lba_policy_frequency CHECK (frequency IN ('ONCE_PER_DAY', 'WEEKLY_REMINDER', 'ONCE_PER_THRESHOLD_CROSSING')),
    CONSTRAINT chk_lba_policy_threshold CHECK (condition_type <> 'BELOW_OR_EQUAL' OR threshold_value IS NOT NULL),
    CONSTRAINT chk_lba_policy_effective_range CHECK (effective_from IS NULL OR effective_to IS NULL OR effective_from <= effective_to),
    CONSTRAINT chk_lba_policy_recipient CHECK (recipients_employee OR recipients_manager OR recipients_hr_role),
    CONSTRAINT chk_lba_policy_in_app CHECK (channel_in_app = TRUE)
);

CREATE INDEX idx_lba_policy_active_effective ON leave_balance_alert_policy (active, effective_from, effective_to);
CREATE INDEX idx_lba_policy_leave_type_active ON leave_balance_alert_policy (leave_type_id, active);

CREATE TABLE leave_balance_alert_event (
    id BIGSERIAL PRIMARY KEY,
    policy_id BIGINT NOT NULL REFERENCES leave_balance_alert_policy (id),
    subject_employee_id VARCHAR(64) NOT NULL,
    subject_employee_user_id VARCHAR(128),
    subject_employee_display_name VARCHAR(255),
    leave_type_id VARCHAR(64) NOT NULL,
    leave_type_name VARCHAR(160),
    triggered_condition_type VARCHAR(32) NOT NULL,
    balance_at_trigger NUMERIC(12, 4) NOT NULL,
    threshold_value NUMERIC(12, 4),
    dedup_key VARCHAR(255) NOT NULL,
    generated_at TIMESTAMPTZ NOT NULL,
    sent_at TIMESTAMPTZ,
    delivery_status VARCHAR(32) NOT NULL,
    failure_reason VARCHAR(512),
    CONSTRAINT chk_lba_event_condition_type CHECK (triggered_condition_type IN ('BELOW_OR_EQUAL', 'ZERO', 'NEGATIVE')),
    CONSTRAINT chk_lba_event_delivery_status CHECK (delivery_status IN ('GENERATED', 'IN_APP_DELIVERED', 'FAILED')),
    CONSTRAINT uq_lba_event_dedup_key UNIQUE (dedup_key)
);

CREATE INDEX idx_lba_event_subject_generated ON leave_balance_alert_event (subject_employee_id, generated_at DESC);
CREATE INDEX idx_lba_event_policy_generated ON leave_balance_alert_event (policy_id, generated_at DESC);
CREATE INDEX idx_lba_event_status_generated ON leave_balance_alert_event (delivery_status, generated_at DESC);
CREATE INDEX idx_lba_event_leave_type_generated ON leave_balance_alert_event (leave_type_id, generated_at DESC);

CREATE TABLE leave_balance_alert_recipient (
    id BIGSERIAL PRIMARY KEY,
    alert_event_id BIGINT NOT NULL REFERENCES leave_balance_alert_event (id) ON DELETE CASCADE,
    recipient_user_id VARCHAR(128) NOT NULL,
    recipient_type VARCHAR(32) NOT NULL,
    visible_from TIMESTAMPTZ NOT NULL,
    CONSTRAINT chk_lba_recipient_type CHECK (recipient_type IN ('EMPLOYEE', 'MANAGER', 'HR')),
    CONSTRAINT uq_lba_recipient_event UNIQUE (recipient_user_id, alert_event_id)
);

CREATE INDEX idx_lba_recipient_user_visible ON leave_balance_alert_recipient (recipient_user_id, visible_from DESC);
CREATE INDEX idx_lba_recipient_event ON leave_balance_alert_recipient (alert_event_id);

CREATE TABLE leave_balance_alert_eval_state (
    id BIGSERIAL PRIMARY KEY,
    policy_id BIGINT NOT NULL REFERENCES leave_balance_alert_policy (id) ON DELETE CASCADE,
    subject_employee_id VARCHAR(64) NOT NULL,
    leave_type_id VARCHAR(64) NOT NULL,
    last_state VARCHAR(32) NOT NULL,
    last_evaluated_at TIMESTAMPTZ NOT NULL,
    CONSTRAINT chk_lba_eval_state CHECK (last_state IN ('OK', 'BELOW_OR_EQUAL', 'ZERO', 'NEGATIVE')),
    CONSTRAINT uq_lba_eval_state UNIQUE (policy_id, subject_employee_id, leave_type_id)
);

CREATE INDEX idx_lba_eval_state_policy_evaluated ON leave_balance_alert_eval_state (policy_id, last_evaluated_at DESC);

CREATE TABLE leave_balance_alert_job_run (
    id UUID PRIMARY KEY,
    started_at TIMESTAMPTZ NOT NULL,
    ended_at TIMESTAMPTZ,
    status VARCHAR(32) NOT NULL,
    processed_policies_count INTEGER NOT NULL DEFAULT 0,
    processed_employees_count INTEGER NOT NULL DEFAULT 0,
    alerts_generated_count INTEGER NOT NULL DEFAULT 0,
    error_summary TEXT,
    CONSTRAINT chk_lba_job_status CHECK (status IN ('RUNNING', 'SUCCESS', 'PARTIAL_FAILURE', 'FAILED'))
);

CREATE INDEX idx_lba_job_run_started ON leave_balance_alert_job_run (started_at DESC);
CREATE INDEX idx_lba_job_run_status_started ON leave_balance_alert_job_run (status, started_at DESC);