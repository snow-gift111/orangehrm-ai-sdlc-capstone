package com.orangehrm.lba.api.controller;

import com.orangehrm.lba.api.dto.RuleCreateRequest;
import com.orangehrm.lba.api.dto.RuleResponse;
import com.orangehrm.lba.api.dto.RuleStatusPatchRequest;
import com.orangehrm.lba.api.dto.RuleUpdateRequest;
import com.orangehrm.lba.service.RuleService;
import jakarta.validation.Valid;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.servlet.support.ServletUriComponentsBuilder;

import java.net.URI;
import java.util.List;

@RestController
@RequestMapping("/api/v1/leave-balance-alert/rules")
public class LeaveBalanceAlertRuleController {

    private final RuleService ruleService;

    public LeaveBalanceAlertRuleController(RuleService ruleService) {
        this.ruleService = ruleService;
    }

    @PostMapping
    @PreAuthorize("hasAuthority(@lbaSecurity.ruleManageAuthority())")
    public ResponseEntity<RuleResponse> create(@Valid @RequestBody RuleCreateRequest req) {
        RuleResponse created = ruleService.create(req);
        URI location = ServletUriComponentsBuilder.fromCurrentRequest().path("/{id}")
                .buildAndExpand(created.id()).toUri();
        return ResponseEntity.created(location).body(created);
    }

    @PutMapping("/{ruleId}")
    @PreAuthorize("hasAuthority(@lbaSecurity.ruleManageAuthority())")
    public ResponseEntity<RuleResponse> update(@PathVariable long ruleId, @Valid @RequestBody RuleUpdateRequest req) {
        return ResponseEntity.ok(ruleService.update(ruleId, req));
    }

    @GetMapping
    @PreAuthorize("hasAuthority(@lbaSecurity.ruleManageAuthority())")
    public ResponseEntity<List<RuleResponse>> list() {
        return ResponseEntity.ok(ruleService.list());
    }

    @GetMapping("/{ruleId}")
    @PreAuthorize("hasAuthority(@lbaSecurity.ruleManageAuthority())")
    public ResponseEntity<RuleResponse> get(@PathVariable long ruleId) {
        return ResponseEntity.ok(ruleService.get(ruleId));
    }

    @PatchMapping("/{ruleId}/status")
    @PreAuthorize("hasAuthority(@lbaSecurity.ruleManageAuthority())")
    public ResponseEntity<RuleResponse> setStatus(@PathVariable long ruleId, @Valid @RequestBody RuleStatusPatchRequest req) {
        return ResponseEntity.ok(ruleService.setStatus(ruleId, req.status()));
    }

    @DeleteMapping("/{ruleId}")
    @PreAuthorize("hasAuthority(@lbaSecurity.ruleManageAuthority())")
    public ResponseEntity<Void> delete(@PathVariable long ruleId) {
        ruleService.delete(ruleId);
        return ResponseEntity.noContent().build();
    }
}
