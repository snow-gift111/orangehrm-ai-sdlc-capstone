package com.orangehrm.lba.domain.entity;

import com.orangehrm.lba.domain.enums.AuditActionType;
import jakarta.persistence.*;
import lombok.Getter;
import lombok.Setter;

import java.time.Instant;

@Entity
@Table(name = "lba_rule_audit")
@Getter
@Setter
public class RuleAuditEntity {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(fetch = FetchType.LAZY, optional = false)
    @JoinColumn(name = "rule_id", nullable = false)
    private AlertRuleEntity rule;

    @Enumerated(EnumType.STRING)
    @Column(name = "action_type", nullable = false, length = 24)
    private AuditActionType actionType;

    @Column(name = "actor_user_id", length = 128)
    private String actorUserId;

    @Column(name = "timestamp", nullable = false)
    private Instant timestamp;

    @Column(name = "change_summary", columnDefinition = "TEXT")
    private String changeSummary;

    @PrePersist
    void prePersist() {
        if (timestamp == null) timestamp = Instant.now();
    }
}
