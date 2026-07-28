package com.orangehrm.leavealert;

import com.orangehrm.leavealert.config.LeaveBalanceAlertProperties;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.boot.context.properties.EnableConfigurationProperties;
import org.springframework.scheduling.annotation.EnableScheduling;

@EnableScheduling
@SpringBootApplication
@EnableConfigurationProperties(LeaveBalanceAlertProperties.class)
public class LeaveBalanceAlertApplication {

    public static void main(String[] args) {
        SpringApplication.run(LeaveBalanceAlertApplication.class, args);
    }
}