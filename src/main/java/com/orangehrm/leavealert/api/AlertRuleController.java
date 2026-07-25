package com.orangehrm.leavealert.api;

import com.orangehrm.leavealert.api.dto.AlertRuleResponse;
import com.orangehrm.leavealert.api.dto.CreateAlertRuleRequest;
import com.orangehrm.leavealert.api.dto.UpdateRuleStatusRequest;
import com.orangehrm.leavealert.service.AlertRuleService;
import jakarta.validation.Valid;
import java.util.UUID;
import org.springframework.data.domain.Page;
import org.springframework.data.domain.PageRequest;
import org.springframework.data.domain.Pageable;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PatchMapping;
import org.springframework.web.bind.annotation.PathVariable;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.ResponseStatus;
import org.springframework.web.bind.annotation.RestController;

@RestController
@RequestMapping("/api/leave-balance-alert/rules")
public class AlertRuleController {

    private final AlertRuleService alertRuleService;

    public AlertRuleController(AlertRuleService alertRuleService) {
        this.alertRuleService = alertRuleService;
    }

    @PostMapping
    @ResponseStatus(HttpStatus.CREATED)
    public AlertRuleResponse create(@Valid @RequestBody CreateAlertRuleRequest request) {
        return alertRuleService.create(request);
    }

    @GetMapping
    public Page<AlertRuleResponse> list(@RequestParam(defaultValue = "0") int page,
                                       @RequestParam(defaultValue = "20") int pageSize) {
        Pageable pageable = PageRequest.of(Math.max(0, page), Math.min(200, Math.max(1, pageSize)));
        return alertRuleService.list(pageable);
    }

    @PatchMapping("/{ruleId}/status")
    public AlertRuleResponse updateStatus(@PathVariable UUID ruleId,
                                         @Valid @RequestBody UpdateRuleStatusRequest request) {
        return alertRuleService.setEnabled(ruleId, request.enabled());
    }
}
