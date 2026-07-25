package com.orangehrm.leavealert.job;

import com.orangehrm.leavealert.config.AppProperties;
import com.orangehrm.leavealert.service.AlertEvaluationService;
import java.time.Instant;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.scheduling.annotation.Scheduled;
import org.springframework.stereotype.Component;

@Component
public class LeaveBalanceAlertEvaluationJob {

    private static final Logger log = LoggerFactory.getLogger(LeaveBalanceAlertEvaluationJob.class);

    private final AppProperties appProperties;
    private final AlertEvaluationService alertEvaluationService;

    public LeaveBalanceAlertEvaluationJob(AppProperties appProperties, AlertEvaluationService alertEvaluationService) {
        this.appProperties = appProperties;
        this.alertEvaluationService = alertEvaluationService;
    }

    @Scheduled(cron = "${app.leaveBalanceAlert.evaluationCron:0 0 2 * * *}")
    public void run() {
        Instant asOf = Instant.now();
        log.info("LeaveBalanceAlertEvaluationJob started asOf={}", asOf);
        AlertEvaluationService.EvaluationResult result = alertEvaluationService.evaluate(asOf);
        log.info("LeaveBalanceAlertEvaluationJob finished evaluatedPairs={} breaches={} eventsCreated={}"
                , result.evaluatedPairs(), result.breaches(), result.eventsCreated());
    }
}
