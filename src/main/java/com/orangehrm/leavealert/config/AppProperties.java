package com.orangehrm.leavealert.config;

import java.util.List;
import org.springframework.boot.context.properties.ConfigurationProperties;

@ConfigurationProperties(prefix = "app")
public class AppProperties {

    private final Security security = new Security();
    private final LeaveBalanceAlert leaveBalanceAlert = new LeaveBalanceAlert();

    public Security getSecurity() {
        return security;
    }

    public LeaveBalanceAlert getLeaveBalanceAlert() {
        return leaveBalanceAlert;
    }

    public static class Security {
        private String hrAdminRole = "HR_ADMIN";
        private String hrHistoryViewerRoles = "HR";

        public String getHrAdminRole() {
            return hrAdminRole;
        }

        public void setHrAdminRole(String hrAdminRole) {
            this.hrAdminRole = hrAdminRole;
        }

        public String getHrHistoryViewerRoles() {
            return hrHistoryViewerRoles;
        }

        public void setHrHistoryViewerRoles(String hrHistoryViewerRoles) {
            this.hrHistoryViewerRoles = hrHistoryViewerRoles;
        }

        public List<String> getHrHistoryViewerRolesList() {
            return List.of(hrHistoryViewerRoles.split(","));
        }
    }

    public static class LeaveBalanceAlert {
        private String evaluationCron = "0 0 2 * * *";
        private int employeeBatchSize = 500;

        public String getEvaluationCron() {
            return evaluationCron;
        }

        public void setEvaluationCron(String evaluationCron) {
            this.evaluationCron = evaluationCron;
        }

        public int getEmployeeBatchSize() {
            return employeeBatchSize;
        }

        public void setEmployeeBatchSize(int employeeBatchSize) {
            this.employeeBatchSize = employeeBatchSize;
        }
    }
}
