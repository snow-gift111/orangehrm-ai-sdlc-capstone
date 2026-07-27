package com.orangehrm.lba.job;

import com.orangehrm.lba.config.LbaProperties;
import com.orangehrm.lba.service.EvaluationService;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.scheduling.annotation.Scheduled;
import org.springframework.stereotype.Component;

@Component
public class LeaveBalanceAlertEvaluationJob {

    private static final Logger log = LoggerFactory.getLogger(LeaveBalanceAlertEvaluationJob.class);

    private final EvaluationService evaluationService;
    private final LbaProperties props;

    public LeaveBalanceAlertEvaluationJob(EvaluationService evaluationService, LbaProperties props) {
        this.evaluationService = evaluationService;
        this.props = props;
    }

    @Scheduled(cron = "${lba.scheduler.cron}")
    public void runScheduled() {
        EvaluationService.EvaluationSummary summary = evaluationService.run();
        log.info("LBA evaluation finished jobRunId={} evaluated={} matched={} suppressed={} sendAttempted={} (cron={})",
                summary.jobRunId(),
                summary.evaluatedCount(),
                summary.matchedCount(),
                summary.suppressedCount(),
                summary.sendAttemptedCount(),
                props.scheduler().cron());
    }
}
