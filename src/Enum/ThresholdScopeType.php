<?php

declare(strict_types=1);

namespace App\Enum;

enum ThresholdScopeType: string
{
    case GLOBAL = 'GLOBAL';
    case LEAVE_TYPE = 'LEAVE_TYPE';
}
