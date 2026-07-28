<?php

declare(strict_types=1);

namespace App\Shared\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Raised when no active authenticated user is present (HTTP 401).
 */
final class UnauthenticatedException extends ApplicationException
{
    public function __construct(string $message = 'Authentication is required.')
    {
        parent::__construct($message, 'UNAUTHENTICATED', Response::HTTP_UNAUTHORIZED);
    }
}
