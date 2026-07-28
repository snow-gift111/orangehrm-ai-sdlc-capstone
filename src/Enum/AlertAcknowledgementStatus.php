<?php

declare(strict_types=1);

namespace App\Enum;

enum AlertAcknowledgementStatus: string
{
    case UNACKNOWLEDGED = 'UNACKNOWLEDGED';
    case ACKNOWLEDGED = 'ACKNOWLEDGED';
}
