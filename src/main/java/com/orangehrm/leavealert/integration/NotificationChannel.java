package com.orangehrm.leavealert.integration;

import java.util.Map;

public interface NotificationChannel {

    void send(String recipientId, String subject, String body, Map<String, String> metadata);
}
