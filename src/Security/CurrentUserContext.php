<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class CurrentUserContext
{
    public function __construct(private Security $security)
    {
    }

    public function isAuthenticated(): bool
    {
        return $this->security->getUser() instanceof UserInterface;
    }

    public function getUserId(): ?int
    {
        $user = $this->security->getUser();
        if ($user === null) {
            return null;
        }

        return abs(crc32($user->getUserIdentifier()));
    }

    public function getUsername(): string
    {
        $user = $this->security->getUser();

        return $user?->getUserIdentifier() ?? 'system';
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        return $this->security->getUser()?->getRoles() ?? [];
    }
}
