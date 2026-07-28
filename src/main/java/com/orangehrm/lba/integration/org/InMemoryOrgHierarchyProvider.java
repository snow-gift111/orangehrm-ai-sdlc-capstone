package com.orangehrm.lba.integration.org;

import java.util.List;
import org.springframework.boot.autoconfigure.condition.ConditionalOnProperty;
import org.springframework.stereotype.Component;

/**
 * Development stub.
 *
 * <p>Enabled when lba.integration.org.stub=true.
 */
@Component
@ConditionalOnProperty(name = "lba.integration.org.stub", havingValue = "true", matchIfMissing = true)
public class InMemoryOrgHierarchyProvider implements OrgHierarchyProvider {

  @Override
  public List<String> getManagerUserIdsForEmployee(Long employeeId) {
    // Minimal deterministic mapping for demo/dev.
    if (employeeId == null) {
      return List.of();
    }
    if (employeeId.equals(1001L) || employeeId.equals(1002L)) {
      return List.of("manager1");
    }
    return List.of();
  }
}
