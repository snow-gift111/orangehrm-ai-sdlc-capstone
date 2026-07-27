package com.orangehrm.lba.domain.enums;

import java.math.BigDecimal;

public enum ThresholdOperator {
    LE,
    LT,
    EQ,
    GE,
    GT;

    public boolean matches(BigDecimal value, BigDecimal threshold) {
        int c = value.compareTo(threshold);
        return switch (this) {
            case LE -> c <= 0;
            case LT -> c < 0;
            case EQ -> c == 0;
            case GE -> c >= 0;
            case GT -> c > 0;
        };
    }
}
