package com.orangehrm.lba.api.exception;

import java.time.Instant;
import java.util.List;

public class ApiError {
  private Instant timestamp;
  private int status;
  private String error;
  private String message;
  private String path;
  private List<FieldViolation> fieldViolations;

  public Instant getTimestamp() {
    return timestamp;
  }

  public void setTimestamp(Instant timestamp) {
    this.timestamp = timestamp;
  }

  public int getStatus() {
    return status;
  }

  public void setStatus(int status) {
    this.status = status;
  }

  public String getError() {
    return error;
  }

  public void setError(String error) {
    this.error = error;
  }

  public String getMessage() {
    return message;
  }

  public void setMessage(String message) {
    this.message = message;
  }

  public String getPath() {
    return path;
  }

  public void setPath(String path) {
    this.path = path;
  }

  public List<FieldViolation> getFieldViolations() {
    return fieldViolations;
  }

  public void setFieldViolations(List<FieldViolation> fieldViolations) {
    this.fieldViolations = fieldViolations;
  }

  public static class FieldViolation {
    private String field;
    private String message;

    public FieldViolation() {}

    public FieldViolation(String field, String message) {
      this.field = field;
      this.message = message;
    }

    public String getField() {
      return field;
    }

    public void setField(String field) {
      this.field = field;
    }

    public String getMessage() {
      return message;
    }

    public void setMessage(String message) {
      this.message = message;
    }
  }
}
