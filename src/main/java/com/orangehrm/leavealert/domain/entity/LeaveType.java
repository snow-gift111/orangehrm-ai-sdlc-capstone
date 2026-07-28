package com.orangehrm.leavealert.domain.entity;

import com.orangehrm.leavealert.domain.enums.LeaveUnit;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;
import java.util.Objects;

@Entity
@Table(name = "leave_type")
public class LeaveType {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, unique = true, length = 64)
    private String code;

    @Column(nullable = false)
    private String name;

    @Enumerated(EnumType.STRING)
    @Column(nullable = false, length = 16)
    private LeaveUnit unit;

    @Column(name = "is_active", nullable = false)
    private boolean active = true;

    protected LeaveType() {
    }

    public LeaveType(String code, String name, LeaveUnit unit) {
        this.code = code;
        this.name = name;
        this.unit = unit;
        this.active = true;
    }

    public Long getId() {
        return id;
    }

    public String getCode() {
        return code;
    }

    public void setCode(String code) {
        this.code = code;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public LeaveUnit getUnit() {
        return unit;
    }

    public void setUnit(LeaveUnit unit) {
        this.unit = unit;
    }

    public boolean isActive() {
        return active;
    }

    public void setActive(boolean active) {
        this.active = active;
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) {
            return true;
        }
        if (!(o instanceof LeaveType leaveType)) {
            return false;
        }
        return id != null && Objects.equals(id, leaveType.id);
    }

    @Override
    public int hashCode() {
        return getClass().hashCode();
    }
}
