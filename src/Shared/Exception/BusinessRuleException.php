<?php

declare(strict_types=1);

namespace App\Shared\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Raised when a business rule prevents the requested operation (HTTP 409).
 */
final class BusinessRuleException extends ApplicationException
{
    public function __construct(string $message, string $errorCode = 'BUSINESS_RULE_VIOLATION')
    {
        parent::__construct($message, $errorCode, Response::HTTP_CONFLICT);
    }

    public static function employeeDeleted(): self
    {
        return new self(
            'Leave balance cannot be maintained for a deleted employee record.',
            'EMPLOYEE_DELETED'
        );
    }
}
