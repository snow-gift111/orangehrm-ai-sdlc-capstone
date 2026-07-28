<?php

declare(strict_types=1);

namespace App\Shared\Exception;

/**
 * Base application exception carrying a stable error code and HTTP status.
 */
abstract class ApplicationException extends \RuntimeException
{
    /**
     * @param array<string, string> $fieldErrors
     */
    public function __construct(
        string $message,
        private readonly string $errorCode,
        private readonly int $statusCode,
        private readonly array $fieldErrors = [],
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, string>
     */
    public function getFieldErrors(): array
    {
        return $this->fieldErrors;
    }
}
