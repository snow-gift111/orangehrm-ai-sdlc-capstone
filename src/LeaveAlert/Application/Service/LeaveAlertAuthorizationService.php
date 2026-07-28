<?php

declare(strict_types=1);

namespace App\LeaveAlert\Application\Service;

use App\LeaveAlert\Application\Security\VisibilityScope;
use App\Pim\Domain\Entity\Employee;
use App\Security\Domain\Entity\Role;
use App\Security\Domain\Entity\User;
use App\Shared\Exception\AccessDeniedException;
use Psr\Log\LoggerInterface;

/**
 * Centralized authorization for Leave Balance Alert functionality.
 *
 * Requirements: LBA-FR-035, LBA-FR-036, LBA-FR-037, LBA-FR-038.
 *
 * Authorization is always enforced in the backend; frontend hiding is treated as
 * a usability measure only. The service fails securely: any unknown or
 * unmatched combination results in denial.
 */
final class LeaveAlertAuthorizationService
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function isHrAdmin(User $user): bool
    {
        return $user->hasRole(Role::HR_ADMIN);
    }

    public function isHrUser(User $user): bool
    {
        return $user->hasRole(Role::HR_USER);
    }

    public function hasHrVisibility(User $user): bool
    {
        return $this->isHrAdmin($user) || $this->isHrUser($user);
    }

    /**
     * Only HR administrators may create, edit, activate or deactivate rules.
     *
     * @throws AccessDeniedException
     */
    public function assertCanManageAlertRules(User $user): void
    {
        if (!$this->isHrAdmin($user)) {
            $this->denied($user, 'MANAGE_ALERT_RULES');
        }
    }

    /**
     * Only HR administrators may maintain leave balances in this sprint scope.
     *
     * @throws AccessDeniedException
     */
    public function assertCanMaintainLeaveBalance(User $user): void
    {
        if (!$this->isHrAdmin($user)) {
            $this->denied($user, 'MAINTAIN_LEAVE_BALANCE');
        }
    }

    /**
     * HR roles may view balances within HR scope; an employee user may view
     * only their own leave balance.
     *
     * @throws AccessDeniedException
     */
    public function assertCanViewLeaveBalance(User $user, Employee $employee): void
    {
        if ($this->hasHrVisibility($user)) {
            return;
        }

        if ($this->isSelf($user, $employee)) {
            return;
        }

        $this->denied($user, 'VIEW_LEAVE_BALANCE');
    }

    /**
     * HR roles may view alerts within HR scope; an employee user may view only
     * their own alerts.
     *
     * @throws AccessDeniedException
     */
    public function assertCanViewAlertForEmployee(User $user, Employee $employee): void
    {
        if ($this->hasHrVisibility($user)) {
            return;
        }

        if ($this->isSelf($user, $employee)) {
            return;
        }

        $this->denied($user, 'VIEW_LEAVE_ALERT');
    }

    /**
     * Determines the alert visibility scope permitted for the current user.
     *
     * @throws AccessDeniedException when the user has neither HR visibility nor
     *                               a linked employee record
     */
    public function resolveVisibilityScope(User $user): VisibilityScope
    {
        if ($this->hasHrVisibility($user)) {
            return VisibilityScope::allEmployees();
        }

        $employee = $user->getEmployee();

        if ($employee !== null && !$employee->isDeleted()) {
            return VisibilityScope::ownEmployeeOnly($employee);
        }

        $this->denied($user, 'LIST_LEAVE_ALERTS');
    }

    public function isSelf(User $user, Employee $employee): bool
    {
        $linkedEmployee = $user->getEmployee();

        if ($linkedEmployee === null) {
            return false;
        }

        return $linkedEmployee->getEmployeeId() === $employee->getEmployeeId();
    }

    /**
     * Logs an authorization failure without exposing protected employee data
     * and raises a generic access denied error.
     *
     * @throws AccessDeniedException
     */
    private function denied(User $user, string $action): never
    {
        $this->logger->warning('Leave alert authorization denied.', [
            'action' => $action,
            'user_id' => $user->getId(),
            'roles' => $user->getRoleNames(),
        ]);

        throw new AccessDeniedException();
    }
}
