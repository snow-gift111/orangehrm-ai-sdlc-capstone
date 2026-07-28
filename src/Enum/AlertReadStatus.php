<?php

declare(strict_types=1);

namespace App\Enum;

enum AlertReadStatus: string
{
    case UNREAD = 'UNREAD';
    case READ = 'READ';
}
