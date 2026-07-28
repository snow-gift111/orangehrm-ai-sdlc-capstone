package com.orangehrm.lba.security;

import org.springframework.stereotype.Service;

@Service
public class RbacService {
  public static final String ROLE_HR_ADMIN = "ROLE_HR_ADMIN";

  public boolean isHrAdmin(CurrentUser user) {
    return user != null && user.hasRole(ROLE_HR_ADMIN);
  }
}
