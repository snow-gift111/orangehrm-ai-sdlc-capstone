CREATE TABLE leave_type (
    id BIGINT NOT NULL AUTO_INCREMENT,
    code VARCHAR(64) NOT NULL,
    name VARCHAR(255) NOT NULL,
    unit VARCHAR(16) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    PRIMARY KEY (id),
    CONSTRAINT uk_leave_type_code UNIQUE (code),
    CONSTRAINT ck_leave_type_unit CHECK (unit IN ('DAYS', 'HOURS'))
);

CREATE INDEX idx_leave_type_active ON leave_type (is_active);

CREATE TABLE leave_balance (
    id BIGINT NOT NULL AUTO_INCREMENT,
    employee_id BIGINT NOT NULL,
    leave_type_id BIGINT NOT NULL,
    period_key VARCHAR(32) NOT NULL,
    balance_value DECIMAL(12, 2) NOT NULL,
    unit VARCHAR(16) NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_leave_balance_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type (id),
    CONSTRAINT uk_leave_balance_employee_type_period UNIQUE (employee_id, leave_type_id, period_key),
    CONSTRAINT ck_leave_balance_unit CHECK (unit IN ('DAYS', 'HOURS'))
);

CREATE INDEX idx_leave_balance_period_type ON leave_balance (period_key, leave_type_id);
CREATE INDEX idx_leave_balance_employee_period ON leave_balance (employee_id, period_key);
CREATE INDEX idx_leave_balance_eval ON leave_balance (leave_type_id, period_key, balance_value);

CREATE TABLE leave_alert_rule (
    id BIGINT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    status VARCHAR(16) NOT NULL,
    threshold_comparator VARCHAR(16) NOT NULL DEFAULT 'LEQ',
    threshold_value DECIMAL(12, 2) NOT NULL,
    suppression_window_days INT NOT NULL,
    significant_change_delta DECIMAL(12, 2) NOT NULL DEFAULT 0,
    schedule_type VARCHAR(16) NOT NULL DEFAULT 'DAILY',
    schedule_value VARCHAR(128) NULL,
    created_by BIGINT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_by BIGINT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT ck_leave_alert_rule_status CHECK (status IN ('ACTIVE', 'INACTIVE')),
    CONSTRAINT ck_leave_alert_rule_comparator CHECK (threshold_comparator IN ('LEQ')),
    CONSTRAINT ck_leave_alert_rule_threshold CHECK (threshold_value >= 0),
    CONSTRAINT ck_leave_alert_rule_suppression CHECK (suppression_window_days >= 0),
    CONSTRAINT ck_leave_alert_rule_delta CHECK (significant_change_delta >= 0),
    CONSTRAINT ck_leave_alert_rule_schedule_type CHECK (schedule_type IN ('DAILY', 'CRON'))
);

CREATE INDEX idx_leave_alert_rule_status ON leave_alert_rule (status);
CREATE INDEX idx_leave_alert_rule_updated_at ON leave_alert_rule (updated_at);

CREATE TABLE leave_alert_rule_leave_type (
    rule_id BIGINT NOT NULL,
    leave_type_id BIGINT NOT NULL,
    PRIMARY KEY (rule_id, leave_type_id),
    CONSTRAINT fk_larlt_rule FOREIGN KEY (rule_id) REFERENCES leave_alert_rule (id) ON DELETE CASCADE,
    CONSTRAINT fk_larlt_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type (id)
);

CREATE INDEX idx_larlt_leave_type ON leave_alert_rule_leave_type (leave_type_id);

CREATE TABLE leave_alert_rule_recipient (
    rule_id BIGINT NOT NULL,
    recipient_type VARCHAR(16) NOT NULL,
    PRIMARY KEY (rule_id, recipient_type),
    CONSTRAINT fk_larr_rule FOREIGN KEY (rule_id) REFERENCES leave_alert_rule (id) ON DELETE CASCADE,
    CONSTRAINT ck_larr_recipient_type CHECK (recipient_type IN ('EMPLOYEE', 'MANAGER', 'HR'))
);

