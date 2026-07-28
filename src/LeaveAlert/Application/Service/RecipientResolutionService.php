<?php

declare(strict_types=1);

namespace App\LeaveAlert\Application\Service;

use App\LeaveAlert\Domain\Enum\RecipientType;
use App\Pim\Domain\Entity\Employee;
use App\Security\Domain\Entity\User;
use App\Security\Infrastructure\Repository\UserRepository;

/**
 * Converts configured recipient types into concrete application users.
 *
 * Requirements: LBA-FR-009, LBA-FR-023.
 */
final class RecipientResolutionService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * Resolves the distinct set of active users that must receive an in-app
     * notification for an alert raised against the supplied employee.
     *
     * @param list<RecipientType> $recipientTypes
     *
     * @return list<User>
     */
    public function resolve(array $recipientTypes, Employee $employee): array
    {
        /** @var array<int, User> $resolved */
        $resolved = [];

        foreach ($recipientTypes as $recipientType) {
            foreach ($this->resolveSingle($recipientType, $employee) as $user) {
                $userId = $user->getId();

                if ($userId !== null) {
                    $resolved[$userId] = $user;
                }
            }
        }

        return array_values($resolved);
    }

    /**
     * @return list<User>
     */
    private function resolveSingle(RecipientType $recipientType, Employee $employee): array
    {
        $roleName = $recipientType->roleName();

        if ($roleName !== null) {
            return $this->userRepository->findActiveByRoleName($roleName);
        }

        // RecipientType::EMPLOYEE resolves to the alert subject employee's user.
        $employeeUser = $this->userRepository->findActiveByEmployee($employee);

        return $employeeUser === null ? [] : [$employeeUser];
    }
}
