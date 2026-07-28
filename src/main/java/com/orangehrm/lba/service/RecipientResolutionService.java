package com.orangehrm.lba.service;

import com.orangehrm.lba.domain.model.RecipientType;
import com.orangehrm.lba.integration.identity.IdentityProvider;
import com.orangehrm.lba.integration.org.OrgHierarchyProvider;
import com.orangehrm.lba.persistence.entity.AlertRuleRecipientEntity;
import java.util.ArrayList;
import java.util.LinkedHashSet;
import java.util.List;
import java.util.Objects;
import java.util.Set;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.stereotype.Service;

@Service
public class RecipientResolutionService {
  private static final Logger log = LoggerFactory.getLogger(RecipientResolutionService.class);

  private final OrgHierarchyProvider orgHierarchyProvider;
  private final IdentityProvider identityProvider;

  public RecipientResolutionService(
      OrgHierarchyProvider orgHierarchyProvider, IdentityProvider identityProvider) {
    this.orgHierarchyProvider = orgHierarchyProvider;
    this.identityProvider = identityProvider;
  }

  public List<ResolvedRecipient> resolve(Long employeeId, List<AlertRuleRecipientEntity> recipients) {
    Objects.requireNonNull(employeeId, "employeeId");

    Set<ResolvedRecipient> out = new LinkedHashSet<>();
    for (AlertRuleRecipientEntity r : recipients) {
      if (r.getRecipientType() == null) {
        continue;
      }
      switch (r.getRecipientType()) {
        case EMPLOYEE -> {
          String userId = identityProvider.getUserIdForEmployee(employeeId);
          if (userId != null) {
            out.add(new ResolvedRecipient(userId, RecipientType.EMPLOYEE));
          }
        }
        case MANAGER -> {
          List<String> managerUserIds = orgHierarchyProvider.getManagerUserIdsForEmployee(employeeId);
          if (managerUserIds == null || managerUserIds.isEmpty()) {
            log.warn("No managers found for employeeId={} (manager recipients omitted)", employeeId);
            break;
          }
          for (String managerUserId : managerUserIds) {
            if (managerUserId != null && !managerUserId.isBlank()) {
              out.add(new ResolvedRecipient(managerUserId, RecipientType.MANAGER));
            }
          }
        }
        case HR -> {
          List<String> hrUserIds = identityProvider.getHrUserIds();
          if (hrUserIds != null) {
            for (String hrUserId : hrUserIds) {
              if (hrUserId != null && !hrUserId.isBlank()) {
                out.add(new ResolvedRecipient(hrUserId, RecipientType.HR));
              }
            }
          }
        }
        case ROLE -> {
          if (r.getRecipientRoleId() == null) {
            log.warn("RecipientType=ROLE missing roleId; skipping");
            break;
          }
          List<String> userIds = identityProvider.getUserIdsForRole(r.getRecipientRoleId());
          if (userIds != null) {
            for (String userId : userIds) {
              if (userId != null && !userId.isBlank()) {
                out.add(new ResolvedRecipient(userId, RecipientType.ROLE));
              }
            }
          }
        }
      }
    }

    return new ArrayList<>(out);
  }

  public record ResolvedRecipient(String userId, RecipientType type) {}
}
