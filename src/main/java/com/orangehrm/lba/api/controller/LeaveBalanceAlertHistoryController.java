package com.orangehrm.lba.api.controller;

import com.orangehrm.lba.api.dto.HistoryEventResponse;
import com.orangehrm.lba.service.HistoryService;
import org.springframework.data.domain.Page;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/v1/leave-balance-alert/history")
public class LeaveBalanceAlertHistoryController {

    private final HistoryService historyService;

    public LeaveBalanceAlertHistoryController(HistoryService historyService) {
        this.historyService = historyService;
    }

    @GetMapping
    @PreAuthorize("hasAuthority(@lbaSecurity.historyViewAuthority())")
    public ResponseEntity<Page<HistoryEventResponse>> list(
            @RequestParam(defaultValue = "0") int page,
            @RequestParam(defaultValue = "25") int pageSize
    ) {
        return ResponseEntity.ok(historyService.list(page, pageSize));
    }
}
