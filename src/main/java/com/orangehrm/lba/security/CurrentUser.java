package com.orangehrm.lba.security;

import java.util.Set;

/**
 * Minimal authenticated user model.
 *
 * <p>In a real OrangeHRM integration, this would be backed by the platform's auth/session model.
 */
public record CurrentUser(String userId, Set<String> roles, Long employeeId) {
  public boolean hasRole(String role) {
    return roles != null && roles.contains(role);
  }
}
