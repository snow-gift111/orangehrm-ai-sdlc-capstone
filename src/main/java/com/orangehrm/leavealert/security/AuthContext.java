package com.orangehrm.leavealert.security;

import java.util.Collection;
import java.util.Objects;
import java.util.Optional;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.GrantedAuthority;
import org.springframework.security.core.context.SecurityContextHolder;

public final class AuthContext {

    private AuthContext() {
    }

    public static Optional<Authentication> authentication() {
        return Optional.ofNullable(SecurityContextHolder.getContext().getAuthentication());
    }

    public static String requirePrincipalName() {
        return authentication()
                .map(Authentication::getName)
                .filter(n -> !n.isBlank())
                .orElseThrow(() -> new IllegalStateException("No authenticated principal"));
    }

    public static boolean hasAuthority(String authority) {
        Authentication auth = authentication().orElse(null);
        if (auth == null) {
            return false;
        }
        Collection<? extends GrantedAuthority> authorities = auth.getAuthorities();
        if (authorities == null) {
            return false;
        }
        return authorities.stream().map(GrantedAuthority::getAuthority)
                .filter(Objects::nonNull)
                .anyMatch(a -> a.equals(authority) || a.equals("ROLE_" + authority));
    }
}
