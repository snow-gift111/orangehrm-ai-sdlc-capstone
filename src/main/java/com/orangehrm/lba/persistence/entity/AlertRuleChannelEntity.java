package com.orangehrm.lba.persistence.entity;

import com.orangehrm.lba.domain.model.ChannelType;
import jakarta.persistence.*;

@Entity
@Table(name = "lba_alert_rule_channel")
public class AlertRuleChannelEntity {

  @Id
  @GeneratedValue(strategy = GenerationType.IDENTITY)
  private Long id;

  @ManyToOne(fetch = FetchType.LAZY)
  @JoinColumn(name = "rule_id", nullable = false)
  private AlertRuleEntity rule;

  @Enumerated(EnumType.STRING)
  @Column(name = "channel_type", nullable = false, length = 16)
  private ChannelType channelType;

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

  public ChannelType getChannelType() {
    return channelType;
  }

  public void setChannelType(ChannelType channelType) {
    this.channelType = channelType;
  }
}
