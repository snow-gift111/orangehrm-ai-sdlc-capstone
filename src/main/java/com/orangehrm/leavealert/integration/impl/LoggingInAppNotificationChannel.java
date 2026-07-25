package com.orangehrm.leavealert.integration.impl;

import com.orangehrm.leavealert.integration.NotificationChannel;
import java.util.Map;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.context.annotation.Primary;
import org.springframework.stereotype.Component;

/**
 * In-app notification channel baseline.
 *
 * In Sprint 1 we implement at least one delivery channel. This implementation logs notifications.
 * Replace with a real in-app notification/inbox integration if available.
 */
@Component
@Primary
public class LoggingInAppNotificationChannel implements NotificationChannel {

    private static final Logger log = LoggerFactory.getLogger(LoggingInAppNotificationChannel.class);

    @Override
    public void send(String recipientId, String subject, String body, Map<String, String> metadata) {
        log.info("IN_APP_NOTIFICATION recipient={} subject={} metadata={} body={}"
                , recipientId, subject, metadata, body);
    }
}
