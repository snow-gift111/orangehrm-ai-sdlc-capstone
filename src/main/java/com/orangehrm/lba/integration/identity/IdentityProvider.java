package com.orangehrm.lba.integration.identity;

import java.util.List;

/**
 * Adapter for identity-related lookups.
 *
 * <p>OrangeHRM integration should map employeeId->userId and role/group memberships.
 */
public interface IdentityProvider {

  /** Resolve a user id for the given employee id. */
  String getUserIdForEmployee(Long employeeId);

  /** Resolve user ids for the HR recipient group. */
  List<String> getHrUserIds();

  /** Resolve user ids for a custom role. */
  List<String> getUserIdsForRole(Long roleId);

  /** Reference lookup for employee display name. */
  String getEmployeeDisplayName(Long employeeId);

  /** Reference lookup for leave type name. */
  String getLeaveTypeName(Long leaveTypeId);
}
