package com.orangehrm.leave.service;

import com.orangehrm.common.NotFoundException;
import com.orangehrm.leave.domain.AdjustmentKind;
import com.orangehrm.leave.domain.LeaveBalance;
import com.orangehrm.leave.domain.LeaveBalanceAdjustment;
import com.orangehrm.leave.repo.LeaveBalanceAdjustmentRepository;
import com.orangehrm.leave.repo.LeaveBalanceRepository;
import com.orangehrm.leave.repo.LeaveTypeRepository;
import com.orangehrm.pim.EmployeeRepository;
import com.orangehrm.user.AppUserRepository;
import java.math.BigDecimal;
import java.time.Clock;
import java.time.Instant;
import java.time.LocalDate;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@RequiredArgsConstructor
public class LeaveBalanceAdjustmentService {

  private final LeaveBalanceRepository balanceRepository;
  private final LeaveBalanceAdjustmentRepository adjustmentRepository;
  private final LeaveTypeRepository leaveTypeRepository;
  private final EmployeeRepository employeeRepository;
  private final AppUserRepository userRepository;
  private final AlertEvaluationService evaluationService;
  private final Clock clock;

  @Transactional
  public LeaveBalanceAdjustment applyDelta(Long employeeId,
                                          Long leaveTypeId,
                                          BigDecimal delta,
                                          String reason,
                                          LocalDate effectiveDate,
                                          String createdByUsername) {

    employeeRepository.findById(employeeId).orElseThrow(() -> new NotFoundException("Employee not found"));
    var leaveType = leaveTypeRepository.findById(leaveTypeId).orElseThrow(() -> new NotFoundException("Leave type not found"));
    var createdBy = userRepository.findByUsername(createdByUsername).orElseThrow(() -> new NotFoundException("User not found"));

    Instant now = Instant.now(clock);

    LeaveBalance balance = balanceRepository.findByEmployeeIdAndLeaveType_Id(employeeId, leaveTypeId)
        .orElseGet(() -> {
          LeaveBalance lb = new LeaveBalance();
          lb.setEmployeeId(employeeId);
          lb.setLeaveType(leaveType);
          lb.setBalance(BigDecimal.ZERO);
          lb.setCreatedAt(now);
          lb.setUpdatedAt(now);
          lb.setLastUpdatedAt(now);
          return lb;
        });

    BigDecimal newBalance = balance.getBalance().add(delta);
    balance.setBalance(newBalance);
    balance.setLastUpdatedAt(now);
    balance.setUpdatedAt(now);
    balanceRepository.save(balance);

    LeaveBalanceAdjustment adj = new LeaveBalanceAdjustment();
    adj.setEmployeeId(employeeId);
    adj.setLeaveTypeId(leaveTypeId);
    adj.setAdjustmentKind(AdjustmentKind.DELTA);
    adj.setDelta(delta);
    adj.setNewBalance(newBalance);
    adj.setReason(reason);
    adj.setEffectiveDate(effectiveDate);
    adj.setCreatedByUserId(createdBy.getId());
    adj.setCreatedAt(now);

    LeaveBalanceAdjustment saved = adjustmentRepository.save(adj);

    evaluationService.evaluateOnChange(employeeId, leaveTypeId);

    return saved;
  }
}
