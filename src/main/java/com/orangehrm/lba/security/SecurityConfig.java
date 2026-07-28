package com.orangehrm.lba.security;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;
import org.springframework.security.config.Customizer;
import org.springframework.security.config.annotation.web.builders.HttpSecurity;
import org.springframework.security.config.http.SessionCreationPolicy;
import org.springframework.security.web.SecurityFilterChain;

@Configuration
public class SecurityConfig {

  @Bean
  SecurityFilterChain securityFilterChain(HttpSecurity http) throws Exception {
    // Baseline: allow all authenticated (HTTP Basic for local), enforce role checks in controllers/services.
    // Production OrangeHRM integration should replace this with platform security.
    http.csrf(csrf -> csrf.disable());
    http.sessionManagement(sm -> sm.sessionCreationPolicy(SessionCreationPolicy.STATELESS));
    http.authorizeHttpRequests(authz -> authz.anyRequest().authenticated());
    http.httpBasic(Customizer.withDefaults());
    return http.build();
  }
}
