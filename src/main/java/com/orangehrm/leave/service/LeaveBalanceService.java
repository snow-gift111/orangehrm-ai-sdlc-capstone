package com.orangehrm.leave.service;

import com.orangehrm.common.NotFoundException;
import com.orangehrm.leave.domain.LeaveBalance;
import com.orangehrm.leave.repo.LeaveBalanceRepository;
import com.orangehrm.leave.repo.LeaveTypeRepository;
import com.orangehrm.pim.EmployeeRepository;
import java.math.BigDecimal;
import java.time.Clock;
import java.time.Instant;
import java.util.List;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@RequiredArgsConstructor
public class LeaveBalanceService {

  private final LeaveBalanceRepository balanceRepository;
  private final LeaveTypeRepository leaveTypeRepository;
  private final EmployeeRepository employeeRepository;
  private final AlertEvaluationService evaluationService;
  private final Clock clock;

  @Transactional
  public LeaveBalance upsertBalance(Long employeeId, Long leaveTypeId, BigDecimal balance) {
    employeeRepository.findById(employeeId).orElseThrow(() -> new NotFoundException("Employee not found"));
    var leaveType = leaveTypeRepository.findById(leaveTypeId).orElseThrow(() -> new NotFoundException("Leave type not found"));

    Instant now = Instant.now(clock);

    LeaveBalance lb = balanceRepository.findByEmployeeIdAndLeaveType_Id(employeeId, leaveTypeId)
        .orElseGet(LeaveBalance::new);

    boolean isNew = lb.getId() == null;
    lb.setEmployeeId(employeeId);
    lb.setLeaveType(leaveType);
    lb.setBalance(balance);
    lb.setLastUpdatedAt(now);
    if (isNew) {
      lb.setCreatedAt(now);
    }
    lb.setUpdatedAt(now);

    LeaveBalance saved = balanceRepository.save(lb);

    evaluationService.evaluateOnChange(employeeId, leaveTypeId);

    return saved;
  }

  @Transactional(readOnly = true)
  public List<LeaveBalance> listByEmployee(Long employeeId) {
    employeeRepository.findById(employeeId).orElseThrow(() -> new NotFoundException("Employee not found"));
    return balanceRepository.findAllByEmployeeId(employeeId);
  }
}
