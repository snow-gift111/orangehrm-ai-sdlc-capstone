package com.orangehrm.leave.service;

import com.orangehrm.common.NotFoundException;
import com.orangehrm.leave.domain.LeaveType;
import com.orangehrm.leave.repo.LeaveTypeRepository;
import java.time.Clock;
import java.time.Instant;
import java.util.List;
import lombok.RequiredArgsConstructor;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

@Service
@RequiredArgsConstructor
public class LeaveTypeService {

  private final LeaveTypeRepository repository;
  private final Clock clock;

  @Transactional
  public LeaveType create(String name, String code, boolean active) {
    LeaveType lt = new LeaveType();
    lt.setName(name.trim());
    lt.setCode(code == null || code.isBlank() ? null : code.trim());
    lt.setActive(active);
    Instant now = Instant.now(clock);
    lt.setCreatedAt(now);
    lt.setUpdatedAt(now);
    return repository.save(lt);
  }

  @Transactional(readOnly = true)
  public List<LeaveType> list() {
    return repository.findAll();
  }

  @Transactional
  public LeaveType update(Long id, String name, String code, Boolean active) {
    LeaveType lt = repository.findById(id).orElseThrow(() -> new NotFoundException("Leave type not found"));

    if (name != null) {
      lt.setName(name.trim());
    }
    if (code != null) {
      lt.setCode(code.isBlank() ? null : code.trim());
    }
    if (active != null) {
      lt.setActive(active);
    }
    lt.setUpdatedAt(Instant.now(clock));
    return repository.save(lt);
  }
}
