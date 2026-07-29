<?php

declare(strict_types=1);

namespace App\Security;

final readonly class AuditAuthorizationService
{
    /** @param list<string> $authorizedRoles */
    public function __construct(
        private CurrentUserContext $currentUserContext,
        private array $authorizedRoles = ['ROLE_HR_ADMIN', 'ROLE_SYSTEM_ADMIN'],
    ) {
    }

    public function isAuthorized(): bool
    {
        if (!$this->currentUserContext->isAuthenticated()) {
            return false;
        }

        return array_intersect($this->authorizedRoles, $this->currentUserContext->getRoles()) !== [];
    }
}
