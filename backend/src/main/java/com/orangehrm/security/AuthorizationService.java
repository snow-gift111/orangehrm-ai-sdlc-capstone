package com.orangehrm.security;

import org.springframework.security.access.AccessDeniedException;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Service;

@Service
public class AuthorizationService {

    public void requireAuthority(String authority) {
        Authentication auth = SecurityContextHolder.getContext().getAuthentication();
        if (auth == null || auth.getAuthorities() == null) {
            throw new AccessDeniedException("Forbidden");
        }
        boolean has = auth.getAuthorities().stream().anyMatch(a -> authority.equals(a.getAuthority()));
        if (!has) {
            throw new AccessDeniedException("Forbidden");
        }
    }
}
