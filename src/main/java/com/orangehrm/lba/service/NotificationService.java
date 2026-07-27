package com.orangehrm.lba.service;

import com.orangehrm.lba.config.LbaProperties;
import com.orangehrm.lba.domain.entity.AlertEventEntity;
import com.orangehrm.lba.domain.entity.AlertEventRecipientEntity;
import com.orangehrm.lba.domain.enums.AlertEventStatus;
import com.orangehrm.lba.domain.enums.RecipientDeliveryStatus;
import com.orangehrm.lba.integration.EmailProvider;
import com.orangehrm.lba.integration.LeaveBalanceProvider;
import com.orangehrm.lba.repository.AlertEventRecipientRepository;
import com.orangehrm.lba.repository.AlertEventRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.time.Instant;
import java.util.List;

@Service
public class NotificationService {

    private final EmailProvider emailProvider;
    private final LbaProperties props;
    private final AlertEventRepository eventRepository;
    private final AlertEventRecipientRepository recipientRepository;

    public NotificationService(EmailProvider emailProvider,
                               LbaProperties props,
                               AlertEventRepository eventRepository,
                               AlertEventRecipientRepository recipientRepository) {
        this.emailProvider = emailProvider;
        this.props = props;
        this.eventRepository = eventRepository;
        this.recipientRepository = recipientRepository;
    }

    @Transactional
    public void sendEvent(AlertEventEntity event,
                          LeaveBalanceProvider.EmployeeRef employee,
                          LeaveBalanceProvider.LeaveTypeRef leaveType,
                          List<AlertEventRecipientEntity> recipients) {

        String subject = props.email().subjectPrefix() + "Leave balance alert";
        String body = renderBody(employee, leaveType, event);

        boolean anySent = false;
        boolean anyDeliverable = false;

        for (AlertEventRecipientEntity r : recipients) {
            if (r.getRecipientEmail() == null || r.getRecipientEmail().isBlank()) {
                r.setDeliveryStatus(RecipientDeliveryStatus.NOT_DELIVERABLE);
                if (r.getFailureReason() == null) {
                    r.setFailureReason("Recipient email not available");
                }
                continue;
            }
            anyDeliverable = true;
            try {
                emailProvider.send(r.getRecipientEmail(), subject, body);
                r.setDeliveryStatus(RecipientDeliveryStatus.SENT);
                r.setFailureReason(null);
                anySent = true;
            } catch (Exception ex) {
                r.setDeliveryStatus(RecipientDeliveryStatus.FAILED);
                r.setFailureReason(ex.getMessage());
            }
        }

        recipientRepository.saveAll(recipients);

        if (anySent) {
            event.setStatus(AlertEventStatus.SENT);
            event.setSentAt(Instant.now());
            event.setFailureReason(null);
        } else if (!anyDeliverable) {
            event.setStatus(AlertEventStatus.NOT_DELIVERABLE);
            event.setFailureReason("No deliverable recipients");
        } else {
            event.setStatus(AlertEventStatus.FAILED);
            event.setFailureReason("All email sends failed");
        }

        eventRepository.save(event);
    }

    private String renderBody(LeaveBalanceProvider.EmployeeRef employee,
                              LeaveBalanceProvider.LeaveTypeRef leaveType,
                              AlertEventEntity event) {
        // Data minimization per NFR-007: include only essential details.
        return "Leave Balance Alert\n\n" +
                "Employee: " + employee.displayName() + " (ID: " + employee.employeeId() + ")\n" +
                "Leave Type: " + leaveType.name() + "\n" +
                "Current Balance: " + event.getBalanceSnapshot() + "\n" +
                "Condition: " + event.getRule().getOperator() + " " + event.getRule().getThresholdValue() + "\n" +
                "\nThis is an automated notification from OrangeHRM.";
    }
}
