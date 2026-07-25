package com.orangehrm.leavealert.api;

import com.orangehrm.leavealert.api.dto.ApiErrorResponse;
import com.orangehrm.leavealert.security.ForbiddenException;
import jakarta.validation.ConstraintViolationException;
import java.util.List;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.validation.FieldError;
import org.springframework.web.bind.MethodArgumentNotValidException;
import org.springframework.web.bind.annotation.ExceptionHandler;
import org.springframework.web.bind.annotation.RestControllerAdvice;

@RestControllerAdvice
public class GlobalExceptionHandler {

    @ExceptionHandler(MethodArgumentNotValidException.class)
    public ResponseEntity<ApiErrorResponse> handleValidation(MethodArgumentNotValidException ex) {
        List<ApiErrorResponse.FieldError> fieldErrors = ex.getBindingResult().getFieldErrors().stream()
                .map(this::map)
                .toList();

        ApiErrorResponse body = new ApiErrorResponse(
                "VALIDATION_ERROR",
                "Validation failed",
                fieldErrors
        );
        return ResponseEntity.badRequest().body(body);
    }

    @ExceptionHandler(ConstraintViolationException.class)
    public ResponseEntity<ApiErrorResponse> handleConstraint(ConstraintViolationException ex) {
        ApiErrorResponse body = new ApiErrorResponse(
                "VALIDATION_ERROR",
                ex.getMessage(),
                List.of()
        );
        return ResponseEntity.badRequest().body(body);
    }

    @ExceptionHandler(ForbiddenException.class)
    public ResponseEntity<ApiErrorResponse> handleForbidden(ForbiddenException ex) {
        ApiErrorResponse body = new ApiErrorResponse(
                "FORBIDDEN",
                ex.getMessage(),
                List.of()
        );
        return ResponseEntity.status(HttpStatus.FORBIDDEN).body(body);
    }

    @ExceptionHandler(IllegalArgumentException.class)
    public ResponseEntity<ApiErrorResponse> handleBadRequest(IllegalArgumentException ex) {
        ApiErrorResponse body = new ApiErrorResponse(
                "BAD_REQUEST",
                ex.getMessage(),
                List.of()
        );
        return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(body);
    }

    @ExceptionHandler(Exception.class)
    public ResponseEntity<ApiErrorResponse> handleGeneric(Exception ex) {
        ApiErrorResponse body = new ApiErrorResponse(
                "INTERNAL_ERROR",
                "An unexpected error occurred",
                List.of()
        );
        return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(body);
    }

    private ApiErrorResponse.FieldError map(FieldError fe) {
        return new ApiErrorResponse.FieldError(fe.getField(), fe.getDefaultMessage());
    }
}
