package com.orangehrm.leavealert.config;

import org.springframework.boot.context.properties.ConfigurationProperties;

@ConfigurationProperties(prefix = "leave-balance-alert")
public class LeaveBalanceAlertProperties {

    private final Scheduler scheduler = new Scheduler();
    private final Integration integration = new Integration();

    public Scheduler getScheduler() {
        return scheduler;
    }

    public Integration getIntegration() {
        return integration;
    }

    public static class Scheduler {
        private boolean enabled;
        private String cron = "0 0 1 * * *";

        public boolean isEnabled() {
            return enabled;
        }

        public void setEnabled(boolean enabled) {
            this.enabled = enabled;
        }

        public String getCron() {
            return cron;
        }

        public void setCron(String cron) {
            this.cron = cron;
        }
    }

    public static class Integration {
        private String balanceQuery;
        private String managerQuery;
        private String hrUsersQuery;

        public String getBalanceQuery() {
            return balanceQuery;
        }

        public void setBalanceQuery(String balanceQuery) {
            this.balanceQuery = balanceQuery;
        }

        public String getManagerQuery() {
            return managerQuery;
        }

        public void setManagerQuery(String managerQuery) {
            this.managerQuery = managerQuery;
        }

        public String getHrUsersQuery() {
            return hrUsersQuery;
        }

        public void setHrUsersQuery(String hrUsersQuery) {
            this.hrUsersQuery = hrUsersQuery;
        }
    }
}
