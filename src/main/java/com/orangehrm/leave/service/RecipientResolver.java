package com.orangehrm.leave.service;

import com.orangehrm.leave.domain.RecipientType;
import com.orangehrm.pim.Employee;
import com.orangehrm.pim.EmployeeRepository;
import com.orangehrm.security.AppRole;
import com.orangehrm.user.AppUser;
import com.orangehrm.user.AppUserRepository;
import java.util.EnumSet;
import java.util.HashSet;
import java.util.List;
import java.util.Set;
import lombok.RequiredArgsConstructor;
import lombok.extern.slf4j.Slf4j;
import org.springframework.stereotype.Component;

@Component
@RequiredArgsConstructor
@Slf4j
public class RecipientResolver {

  private final AppUserRepository userRepository;
  private final EmployeeRepository employeeRepository;

  public Set<AppUser> resolveRecipients(Long employeeId, Set<RecipientType> recipientTypes) {
    Set<AppUser> recipients = new HashSet<>();

    EnumSet<RecipientType> types = recipientTypes == null || recipientTypes.isEmpty()
        ? EnumSet.of(RecipientType.EMPLOYEE)
        : EnumSet.copyOf(recipientTypes);

    if (types.contains(RecipientType.EMPLOYEE)) {
      // Minimal mapping: employee username assumed to be "employee{employeeId}" if present.
      // In real system, user<->employee mapping would exist.
      String assumedUsername = "employee" + employeeId;
      userRepository.findByUsername(assumedUsername).ifPresentOrElse(recipients::add, () -> {
        log.info("Employee recipient user not found for employeeId={}, assumedUsername={}", employeeId, assumedUsername);
      });
    }

    if (types.contains(RecipientType.HR_ADMIN)) {
      List<AppUser> hrAdmins = userRepository.findAllByRole(AppRole.HR_ADMIN);
      recipients.addAll(hrAdmins);
    }

    if (types.contains(RecipientType.MANAGER)) {
      // Manager relationship not defined in Sprint 1. Skip without failing.
      Employee emp = employeeRepository.findById(employeeId).orElse(null);
      log.info("Manager recipient type requested but manager mapping unavailable. employeeId={}, employee={}",
          employeeId, emp == null ? null : emp.getEmployeeNumber());
    }

    return recipients;
  }
}
