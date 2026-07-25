package com.orangehrm.leavealert;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.scheduling.annotation.EnableScheduling;

@EnableScheduling
@SpringBootApplication
public class LeaveBalanceAlertApplication {

    public static void main(String[] args) {
        SpringApplication.run(LeaveBalanceAlertApplication.class, args);
    }
}
