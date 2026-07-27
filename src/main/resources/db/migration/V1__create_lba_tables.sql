CREATE TABLE IF NOT EXISTS lba_alert_rule (
    id BIGSERIAL PRIMARY KEY,
    leave_type_scope VARCHAR(16) NOT NULL,
    leave_type_id BIGINT NULL,
    operator VARCHAR(4) NOT NULL,
    threshold_value NUMERIC(10,2) NOT NULL CHECK (threshold_value >= 0),
    suppression_window_days INTEGER NOT NULL DEFAULT 0 CHECK (suppression_window_days >= 0),
    recipients_json TEXT NOT NULL,
    status VARCHAR(16) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    created_by VARCHAR(128) NULL,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_by VARCHAR(128) NULL
);

CREATE INDEX IF NOT EXISTS idx_lba_rule_status ON lba_alert_rule(status);
CREATE INDEX IF NOT EXISTS idx_lba_rule_leave_type ON lba_alert_rule(leave_type_scope, leave_type_id);

CREATE TABLE IF NOT EXISTS lba_alert_event (
    id BIGSERIAL PRIMARY KEY,
    rule_id BIGINT NOT NULL REFERENCES lba_alert_rule(id) ON DELETE RESTRICT,
    employee_id BIGINT NOT NULL,
    leave_type_id BIGINT NULL,
    balance_snapshot NUMERIC(10,2) NOT NULL,
    evaluated_at TIMESTAMPTZ NOT NULL,
    status VARCHAR(24) NOT NULL,
    failure_reason TEXT NULL,
    sent_at TIMESTAMPTZ NULL,
    job_run_id VARCHAR(64) NULL
);

CREATE INDEX IF NOT EXISTS idx_lba_event_rule_employee_leave_eval ON lba_alert_event(rule_id, employee_id, leave_type_id, evaluated_at);
CREATE INDEX IF NOT EXISTS idx_lba_event_status_eval ON lba_alert_event(status, evaluated_at);
CREATE INDEX IF NOT EXISTS idx_lba_event_suppression ON lba_alert_event(rule_id, employee_id, leave_type_id, status, sent_at);

CREATE TABLE IF NOT EXISTS lba_alert_event_recipient (
    id BIGSERIAL PRIMARY KEY,
    event_id BIGINT NOT NULL REFERENCES lba_alert_event(id) ON DELETE CASCADE,
    recipient_type VARCHAR(24) NOT NULL,
    recipient_user_id BIGINT NULL,
    recipient_email VARCHAR(320) NULL,
    delivery_status VARCHAR(24) NOT NULL,
    failure_reason TEXT NULL
);

CREATE INDEX IF NOT EXISTS idx_lba_recipient_event ON lba_alert_event_recipient(event_id);
CREATE INDEX IF NOT EXISTS idx_lba_recipient_email ON lba_alert_event_recipient(recipient_email);

CREATE TABLE IF NOT EXISTS lba_rule_audit (
    id BIGSERIAL PRIMARY KEY,
    rule_id BIGINT NOT NULL REFERENCES lba_alert_rule(id) ON DELETE RESTRICT,
    action_type VARCHAR(24) NOT NULL,
    actor_user_id VARCHAR(128) NULL,
    timestamp TIMESTAMPTZ NOT NULL,
    change_summary TEXT NULL
);

CREATE INDEX IF NOT EXISTS idx_lba_audit_rule_time ON lba_rule_audit(rule_id, timestamp);
