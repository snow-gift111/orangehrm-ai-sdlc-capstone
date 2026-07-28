package com.orangehrm.lba;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.scheduling.annotation.EnableScheduling;

@SpringBootApplication
@EnableScheduling
public class LeaveBalanceAlertApplication {
  public static void main(String[] args) {
    SpringApplication.run(LeaveBalanceAlertApplication.class, args);
  }
}
