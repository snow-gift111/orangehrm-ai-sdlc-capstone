-- Leave Balance Alert tables per approved solution design

CREATE TABLE IF NOT EXISTS lab_alert_rule (
    id UUID PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    threshold_value NUMERIC(10, 2) NOT NULL,
    threshold_unit VARCHAR(16) NOT NULL,
    enabled BOOLEAN NOT NULL DEFAULT TRUE,
    channel VARCHAR(32) NOT NULL,
    include_employee BOOLEAN NOT NULL DEFAULT TRUE,
    hr_role_ids TEXT NOT NULL,
    created_by VARCHAR(128) NOT NULL,
    created_at TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    updated_by VARCHAR(128) NOT NULL,
    updated_at TIMESTAMP WITHOUT TIME ZONE NOT NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS ux_lab_alert_rule_name ON lab_alert_rule (name);
CREATE INDEX IF NOT EXISTS ix_lab_alert_rule_enabled ON lab_alert_rule (enabled);

CREATE TABLE IF NOT EXISTS lab_alert_rule_leave_type (
    rule_id UUID NOT NULL,
    leave_type_id VARCHAR(64) NOT NULL,
    PRIMARY KEY (rule_id, leave_type_id),
    CONSTRAINT fk_lab_alert_rule_leave_type_rule
        FOREIGN KEY (rule_id) REFERENCES lab_alert_rule (id)
);

CREATE INDEX IF NOT EXISTS ix_lab_alert_rule_leave_type_leave_type ON lab_alert_rule_leave_type (leave_type_id);

CREATE TABLE IF NOT EXISTS lab_alert_event (
    id UUID PRIMARY KEY,
    rule_id UUID NOT NULL,
    employee_id VARCHAR(64) NOT NULL,
    leave_type_id VARCHAR(64) NOT NULL,
    evaluated_balance NUMERIC(10, 2) NOT NULL,
    threshold_value NUMERIC(10, 2) NOT NULL,
    evaluated_at TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    channel VARCHAR(32) NOT NULL,
    recipients TEXT NOT NULL,
    status VARCHAR(16) NOT NULL,
    failure_reason TEXT NULL,
    sent_at TIMESTAMP WITHOUT TIME ZONE NULL,
    CONSTRAINT fk_lab_alert_event_rule
        FOREIGN KEY (rule_id) REFERENCES lab_alert_rule (id)
);

CREATE INDEX IF NOT EXISTS ix_lab_alert_event_employee_evaluated_at ON lab_alert_event (employee_id, evaluated_at DESC);
CREATE INDEX IF NOT EXISTS ix_lab_alert_event_status_evaluated_at ON lab_alert_event (status, evaluated_at);
CREATE INDEX IF NOT EXISTS ix_lab_alert_event_rule_evaluated_at ON lab_alert_event (rule_id, evaluated_at);
