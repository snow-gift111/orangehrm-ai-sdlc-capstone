package com.orangehrm.leavealert.security;

import java.util.List;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.core.userdetails.User;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.security.provisioning.InMemoryUserDetailsManager;

@Configuration
public class UserConfig {

    /**
     * Local-dev only in-memory users to exercise RBAC endpoints.
     * In real integration, OrangeHRM will supply users/roles.
     */
    @Bean
    public InMemoryUserDetailsManager inMemoryUserDetailsManager() {
        UserDetails hrAdmin = User.withUsername("hradmin")
                .password("{noop}password")
                .roles("HR_ADMIN")
                .build();

        UserDetails employee = User.withUsername("e001")
                .password("{noop}password")
                .roles("EMPLOYEE")
                .build();

        UserDetails hr = User.withUsername("hr")
                .password("{noop}password")
                .roles("HR")
                .build();

        return new InMemoryUserDetailsManager(List.of(hrAdmin, employee, hr));
    }
}
