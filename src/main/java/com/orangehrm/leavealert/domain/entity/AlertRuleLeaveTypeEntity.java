package com.orangehrm.leavealert.domain.entity;

import jakarta.persistence.Column;
import jakarta.persistence.EmbeddedId;
import jakarta.persistence.Entity;
import jakarta.persistence.Table;
import java.io.Serializable;
import java.util.Objects;
import java.util.UUID;

@Entity
@Table(name = "lab_alert_rule_leave_type")
public class AlertRuleLeaveTypeEntity {

    @EmbeddedId
    private Pk pk;

    public AlertRuleLeaveTypeEntity() {
    }

    public AlertRuleLeaveTypeEntity(UUID ruleId, String leaveTypeId) {
        this.pk = new Pk(ruleId, leaveTypeId);
    }

    public Pk getPk() {
        return pk;
    }

    public void setPk(Pk pk) {
        this.pk = pk;
    }

    @jakarta.persistence.Embeddable
    public static class Pk implements Serializable {

        @Column(name = "rule_id", nullable = false)
        private UUID ruleId;

        @Column(name = "leave_type_id", nullable = false, length = 64)
        private String leaveTypeId;

        public Pk() {
        }

        public Pk(UUID ruleId, String leaveTypeId) {
            this.ruleId = ruleId;
            this.leaveTypeId = leaveTypeId;
        }

        public UUID getRuleId() {
            return ruleId;
        }

        public void setRuleId(UUID ruleId) {
            this.ruleId = ruleId;
        }

        public String getLeaveTypeId() {
            return leaveTypeId;
        }

        public void setLeaveTypeId(String leaveTypeId) {
            this.leaveTypeId = leaveTypeId;
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
