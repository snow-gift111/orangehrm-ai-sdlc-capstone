package com.orangehrm.lba.api.dto;

import com.orangehrm.lba.domain.model.RecipientType;
import jakarta.validation.constraints.NotNull;

public class RecipientDto {
  @NotNull private RecipientType type;
  private Long roleId;

  public RecipientType getType() {
    return type;
  }

  public void setType(RecipientType type) {
    this.type = type;
  }

  public Long getRoleId() {
    return roleId;
  }

  public void setRoleId(Long roleId) {
    this.roleId = roleId;
  }
}
