<?php

declare(strict_types=1);

namespace App\Enum;

enum AlertLifecycleStatus: string
{
    case ACTIVE = 'ACTIVE';
    case RESOLVED = 'RESOLVED';
}
