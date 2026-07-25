package com.orangehrm.leavealert.integration.impl;

import com.orangehrm.leavealert.integration.EmployeeDirectoryProvider;
import java.util.List;
import org.springframework.context.annotation.Primary;
import org.springframework.stereotype.Component;

/**
 * Demo/local implementation.
 * Replace with real OrangeHRM employee directory integration.
 */
@Component
@Primary
public class InMemoryEmployeeDirectoryProvider implements EmployeeDirectoryProvider {

    @Override
    public List<String> getActiveEmployeeIds() {
        return List.of("e001", "e002", "e003");
    }
}
