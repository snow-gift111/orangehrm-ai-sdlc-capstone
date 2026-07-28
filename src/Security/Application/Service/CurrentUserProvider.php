<?php

declare(strict_types=1);

namespace App\Security\Application\Service;

use App\Security\Domain\Entity\User;
use App\Shared\Exception\UnauthenticatedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Provides the currently authenticated application user.
 *
 * All Leave Balance Alert APIs require an authenticated session; unauthenticated
 * requests are rejected with HTTP 401.
 */
final class CurrentUserProvider
{
    public function __construct(private readonly TokenStorageInterface $tokenStorage)
    {
    }

    public function getCurrentUserOrNull(): ?User
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            return null;
        }

        $user = $token->getUser();

        return $user instanceof User ? $user : null;
    }

    /**
     * @throws UnauthenticatedException
     */
    public function getCurrentUser(): User
    {
        $user = $this->getCurrentUserOrNull();

        if ($user === null || !$user->isActive()) {
            throw new UnauthenticatedException();
        }

        return $user;
    }
}
