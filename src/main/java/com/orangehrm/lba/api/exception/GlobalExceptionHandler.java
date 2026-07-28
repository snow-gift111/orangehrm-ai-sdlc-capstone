package com.orangehrm.lba.api.exception;

import jakarta.servlet.http.HttpServletRequest;
import java.time.Instant;
import java.util.List;
import java.util.stream.Collectors;
import org.springframework.dao.DataIntegrityViolationException;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.validation.FieldError;
import org.springframework.web.bind.MethodArgumentNotValidException;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.bind.annotation.RestControllerAdvice;

@RestControllerAdvice
public class GlobalExceptionHandler {

  @ExceptionHandler(MethodArgumentNotValidException.class)
  public ResponseEntity<ApiError> handleValidation(
      MethodArgumentNotValidException ex, HttpServletRequest request) {
    ApiError error = baseError(HttpStatus.BAD_REQUEST, "Validation failed", request);
    List<ApiError.FieldViolation> violations =
        ex.getBindingResult().getFieldErrors().stream()
            .map(this::toViolation)
            .collect(Collectors.toList());
    error.setFieldViolations(violations);
    return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(error);
  }

  @ExceptionHandler(DataIntegrityViolationException.class)
  public ResponseEntity<ApiError> handleDataIntegrity(
      DataIntegrityViolationException ex, HttpServletRequest request) {
    // Used for uniqueness constraints (e.g., rule name, suppression unique key)
    ApiError error = baseError(HttpStatus.CONFLICT, "Data integrity violation", request);
    error.setMessage(ex.getMostSpecificCause() != null ? ex.getMostSpecificCause().getMessage() : ex.getMessage());
    return ResponseEntity.status(HttpStatus.CONFLICT).body(error);
  }

  @ExceptionHandler(IllegalArgumentException.class)
  public ResponseEntity<ApiError> handleIllegalArgument(
      IllegalArgumentException ex, HttpServletRequest request) {
    ApiError error = baseError(HttpStatus.BAD_REQUEST, ex.getMessage(), request);
    return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(error);
  }

  @ExceptionHandler(ForbiddenException.class)
  public ResponseEntity<ApiError> handleForbidden(ForbiddenException ex, HttpServletRequest request) {
    ApiError error = baseError(HttpStatus.FORBIDDEN, ex.getMessage(), request);
    return ResponseEntity.status(HttpStatus.FORBIDDEN).body(error);
  }

  @ExceptionHandler(NotFoundException.class)
  public ResponseEntity<ApiError> handleNotFound(NotFoundException ex, HttpServletRequest request) {
    ApiError error = baseError(HttpStatus.NOT_FOUND, ex.getMessage(), request);
    return ResponseEntity.status(HttpStatus.NOT_FOUND).body(error);
  }

  @ExceptionHandler(Exception.class)
  public ResponseEntity<ApiError> handleGeneric(Exception ex, HttpServletRequest request) {
    ApiError error = baseError(HttpStatus.INTERNAL_SERVER_ERROR, "Internal server error", request);
    error.setMessage(ex.getMessage());
    return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(error);
  }

  private ApiError baseError(HttpStatus status, String message, HttpServletRequest request) {
    ApiError error = new ApiError();
    error.setTimestamp(Instant.now());
    error.setStatus(status.value());
    error.setError(status.getReasonPhrase());
    error.setMessage(message);
    error.setPath(request.getRequestURI());
    return error;
  }

  private ApiError.FieldViolation toViolation(FieldError fieldError) {
    return new ApiError.FieldViolation(fieldError.getField(), fieldError.getDefaultMessage());
  }
}
