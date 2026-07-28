package com.orangehrm.lba.integration.identity;

import java.util.List;
import org.springframework.boot.autoconfigure.condition.ConditionalOnProperty;
import org.springframework.stereotype.Component;

/**
 * Development stub.
 *
 * <p>Enabled when lba.integration.identity.stub=true.
 */
@Component
@ConditionalOnProperty(name = "lba.integration.identity.stub", havingValue = "true", matchIfMissing = true)
public class InMemoryIdentityProvider implements IdentityProvider {

  @Override
  public String getUserIdForEmployee(Long employeeId) {
    if (employeeId == null) {
      return null;
    }
    return "emp" + employeeId;
  }

  @Override
  public List<String> getHrUserIds() {
    return List.of("hradmin");
  }

  @Override
  public List<String> getUserIdsForRole(Long roleId) {
    if (roleId == null) {
      return List.of();
    }
    return List.of("role" + roleId + "_user1");
  }

  @Override
  public String getEmployeeDisplayName(Long employeeId) {
    if (employeeId == null) {
      return "Unknown Employee";
    }
    return "Employee " + employeeId;
  }

  @Override
  public String getLeaveTypeName(Long leaveTypeId) {
    if (leaveTypeId == null) {
      return "Unknown Leave Type";
    }
    return "LeaveType " + leaveTypeId;
  }
}
