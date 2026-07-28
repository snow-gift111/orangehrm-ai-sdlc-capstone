package com.orangehrm.lba.persistence.entity;

import com.orangehrm.lba.domain.model.RecipientType;
import jakarta.persistence.*;

@Entity
@Table(name = "lba_alert_rule_recipient")
public class AlertRuleRecipientEntity {

  @Id
  @GeneratedValue(strategy = GenerationType.IDENTITY)
  private Long id;

  @ManyToOne(fetch = FetchType.LAZY)
  @JoinColumn(name = "rule_id", nullable = false)
  private AlertRuleEntity rule;

  @Enumerated(EnumType.STRING)
  @Column(name = "recipient_type", nullable = false, length = 16)
  private RecipientType recipientType;

  @Column(name = "recipient_role_id")
  private Long recipientRoleId;

  public Long getId() {
    return id;
  }

  public void setId(Long id) {
    this.id = id;
  }

  public AlertRuleEntity getRule() {
    return rule;
  }

  public void setRule(AlertRuleEntity rule) {
    this.rule = rule;
  }

  public RecipientType getRecipientType() {
    return recipientType;
  }

  public void setRecipientType(RecipientType recipientType) {
    this.recipientType = recipientType;
  }

  public Long getRecipientRoleId() {
    return recipientRoleId;
  }

  public void setRecipientRoleId(Long recipientRoleId) {
    this.recipientRoleId = recipientRoleId;
  }
}
