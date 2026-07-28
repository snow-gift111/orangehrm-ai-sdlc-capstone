<?php

declare(strict_types=1);

namespace App\Enum;

enum AlertCondition: string
{
    case LOW_BALANCE = 'LOW_BALANCE';
    case EXHAUSTED = 'EXHAUSTED';
}
