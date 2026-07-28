package com.orangehrm.lba.persistence.entity;

import jakarta.persistence.*;
import java.io.Serializable;
import java.util.Objects;

@Entity
@Table(name = "lba_alert_rule_leave_type")
public class AlertRuleLeaveTypeEntity {

  @EmbeddedId
  private Pk id;

  @ManyToOne(fetch = FetchType.LAZY)
  @MapsId("ruleId")
  @JoinColumn(name = "rule_id", nullable = false)
  private AlertRuleEntity rule;

  public AlertRuleLeaveTypeEntity() {}

  public AlertRuleLeaveTypeEntity(AlertRuleEntity rule, Long leaveTypeId) {
    this.rule = rule;
    this.id = new Pk(rule.getId(), leaveTypeId);
  }

  public Pk getId() {
    return id;
  }

  public void setId(Pk id) {
    this.id = id;
  }

  public AlertRuleEntity getRule() {
    return rule;
  }

  public void setRule(AlertRuleEntity rule) {
    this.rule = rule;
  }

  public Long getLeaveTypeId() {
    return id != null ? id.leaveTypeId : null;
  }

  @Embeddable
  public static class Pk implements Serializable {
    @Column(name = "rule_id")
    private Long ruleId;

    @Column(name = "leave_type_id")
    private Long leaveTypeId;

    public Pk() {}

    public Pk(Long ruleId, Long leaveTypeId) {
      this.ruleId = ruleId;
      this.leaveTypeId = leaveTypeId;
    }

    public Long getRuleId() {
      return ruleId;
    }

    public Long getLeaveTypeId() {
      return leaveTypeId;
    }

    @Override
    public boolean equals(Object o) {
      if (this == o) return true;
      if (o == null || getClass() != o.getClass()) return false;
      Pk pk = (Pk) o;
      return Objects.equals(ruleId, pk.ruleId) && Objects.equals(leaveTypeId, pk.leaveTypeId);
    }

    @Override
    public int hashCode() {
      return Objects.hash(ruleId, leaveTypeId);
    }
  }
}
