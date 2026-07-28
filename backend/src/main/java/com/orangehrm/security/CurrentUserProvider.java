package com.orangehrm.security;

import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Component;

/**
 * Provides an application user identifier.
 *
 * NOTE: The approved design references a user table. This repository baseline does not contain the
 * real OrangeHRM auth/PIM modules, so we map the authenticated username to a deterministic numeric id.
 * Replace this with real user-id lookup when integrating with the actual auth module.
 */
@Component
public class CurrentUserProvider {

    public String getUsername() {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        return auth != null ? auth.getName() : null;
    }

    public long getUserId() {
        String username = getUsername();
        if (username == null) {
            return 0L;
        }
        // Deterministic, positive id from username
        return Integer.toUnsignedLong(username.hashCode());
    }
}
