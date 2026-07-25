package com.orangehrm.leavealert.security;

import com.orangehrm.leavealert.config.AppProperties;
import java.util.List;
import org.springframework.stereotype.Service;

@Service
public class AuthorizationService {

    private final AppProperties appProperties;

    public AuthorizationService(AppProperties appProperties) {
        this.appProperties = appProperties;
    }

    public void requireHrAdmin() {
        String hrAdminRole = appProperties.getSecurity().getHrAdminRole();
        if (!AuthContext.hasAuthority(hrAdminRole)) {
            throw new ForbiddenException("Requires role: " + hrAdminRole);
        }
    }

    public void requireAlertHistoryViewer() {
        String hrAdminRole = appProperties.getSecurity().getHrAdminRole();
        if (AuthContext.hasAuthority(hrAdminRole)) {
            return;
        }

        List<String> viewerRoles = appProperties.getSecurity().getHrHistoryViewerRolesList().stream()
                .map(String::trim)
                .filter(s -> !s.isBlank())
                .toList();

        boolean ok = viewerRoles.stream().anyMatch(AuthContext::hasAuthority);
        if (!ok) {
            throw new ForbiddenException("Requires HR admin or one of roles: " + viewerRoles);
        }
    }
}
