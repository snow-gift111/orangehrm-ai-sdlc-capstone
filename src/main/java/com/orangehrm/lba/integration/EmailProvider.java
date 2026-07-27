package com.orangehrm.lba.integration;

public interface EmailProvider {
    void send(String to, String subject, String body);
}
