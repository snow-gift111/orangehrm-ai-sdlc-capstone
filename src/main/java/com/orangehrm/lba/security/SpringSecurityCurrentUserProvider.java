package com.orangehrm.lba.security;

import java.util.Collections;
import java.util.Set;
import java.util.stream.Collectors;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Component;

@Component
public class SpringSecurityCurrentUserProvider implements CurrentUserProvider {

  public static final String EMPLOYEE_ID_CLAIM = "employeeId";

  @Override
  public CurrentUser getCurrentUser() {
    Authentication auth = SecurityContextHolder.getContext().getAuthentication();
    if (auth == null || !auth.isAuthenticated()) {
      return new CurrentUser("anonymous", Collections.emptySet(), null);
    }

    String userId = auth.getName();
    Set<String> roles =
        auth.getAuthorities() == null
            ? Collections.emptySet()
            : auth.getAuthorities().stream().map(GrantedAuthority::getAuthority).collect(Collectors.toSet());

    // Employee ID mapping is platform-specific; left null by default.
    return new CurrentUser(userId, roles, null);
  }
}
