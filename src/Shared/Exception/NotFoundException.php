<?php

declare(strict_types=1);

namespace App\Shared\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Raised when a referenced resource does not exist (HTTP 404).
 */
final class NotFoundException extends ApplicationException
{
    public function __construct(string $message = 'The requested resource was not found.')
    {
        parent::__construct($message, 'NOT_FOUND', Response::HTTP_NOT_FOUND);
    }

    public static function employee(): self
    {
        return new self('Employee record was not found.');
    }

    public static function alertRule(): self
    {
        return new self('Leave balance alert rule was not found.');
    }

    public static function leaveBalance(): self
    {
        return new self('Leave balance record was not found.');
    }

    public static function alert(): self
    {
        return new self('Leave balance alert was not found.');
    }
}
