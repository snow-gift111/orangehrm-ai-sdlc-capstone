package com.orangehrm.lba.integration.org;

import java.util.List;

/**
 * Adapter to resolve manager relationships for an employee.
 */
public interface OrgHierarchyProvider {

  /**
   * @param employeeId subject employee
   * @return userIds of managers in scope (direct manager(s) or hierarchy as defined by the org)
   */
  List<String> getManagerUserIdsForEmployee(Long employeeId);
}
