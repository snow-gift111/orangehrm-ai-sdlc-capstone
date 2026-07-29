<?php

declare(strict_types=1);

namespace App\Service\Employee;

use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use App\Service\Audit\AuditCaptureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final readonly class EmployeeService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmployeeRepository $employeeRepository,
        private AuditCaptureService $auditCaptureService,
    ) {
    }

    /** @param array<string, mixed> $payload */
    public function create(array $payload): Employee
    {
        return $this->entityManager->wrapInTransaction(function () use ($payload): Employee {
            $employeeId = $this->normalizeString($payload['employeeId'] ?? null);
            if ($employeeId === null) {
                $employeeId = $this->employeeRepository->nextEmployeeId();
            }

            $firstName = $this->requireString($payload, 'firstName', 'First Name is required.');
            $lastName = $this->requireString($payload, 'lastName', 'Last Name is required.');

            if ($this->employeeRepository->findOneByEmployeeId($employeeId) !== null) {
                throw new ConflictHttpException('Employee ID must be unique.');
            }

            $employee = new Employee($employeeId, $firstName, $lastName);
            $this->applyOptionalFields($employee, $payload);

            $this->employeeRepository->save($employee);
            $this->entityManager->flush();

            $this->auditCaptureService->captureCreate($employee);
            $this->entityManager->flush();

            return $employee;
        });
    }

    /** @param array<string, mixed> $payload */
    public function update(string $employeeIdentifier, array $payload): Employee
    {
        return $this->entityManager->wrapInTransaction(function () use ($employeeIdentifier, $payload): Employee {
            $employee = $this->findEmployee($employeeIdentifier);
            $previousSnapshot = $employee->toAuditSnapshot();

            if (array_key_exists('employeeId', $payload)) {
                $newEmployeeId = $this->requireString($payload, 'employeeId', 'Employee ID is required.');
                $existing = $this->employeeRepository->findOneByEmployeeId($newEmployeeId);
                if ($existing !== null && $existing->getId() !== $employee->getId()) {
                    throw new ConflictHttpException('Employee ID must be unique.');
                }
                $employee->setEmployeeId($newEmployeeId);
            }

            if (array_key_exists('firstName', $payload)) {
                $employee->setFirstName($this->requireString($payload, 'firstName', 'First Name is required.'));
            }

            if (array_key_exists('lastName', $payload)) {
                $employee->setLastName($this->requireString($payload, 'lastName', 'Last Name is required.'));
            }

            $this->applyOptionalFields($employee, $payload);

            $this->employeeRepository->save($employee);
            $this->entityManager->flush();

            $this->auditCaptureService->captureUpdate($previousSnapshot, $employee);
            $this->entityManager->flush();

            return $employee;
        });
    }

    public function delete(string $employeeIdentifier): void
    {
        $this->entityManager->wrapInTransaction(function () use ($employeeIdentifier): void {
            $employee = $this->findEmployee($employeeIdentifier);
            $snapshot = $employee->toAuditSnapshot();

            $this->employeeRepository->remove($employee);
            $this->entityManager->flush();

            $this->auditCaptureService->captureDelete($snapshot);
            $this->entityManager->flush();
        });
    }

    public function findEmployee(string $employeeIdentifier): Employee
    {
        $employee = ctype_digit($employeeIdentifier)
            ? $this->employeeRepository->find((int) $employeeIdentifier)
            : null;

        $employee ??= $this->employeeRepository->findOneByEmployeeId($employeeIdentifier);

        if (!$employee instanceof Employee) {
            throw new NotFoundHttpException('Employee not found.');
        }

        return $employee;
    }

    /** @param array<string, mixed> $payload */
    private function applyOptionalFields(Employee $employee, array $payload): void
    {
        if (array_key_exists('middleName', $payload)) {
            $employee->setMiddleName($this->normalizeString($payload['middleName']));
        }

        if (array_key_exists('jobTitle', $payload)) {
            $employee->setJobTitle($this->normalizeString($payload['jobTitle']));
        }

        if (array_key_exists('employmentStatus', $payload)) {
            $employee->setEmploymentStatus($this->normalizeString($payload['employmentStatus']));
        }
    }

    /** @param array<string, mixed> $payload */
    private function requireString(array $payload, string $fieldName, string $message): string
    {
        $value = $this->normalizeString($payload[$fieldName] ?? null);
        if ($value === null) {
            throw new BadRequestHttpException($message);
        }

        return $value;
    }

    private function normalizeString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
