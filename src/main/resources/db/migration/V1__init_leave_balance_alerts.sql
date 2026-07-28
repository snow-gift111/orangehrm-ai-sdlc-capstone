-- Leave Balance Alerts - initial schema

CREATE TABLE IF NOT EXISTS app_user (
  id BIGSERIAL PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS employee (
  id BIGSERIAL PRIMARY KEY,
  employee_number VARCHAR(50) NOT NULL UNIQUE,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS leave_type (
  id BIGSERIAL PRIMARY KEY,
  name VARCHAR(150) NOT NULL UNIQUE,
  code VARCHAR(50) UNIQUE,
  active BOOLEAN NOT NULL DEFAULT true,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE IF NOT EXISTS leave_balance (
  id BIGSERIAL PRIMARY KEY,
  employee_id BIGINT NOT NULL REFERENCES employee(id) ON DELETE CASCADE,
  leave_type_id BIGINT NOT NULL REFERENCES leave_type(id) ON DELETE CASCADE,
  balance NUMERIC(10,2) NOT NULL,
  last_updated_at TIMESTAMPTZ NOT NULL,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  CONSTRAINT uq_leave_balance_employee_type UNIQUE(employee_id, leave_type_id)
);

CREATE INDEX IF NOT EXISTS idx_leave_balance_employee ON leave_balance(employee_id);
CREATE INDEX IF NOT EXISTS idx_leave_balance_leave_type ON leave_balance(leave_type_id);

CREATE TABLE IF NOT EXISTS leave_balance_adjustment (
  id BIGSERIAL PRIMARY KEY,
  employee_id BIGINT NOT NULL REFERENCES employee(id) ON DELETE CASCADE,
  leave_type_id BIGINT NOT NULL REFERENCES leave_type(id) ON DELETE CASCADE,
  adjustment_kind VARCHAR(10) NOT NULL,
  delta NUMERIC(10,2),
  new_balance NUMERIC(10,2),
  reason TEXT NOT NULL,
  effective_date DATE NOT NULL,
  created_by_user_id BIGINT NOT NULL REFERENCES app_user(id),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_adjustment_emp_type_created ON leave_balance_adjustment(employee_id, leave_type_id, created_at);

CREATE TABLE IF NOT EXISTS leave_alert_rule (
  id BIGSERIAL PRIMARY KEY,
  scope_type VARCHAR(10) NOT NULL,
  leave_type_id BIGINT REFERENCES leave_type(id) ON DELETE SET NULL,
  threshold_operator VARCHAR(20) NOT NULL,
  threshold_value NUMERIC(10,2) NOT NULL,
  frequency VARCHAR(20) NOT NULL,
  active BOOLEAN NOT NULL DEFAULT true,
  created_by_user_id BIGINT NOT NULL REFERENCES app_user(id),
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_alert_rule_active_scope_type ON leave_alert_rule(active, scope_type, leave_type_id);

CREATE TABLE IF NOT EXISTS leave_alert_rule_recipient (
  id BIGSERIAL PRIMARY KEY,
  rule_id BIGINT NOT NULL REFERENCES leave_alert_rule(id) ON DELETE CASCADE,
  recipient_type VARCHAR(20) NOT NULL,
  CONSTRAINT uq_rule_recipient UNIQUE(rule_id, recipient_type)
);

CREATE TABLE IF NOT EXISTS leave_alert_event (
  id BIGSERIAL PRIMARY KEY,
  rule_id BIGINT NOT NULL REFERENCES leave_alert_rule(id) ON DELETE CASCADE,
  employee_id BIGINT NOT NULL REFERENCES employee(id) ON DELETE CASCADE,
  leave_type_id BIGINT NOT NULL REFERENCES leave_type(id) ON DELETE CASCADE,
  current_balance NUMERIC(10,2) NOT NULL,
  threshold_breached TEXT NOT NULL,
  recommended_action TEXT NOT NULL,
  generated_at TIMESTAMPTZ NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_alert_event_emp_type_generated ON leave_alert_event(employee_id, leave_type_id, generated_at);
CREATE INDEX IF NOT EXISTS idx_alert_event_rule_generated ON leave_alert_event(rule_id, generated_at);

CREATE TABLE IF NOT EXISTS leave_alert_recipient (
  id BIGSERIAL PRIMARY KEY,
  alert_event_id BIGINT NOT NULL REFERENCES leave_alert_event(id) ON DELETE CASCADE,
  recipient_user_id BIGINT NOT NULL REFERENCES app_user(id) ON DELETE CASCADE,
  status VARCHAR(20) NOT NULL,
  new_at TIMESTAMPTZ NOT NULL,
  acknowledged_at TIMESTAMPTZ,
  resolved_at TIMESTAMPTZ,
  closed_at TIMESTAMPTZ,
  CONSTRAINT uq_alert_event_recipient UNIQUE(alert_event_id, recipient_user_id)
);

CREATE INDEX IF NOT EXISTS idx_alert_recipient_user_status_date ON leave_alert_recipient(recipient_user_id, status, new_at);

-- seed minimal users/employees for local use
INSERT INTO app_user (username, password_hash, role) VALUES
  ('hradmin', '{noop}hradmin', 'HR_ADMIN'),
  ('employee1', '{noop}employee1', 'EMPLOYEE')
ON CONFLICT DO NOTHING;

INSERT INTO employee (employee_number, first_name, last_name) VALUES
  ('E0001', 'John', 'Doe')
ON CONFLICT DO NOTHING;
