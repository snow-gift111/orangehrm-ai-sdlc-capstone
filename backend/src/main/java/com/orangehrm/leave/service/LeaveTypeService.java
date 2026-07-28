package com.orangehrm.leave.service;

import com.orangehrm.common.exception.NotFoundException;
import com.orangehrm.leave.api.dto.LeaveTypeCreateRequest;
import com.orangehrm.leave.api.dto.LeaveTypeResponse;
import com.orangehrm.leave.api.dto.LeaveTypeUpdateRequest;
import com.orangehrm.leave.domain.LeaveTypeEntity;
import com.orangehrm.leave.repo.LeaveTypeRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;

@Service
public class LeaveTypeService {

    private final LeaveTypeRepository leaveTypeRepository;

    public LeaveTypeService(LeaveTypeRepository leaveTypeRepository) {
        this.leaveTypeRepository = leaveTypeRepository;
    }

    @Transactional(readOnly = true)
    public List<LeaveTypeResponse> listActive() {
        return leaveTypeRepository.findAllByActiveTrueOrderByNameAsc().stream()
                .map(this::toResponse)
                .toList();
    }

    @Transactional(readOnly = true)
    public List<LeaveTypeResponse> listAll() {
        return leaveTypeRepository.findAll().stream()
                .sorted((a, b) -> a.getName().compareToIgnoreCase(b.getName()))
                .map(this::toResponse)
                .toList();
    }

    @Transactional
    public LeaveTypeResponse create(LeaveTypeCreateRequest request) {
        LeaveTypeEntity entity = new LeaveTypeEntity();
        entity.setName(request.name().trim());
        entity.setUnit(request.unit());
        entity.setActive(request.isActive() == null || request.isActive());
        return toResponse(leaveTypeRepository.save(entity));
    }

    @Transactional
    public LeaveTypeResponse update(long id, LeaveTypeUpdateRequest request) {
        LeaveTypeEntity entity = leaveTypeRepository.findById(id)
                .orElseThrow(() -> new NotFoundException("Leave type not found"));
        entity.setName(request.name().trim());
        entity.setUnit(request.unit());
        if (request.isActive() != null) {
            entity.setActive(request.isActive());
        }
        return toResponse(leaveTypeRepository.save(entity));
    }

    @Transactional
    public LeaveTypeResponse deactivate(long id) {
        LeaveTypeEntity entity = leaveTypeRepository.findById(id)
                .orElseThrow(() -> new NotFoundException("Leave type not found"));
        entity.setActive(false);
        return toResponse(leaveTypeRepository.save(entity));
    }

    @Transactional(readOnly = true)
    public LeaveTypeEntity requireActiveEntity(long leaveTypeId) {
        LeaveTypeEntity entity = leaveTypeRepository.findById(leaveTypeId)
                .orElseThrow(() -> new NotFoundException("Leave type not found"));
        if (!entity.isActive()) {
            throw new com.orangehrm.common.exception.BadRequestException("Leave type is inactive");
        }
        return entity;
    }

    private LeaveTypeResponse toResponse(LeaveTypeEntity e) {
        return new LeaveTypeResponse(
                e.getId(),
                e.getName(),
                e.getUnit(),
                e.isActive(),
                e.getCreatedAt(),
                e.getUpdatedAt()
        );
    }
}
