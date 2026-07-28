package com.orangehrm;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.scheduling.annotation.EnableScheduling;

@SpringBootApplication
@EnableScheduling
public class OrangeHrmApplication {

    public static void main(String[] args) {
        SpringApplication.run(OrangeHrmApplication.class, args);
    }
}
