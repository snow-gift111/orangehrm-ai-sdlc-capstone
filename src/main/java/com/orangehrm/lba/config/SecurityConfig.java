package com.orangehrm.lba.config;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.config.Customizer;
import org.springframework.security.config.annotation.method.configuration.EnableMethodSecurity;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.web.SecurityFilterChain;

@Configuration
@EnableMethodSecurity
public class SecurityConfig {

    @Bean
    SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {
        // Baseline configuration:
        // - Permit actuator/health if needed (not included here)
        // - Require authentication for all API endpoints
        // - Use HTTP Basic for demo/local; in real OrangeHRM this must integrate with existing auth.
        http
            .csrf(csrf -> csrf.disable())
            .authorizeHttpRequests(registry -> registry
                .requestMatchers("/api/**").authenticated()
                .anyRequest().permitAll()
            )
            .httpBasic(Customizer.withDefaults());

        return http.build();
    }
}
