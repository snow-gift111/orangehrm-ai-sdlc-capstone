<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class AuditChangeDetailDto
{
    public function __construct(
        public string $fieldName,
        public string $fieldLabel,
        public ?string $previousValue,
        public ?string $newValue,
    ) {
    }

    /** @return array<string, string|null> */
    public function toArray(): array
    {
        return [
            'fieldName' => $this->fieldName,
            'fieldLabel' => $this->fieldLabel,
            'previousValue' => $this->previousValue,
            'newValue' => $this->newValue,
        ];
    }
}
