-- Leave Balance Alert (LBA) schema

CREATE TABLE IF NOT EXISTS lba_alert_rule (
  id BIGINT NOT NULL AUTO_INCREMENT,
  name VARCHAR(128) NOT NULL,
  description VARCHAR(512) NULL,
  status VARCHAR(16) NOT NULL,
  threshold_condition VARCHAR(8) NOT NULL,
  threshold_value DECIMAL(10,2) NOT NULL,
  suppression_window_days INT NOT NULL,
  created_by VARCHAR(64) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_by VARCHAR(64) NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uk_lba_alert_rule_name (name),
  KEY idx_lba_alert_rule_status (status)
);

CREATE TABLE IF NOT EXISTS lba_alert_rule_leave_type (
  rule_id BIGINT NOT NULL,
  leave_type_id BIGINT NOT NULL,
  PRIMARY KEY (rule_id, leave_type_id),
  KEY idx_lba_rule_leave_type_leave_type_id (leave_type_id),
  CONSTRAINT fk_lba_rule_leave_type_rule_id
    FOREIGN KEY (rule_id) REFERENCES lba_alert_rule (id)
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lba_alert_rule_recipient (
  id BIGINT NOT NULL AUTO_INCREMENT,
  rule_id BIGINT NOT NULL,
  recipient_type VARCHAR(16) NOT NULL,
  recipient_role_id BIGINT NULL,
  PRIMARY KEY (id),
  KEY idx_lba_rule_recipient_rule_id (rule_id),
  CONSTRAINT fk_lba_rule_recipient_rule_id
    FOREIGN KEY (rule_id) REFERENCES lba_alert_rule (id)
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lba_alert_rule_channel (
  id BIGINT NOT NULL AUTO_INCREMENT,
  rule_id BIGINT NOT NULL,
  channel_type VARCHAR(16) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uk_lba_rule_channel (rule_id, channel_type),
  CONSTRAINT fk_lba_rule_channel_rule_id
    FOREIGN KEY (rule_id) REFERENCES lba_alert_rule (id)
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lba_alert_history (
  id BIGINT NOT NULL AUTO_INCREMENT,
  rule_id BIGINT NOT NULL,
  rule_name_snapshot VARCHAR(128) NOT NULL,
  employee_id BIGINT NOT NULL,
  leave_type_id BIGINT NOT NULL,
  balance_value_snapshot DECIMAL(10,2) NOT NULL,
  threshold_condition_snapshot VARCHAR(8) NOT NULL,
  threshold_value_snapshot DECIMAL(10,2) NOT NULL,
  threshold_breached_text VARCHAR(256) NOT NULL,
  generated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  dedup_key VARCHAR(256) NOT NULL,
  suppression_window_start DATE NOT NULL,
  delivery_status VARCHAR(16) NOT NULL,
  ack_status VARCHAR(16) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_lba_alert_history_generated_at (generated_at),
  KEY idx_lba_alert_history_employee_leave_type (employee_id, leave_type_id),
  UNIQUE KEY uk_lba_alert_history_dedup_suppression (dedup_key, suppression_window_start),
  CONSTRAINT fk_lba_alert_history_rule_id
    FOREIGN KEY (rule_id) REFERENCES lba_alert_rule (id)
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lba_alert_recipient (
  id BIGINT NOT NULL AUTO_INCREMENT,
  alert_history_id BIGINT NOT NULL,
  recipient_user_id VARCHAR(64) NOT NULL,
  recipient_type VARCHAR(16) NOT NULL,
  delivered_at TIMESTAMP NULL,
  read_at TIMESTAMP NULL,
  PRIMARY KEY (id),
  KEY idx_lba_alert_recipient_recipient_user_id (recipient_user_id, delivered_at),
  KEY idx_lba_alert_recipient_alert_history_id (alert_history_id),
  CONSTRAINT fk_lba_alert_recipient_alert_history_id
    FOREIGN KEY (alert_history_id) REFERENCES lba_alert_history (id)
    ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS lba_alert_rule_audit (
  id BIGINT NOT NULL AUTO_INCREMENT,
  rule_id BIGINT NOT NULL,
  action_type VARCHAR(16) NOT NULL,
  actor_user_id VARCHAR(64) NOT NULL,
  action_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  before_state_json TEXT NULL,
  after_state_json TEXT NULL,
  PRIMARY KEY (id),
  KEY idx_lba_rule_audit_rule_id_action_at (rule_id, action_at),
  CONSTRAINT fk_lba_rule_audit_rule_id
    FOREIGN KEY (rule_id) REFERENCES lba_alert_rule (id)
    ON DELETE CASCADE
);
