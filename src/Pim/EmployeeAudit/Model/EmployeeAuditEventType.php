<?php

declare(strict_types=1);

namespace App\Pim\EmployeeAudit\Model;

enum EmployeeAuditEventType: string
{
    case Create = 'Create';
    case Update = 'Update';
    case Delete = 'Delete';

    public static function fromString(string $eventType): self
    {
        return match ($eventType) {
            self::Create->value => self::Create,
            self::Update->value => self::Update,
            self::Delete->value => self::Delete,
            default => throw new \InvalidArgumentException(sprintf('Unsupported employee audit event type "%s".', $eventType)),
        };
    }
}