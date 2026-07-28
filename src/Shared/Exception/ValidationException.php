<?php

declare(strict_types=1);

namespace App\Shared\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Raised when request input fails business validation (HTTP 400).
 */
final class ValidationException extends ApplicationException
{
    /**
     * @param array<string, string> $fieldErrors
     */
    public function __construct(array $fieldErrors, string $message = 'Request validation failed.')
    {
        parent::__construct($message, 'VALIDATION_ERROR', Response::HTTP_BAD_REQUEST, $fieldErrors);
    }

    public static function forField(string $field, string $message): self
    {
        return new self([$field => $message]);
    }
}