CREATE TABLE leave_alert_instance (
    id BIGINT NOT NULL AUTO_INCREMENT,
    rule_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    leave_type_id BIGINT NOT NULL,
    period_key VARCHAR(32) NOT NULL,
    balance_at_trigger DECIMAL(12, 2) NOT NULL,
    threshold_at_trigger DECIMAL(12, 2) NOT NULL,
    triggered_at TIMESTAMP NOT NULL,
    status VARCHAR(32) NOT NULL,
    acknowledged_by_user_id BIGINT NULL,
    acknowledged_at TIMESTAMP NULL,
    dedupe_key CHAR(64) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_lai_rule FOREIGN KEY (rule_id) REFERENCES leave_alert_rule (id),
    CONSTRAINT fk_lai_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type (id),
    CONSTRAINT uk_lai_dedupe_key UNIQUE (dedupe_key),
    CONSTRAINT ck_lai_status CHECK (status IN ('OPEN', 'ACKNOWLEDGED'))
);

CREATE INDEX idx_lai_triggered_at ON leave_alert_instance (triggered_at);
CREATE INDEX idx_lai_employee_triggered ON leave_alert_instance (employee_id, triggered_at);
CREATE INDEX idx_lai_rule_triggered ON leave_alert_instance (rule_id, triggered_at);
CREATE INDEX idx_lai_status ON leave_alert_instance (status);
CREATE INDEX idx_lai_suppression ON leave_alert_instance (rule_id, employee_id, leave_type_id, triggered_at);

CREATE TABLE leave_alert_recipient_delivery (
    id BIGINT NOT NULL AUTO_INCREMENT,
    alert_instance_id BIGINT NOT NULL,
    recipient_user_id BIGINT NOT NULL,
    channel VARCHAR(16) NOT NULL,
    delivery_status VARCHAR(16) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_lard_alert FOREIGN KEY (alert_instance_id) REFERENCES leave_alert_instance (id) ON DELETE CASCADE,
    CONSTRAINT uk_lard_alert_recipient_channel UNIQUE (alert_instance_id, recipient_user_id, channel),
    CONSTRAINT ck_lard_channel CHECK (channel IN ('IN_APP')),
    CONSTRAINT ck_lard_status CHECK (delivery_status IN ('CREATED', 'READ', 'FAILED'))
);

CREATE INDEX idx_lard_recipient_status ON leave_alert_recipient_delivery (recipient_user_id, delivery_status);
CREATE INDEX idx_lard_alert ON leave_alert_recipient_delivery (alert_instance_id);

CREATE TABLE in_app_notification (
    id BIGINT NOT NULL AUTO_INCREMENT,
    recipient_user_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message VARCHAR(1000) NOT NULL,
    link_type VARCHAR(64) NOT NULL,
    link_id BIGINT NOT NULL,
    status VARCHAR(16) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    PRIMARY KEY (id),
    CONSTRAINT ck_ian_status CHECK (status IN ('NEW', 'READ'))
);

CREATE INDEX idx_ian_recipient_status ON in_app_notification (recipient_user_id, status);
CREATE INDEX idx_ian_created_at ON in_app_notification (created_at);

CREATE TABLE leave_alert_job_run (
    id BIGINT NOT NULL AUTO_INCREMENT,
    started_at TIMESTAMP NOT NULL,
    finished_at TIMESTAMP NULL,
    status VARCHAR(16) NOT NULL,
    rules_evaluated_count INT NOT NULL DEFAULT 0,
    balances_evaluated_count INT NOT NULL DEFAULT 0,
    alerts_created_count INT NOT NULL DEFAULT 0,
    error_summary TEXT NULL,
    PRIMARY KEY (id),
    CONSTRAINT ck_lajr_status CHECK (status IN ('STARTED', 'SUCCESS', 'FAILED', 'PARTIAL'))
);

CREATE INDEX idx_lajr_started_at ON leave_alert_job_run (started_at);
CREATE INDEX idx_lajr_status ON leave_alert_job_run (status);

CREATE TABLE audit_event (
    id BIGINT NOT NULL AUTO_INCREMENT,
    actor_user_id BIGINT NULL,
    event_type VARCHAR(64) NOT NULL,
    entity_type VARCHAR(64) NOT NULL,
    entity_id BIGINT NULL,
    occurred_at TIMESTAMP NOT NULL,
    metadata_json TEXT NULL,
    PRIMARY KEY (id)
);

CREATE INDEX idx_audit_event_occurred_at ON audit_event (occurred_at);
CREATE INDEX idx_audit_event_entity ON audit_event (entity_type, entity_id);
CREATE INDEX idx_audit_event_actor_occurred ON audit_event (actor_user_id, occurred_at);
