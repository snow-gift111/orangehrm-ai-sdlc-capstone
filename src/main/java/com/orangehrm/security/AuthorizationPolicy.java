package com.orangehrm.security;

import com.orangehrm.user.AppUser;
import java.util.Set;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.stereotype.Component;

@Component
public class AuthorizationPolicy {

  public void requireHrAdmin(Authentication authentication) {
    if (!hasRole(authentication, "ROLE_HR_ADMIN")) {
      throw new ForbiddenException("HR admin role required");
    }
  }

  public void requireRecipient(Authentication authentication, AppUser recipientUser) {
    String currentUsername = authentication.getName();
    if (!recipientUser.getUsername().equals(currentUsername)) {
      throw new ForbiddenException("Only the recipient may perform this action");
    }
  }

  public boolean isHrAdmin(Authentication authentication) {
    return hasRole(authentication, "ROLE_HR_ADMIN");
  }

  private boolean hasRole(Authentication authentication, String role) {
    if (authentication == null || authentication.getAuthorities() == null) {
      return false;
    }
    Set<String> roles = authentication.getAuthorities().stream().map(GrantedAuthority::getAuthority).collect(java.util.stream.Collectors.toSet());
    return roles.contains(role);
  }
}
