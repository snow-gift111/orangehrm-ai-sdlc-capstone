package com.orangehrm.leavealert.api;

import com.orangehrm.leavealert.api.dto.AlertEventResponse;
import com.orangehrm.leavealert.api.dto.MyAlertEventResponse;
import com.orangehrm.leavealert.service.AlertHistoryService;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/leave-balance-alert")
public class AlertHistoryController {

    private final AlertHistoryService alertHistoryService;

    public AlertHistoryController(AlertHistoryService alertHistoryService) {
        this.alertHistoryService = alertHistoryService;
    }

    @GetMapping("/alerts")
    public Page<AlertEventResponse> listAll(@RequestParam(defaultValue = "0") int page,
                                           @RequestParam(defaultValue = "20") int pageSize) {
        Pageable pageable = PageRequest.of(Math.max(0, page), Math.min(200, Math.max(1, pageSize)));
        return alertHistoryService.listAll(pageable);
    }

    @GetMapping("/my/alerts")
    public Page<MyAlertEventResponse> listMine(@RequestParam(defaultValue = "0") int page,
                                              @RequestParam(defaultValue = "20") int pageSize) {
        Pageable pageable = PageRequest.of(Math.max(0, page), Math.min(200, Math.max(1, pageSize)));
        return alertHistoryService.listMine(pageable);
    }
}
