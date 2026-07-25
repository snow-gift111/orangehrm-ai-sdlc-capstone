package com.orangehrm.leavealert.integration;

import java.util.List;

/**
 * Provides access to employees to evaluate.
 *
 * Per approved solution design, the baseline is to evaluate all active employees.
 * The actual OrangeHRM integration can replace this with a DB/service-backed implementation.
 */
public interface EmployeeDirectoryProvider {

    List<String> getActiveEmployeeIds();
}
