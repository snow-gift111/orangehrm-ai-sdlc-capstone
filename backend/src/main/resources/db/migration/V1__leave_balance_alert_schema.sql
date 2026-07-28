-- Leave Balance Alert schema (PostgreSQL)

CREATE TABLE IF NOT EXISTS leave_type (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    unit VARCHAR(16) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_leave_type_name UNIQUE (name)
);

CREATE INDEX IF NOT EXISTS idx_leave_type_active ON leave_type (is_active);

-- Minimal employee table stub.
-- NOTE: In a real OrangeHRM integration, this table should be provided by PIM.
CREATE TABLE IF NOT EXISTS employee (
    id BIGSERIAL PRIMARY KEY,
    employee_number VARCHAR(64),
    first_name VARCHAR(120),
    last_name VARCHAR(120)
);

-- Minimal user table stub.
-- NOTE: In a real OrangeHRM integration, this table should be provided by the Auth module.
CREATE TABLE IF NOT EXISTS app_user (
    id BIGSERIAL PRIMARY KEY,
    username VARCHAR(120) NOT NULL,
    CONSTRAINT uq_app_user_username UNIQUE (username)
);

CREATE TABLE IF NOT EXISTS leave_balance (
    id BIGSERIAL PRIMARY KEY,
    employee_id BIGINT NOT NULL,
    leave_type_id BIGINT NOT NULL,
    balance_value NUMERIC(10,2) NOT NULL,
    last_updated_at TIMESTAMPTZ NOT NULL,
    updated_by_user_id BIGINT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_leave_balance_employee_type UNIQUE (employee_id, leave_type_id),
    CONSTRAINT fk_leave_balance_employee FOREIGN KEY (employee_id) REFERENCES employee(id),
    CONSTRAINT fk_leave_balance_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type(id),
    CONSTRAINT fk_leave_balance_updated_by FOREIGN KEY (updated_by_user_id) REFERENCES app_user(id)
);

CREATE INDEX IF NOT EXISTS idx_leave_balance_employee ON leave_balance (employee_id);
CREATE INDEX IF NOT EXISTS idx_leave_balance_type ON leave_balance (leave_type_id);

CREATE TABLE IF NOT EXISTS leave_alert_rule (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(160) NOT NULL,
    leave_type_id BIGINT NOT NULL,
    threshold_value NUMERIC(10,2) NOT NULL,
    comparison_operator VARCHAR(16) NOT NULL,
    frequency_value INT NOT NULL,
    frequency_unit VARCHAR(16) NOT NULL,
    recipients_config TEXT NOT NULL,
    is_active BOOLEAN NOT NULL,
    created_by_user_id BIGINT NULL,
    updated_by_user_id BIGINT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_rule_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type(id),
    CONSTRAINT fk_rule_created_by FOREIGN KEY (created_by_user_id) REFERENCES app_user(id),
    CONSTRAINT fk_rule_updated_by FOREIGN KEY (updated_by_user_id) REFERENCES app_user(id)
);

CREATE INDEX IF NOT EXISTS idx_rule_active ON leave_alert_rule (is_active);
CREATE INDEX IF NOT EXISTS idx_rule_leave_type ON leave_alert_rule (leave_type_id);

CREATE TABLE IF NOT EXISTS leave_alert (
    id BIGSERIAL PRIMARY KEY,
    rule_id BIGINT NOT NULL,
    employee_id BIGINT NOT NULL,
    leave_type_id BIGINT NOT NULL,
    evaluated_balance_value NUMERIC(10,2) NOT NULL,
    triggered_at TIMESTAMPTZ NOT NULL,
    delivery_status VARCHAR(16) NOT NULL,
    delivery_error_reason TEXT NULL,
    recipient_targets TEXT NOT NULL,
    correlation_id VARCHAR(64) NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_alert_rule FOREIGN KEY (rule_id) REFERENCES leave_alert_rule(id),
    CONSTRAINT fk_alert_employee FOREIGN KEY (employee_id) REFERENCES employee(id),
    CONSTRAINT fk_alert_leave_type FOREIGN KEY (leave_type_id) REFERENCES leave_type(id)
);

CREATE INDEX IF NOT EXISTS idx_alert_employee_time ON leave_alert (employee_id, triggered_at DESC);
CREATE INDEX IF NOT EXISTS idx_alert_rule_employee_time ON leave_alert (rule_id, employee_id, triggered_at DESC);

CREATE TABLE IF NOT EXISTS leave_alert_recipient (
    alert_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    PRIMARY KEY (alert_id, user_id),
    CONSTRAINT fk_alert_recipient_alert FOREIGN KEY (alert_id) REFERENCES leave_alert(id) ON DELETE CASCADE,
    CONSTRAINT fk_alert_recipient_user FOREIGN KEY (user_id) REFERENCES app_user(id) ON DELETE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_recipient_user ON leave_alert_recipient (user_id, alert_id);

CREATE TABLE IF NOT EXISTS audit_log (
    id BIGSERIAL PRIMARY KEY,
    actor_user_id BIGINT NULL,
    action_type VARCHAR(64) NOT NULL,
    entity_type VARCHAR(64) NOT NULL,
    entity_id VARCHAR(64) NOT NULL,
    before_snapshot TEXT NULL,
    after_snapshot TEXT NULL,
    occurred_at TIMESTAMPTZ NOT NULL,
    correlation_id VARCHAR(64) NULL,
    CONSTRAINT fk_audit_actor FOREIGN KEY (actor_user_id) REFERENCES app_user(id)
);

CREATE INDEX IF NOT EXISTS idx_audit_time ON audit_log (occurred_at DESC);
CREATE INDEX IF NOT EXISTS idx_audit_entity ON audit_log (entity_type, entity_id);
