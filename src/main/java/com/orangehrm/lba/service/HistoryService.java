package com.orangehrm.lba.service;

import com.orangehrm.lba.api.dto.HistoryEventResponse;
import com.orangehrm.lba.domain.entity.AlertEventEntity;
import com.orangehrm.lba.repository.AlertEventRecipientRepository;
import com.orangehrm.lba.repository.AlertEventRepository;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Service
public class HistoryService {

    private final AlertEventRepository eventRepository;
    private final AlertEventRecipientRepository recipientRepository;

    public HistoryService(AlertEventRepository eventRepository, AlertEventRecipientRepository recipientRepository) {
        this.eventRepository = eventRepository;
        this.recipientRepository = recipientRepository;
    }

    @Transactional(readOnly = true)
    public Page<HistoryEventResponse> list(int page, int pageSize) {
        Page<AlertEventEntity> events = eventRepository.findAllByOrderByEvaluatedAtDesc(PageRequest.of(page, pageSize));

        return events.map(e -> {
            List<String> recipients = recipientRepository.findAllByEventId(e.getId()).stream()
                    .map(r -> r.getRecipientType() + ":" + (r.getRecipientEmail() == null ? "" : r.getRecipientEmail()) + " (" + r.getDeliveryStatus() + ")")
                    .toList();

            // Display names require integration with PIM; currently not available.
            return new HistoryEventResponse(
                    e.getId(),
                    e.getEmployeeId(),
                    null,
                    e.getLeaveTypeId(),
                    null,
                    e.getBalanceSnapshot(),
                    e.getRule().getId(),
                    e.getStatus(),
                    e.getEvaluatedAt(),
                    e.getSentAt(),
                    recipients
            );
        });
    }
}
