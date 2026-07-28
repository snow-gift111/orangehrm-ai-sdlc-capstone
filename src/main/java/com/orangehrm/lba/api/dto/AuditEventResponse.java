package com.orangehrm.lba.api.dto;

import java.time.Instant;

public class AuditEventResponse {
  private Long id;
  private Long ruleId;
  private String actionType;
  private String actorUserId;
  private Instant actionAt;
  private String beforeStateJson;
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
