<?php

declare(strict_types=1);

namespace App\Shared\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Raised when an authenticated user is not authorized for the requested action
 * or resource (HTTP 403).
 *
 * The message intentionally avoids disclosing protected employee data.
 */
final class AccessDeniedException extends ApplicationException
{
    public function __construct(string $message = 'You are not authorized to perform this action.')
    {
        parent::__construct($message, 'ACCESS_DENIED', Response::HTTP_FORBIDDEN);
    }
}
