package com.orangehrm.lba.config;

import org.springframework.boot.context.properties.ConfigurationProperties;

@ConfigurationProperties(prefix = "lba")
public record LbaProperties(Scheduler scheduler, Email email, Security security) {

    public record Scheduler(String cron) {
    }

    public record Email(String from, String subjectPrefix) {
    }

    public record Security(String ruleManageAuthority, String historyViewAuthority) {
    }
}
