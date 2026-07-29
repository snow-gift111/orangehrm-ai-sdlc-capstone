<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Employee;
use App\Service\Employee\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/pim/employees')]
final class EmployeeController extends AbstractController
{
    public function __construct(private readonly EmployeeService $employeeService)
    {
    }

    #[Route('', name: 'employee_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $employee = $this->employeeService->create($this->decodeJsonBody($request));

        return $this->json(['data' => $this->employeeToArray($employee)], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{employeeIdentifier}', name: 'employee_update', methods: ['PUT', 'PATCH'])]
    public function update(string $employeeIdentifier, Request $request): JsonResponse
    {
        $employee = $this->employeeService->update($employeeIdentifier, $this->decodeJsonBody($request));

        return $this->json(['data' => $this->employeeToArray($employee)]);
    }

    #[Route('/{employeeIdentifier}', name: 'employee_delete', methods: ['DELETE'])]
    public function delete(string $employeeIdentifier): JsonResponse
    {
        $this->employeeService->delete($employeeIdentifier);

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /** @return array<string, mixed> */
    private function decodeJsonBody(Request $request): array
    {
        $content = $request->getContent();
        if (trim($content) === '') {
            return [];
        }

        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }

    /** @return array<string, mixed> */
    private function employeeToArray(Employee $employee): array
    {
        return [
            'id' => $employee->getId(),
            'employeeId' => $employee->getEmployeeId(),
            'firstName' => $employee->getFirstName(),
            'middleName' => $employee->getMiddleName(),
            'lastName' => $employee->getLastName(),
            'employeeName' => $employee->getDisplayName(),
            'jobTitle' => $employee->getJobTitle(),
            'employmentStatus' => $employee->getEmploymentStatus(),
        ];
    }
}
