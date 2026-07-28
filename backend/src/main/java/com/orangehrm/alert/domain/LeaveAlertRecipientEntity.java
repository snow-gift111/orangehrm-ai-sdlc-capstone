package com.orangehrm.alert.domain;

import jakarta.persistence.*;

import java.io.Serializable;
import java.util.Objects;

@Entity
@Table(name = "leave_alert_recipient")
public class LeaveAlertRecipientEntity {

    @EmbeddedId
    private Pk pk;

    protected LeaveAlertRecipientEntity() {
    }

    public LeaveAlertRecipientEntity(long alertId, long userId) {
        this.pk = new Pk(alertId, userId);
    }

    public Pk getPk() {
        return pk;
    }

    @Embeddable
    public static class Pk implements Serializable {
        @Column(name = "alert_id", nullable = false)
        private Long alertId;

        @Column(name = "user_id", nullable = false)
        private Long userId;

        protected Pk() {
        }

        public Pk(Long alertId, Long userId) {
            this.alertId = alertId;
            this.userId = userId;
        }

        public Long getAlertId() {
            return alertId;
        }

        public Long getUserId() {
            return userId;
        }

        @Override
        public boolean equals(Object o) {
            if (this == o) return true;
            if (o == null || getClass() != o.getClass()) return false;
            Pk pk = (Pk) o;
            return Objects.equals(alertId, pk.alertId) && Objects.equals(userId, pk.userId);
        }

        @Override
        public int hashCode() {
            return Objects.hash(alertId, userId);
        }
    }
}
