<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Employee> */
final class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function save(Employee $employee, bool $flush = false): void
    {
        $this->getEntityManager()->persist($employee);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Employee $employee, bool $flush = false): void
    {
        $this->getEntityManager()->remove($employee);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByEmployeeId(string $employeeId): ?Employee
    {
        return $this->findOneBy(['employeeId' => $employeeId]);
    }

    public function nextEmployeeId(): string
    {
        $connection = $this->getEntityManager()->getConnection();
        $value = (int) $connection->fetchOne("SELECT COALESCE(MAX(CAST(employee_id AS UNSIGNED)), 0) + 1 FROM employee WHERE employee_id REGEXP '^[0-9]+$'");

        return str_pad((string) max(1, $value), 4, '0', STR_PAD_LEFT);
    }
}
