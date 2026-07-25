package com.orangehrm.leavealert.api;

import com.orangehrm.leavealert.security.AuthorizationService;
import com.orangehrm.leavealert.service.AlertEvaluationService;
import java.time.Instant;
import org.springframework.http.HttpStatus;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.ResponseStatus;
import org.springframework.web.bind.annotation.RestController;

/**
 * Non-business-facing endpoint to trigger an evaluation run.
 * Allowed for HR admin to support demos/ops.
 */
@RestController
@RequestMapping("/api/leave-balance-alert/internal")
public class InternalEvaluationController {

    private final AuthorizationService authorizationService;
    private final AlertEvaluationService alertEvaluationService;

    public InternalEvaluationController(AuthorizationService authorizationService,
                                        AlertEvaluationService alertEvaluationService) {
        this.authorizationService = authorizationService;
        this.alertEvaluationService = alertEvaluationService;
    }

    @PostMapping("/evaluate")
    @ResponseStatus(HttpStatus.ACCEPTED)
    public AlertEvaluationService.EvaluationResult evaluateNow() {
        authorizationService.requireHrAdmin();
        return alertEvaluationService.evaluate(Instant.now());
    }
}
