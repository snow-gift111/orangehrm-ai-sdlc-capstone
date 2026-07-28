package com.orangehrm.lba.persistence.entity;

import jakarta.persistence.*;
import java.time.Instant;

@Entity
@Table(name = "lba_alert_rule_audit")
public class AlertRuleAuditEntity {

  @Id
  @GeneratedValue(strategy = GenerationType.IDENTITY)
  private Long id;

  @Column(name = "rule_id", nullable = false)
  private Long ruleId;

  @Column(name = "action_type", nullable = false, length = 16)
  private String actionType;

  @Column(name = "actor_user_id", nullable = false, length = 64)
  private String actorUserId;

  @Column(name = "action_at", nullable = false)
  private Instant actionAt;

  @Column(name = "before_state_json", columnDefinition = "TEXT")
  private String beforeStateJson;

  @Column(name = "after_state_json", columnDefinition = "TEXT")
  private String afterStateJson;

  public Long getId() {
    return id;
  }

  public void setId(Long id) {
    this.id = id;
  }

  public Long getRuleId() {
    return ruleId;
  }

  public void setRuleId(Long ruleId) {
    this.ruleId = ruleId;
  }

  public String getActionType() {
    return actionType;
  }

  public void setActionType(String actionType) {
    this.actionType = actionType;
  }

  public String getActorUserId() {
    return actorUserId;
  }

  public void setActorUserId(String actorUserId) {
    this.actorUserId = actorUserId;
  }

  public Instant getActionAt() {
    return actionAt;
  }

  public void setActionAt(Instant actionAt) {
    this.actionAt = actionAt;
  }

  public String getBeforeStateJson() {
    return beforeStateJson;
  }

  public void setBeforeStateJson(String beforeStateJson) {
    this.beforeStateJson = beforeStateJson;
  }

  public String getAfterStateJson() {
    return afterStateJson;
  }

  public void setAfterStateJson(String afterStateJson) {
    this.afterStateJson = afterStateJson;
  }
}
