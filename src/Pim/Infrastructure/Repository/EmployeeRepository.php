<?php

declare(strict_types=1);

namespace App\Pim\Infrastructure\Repository;

use App\Pim\Domain\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Existing PIM employee lookup repository.
 *
 * @extends ServiceEntityRepository<Employee>
 */
class EmployeeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Employee::class);
    }

    public function findOneByEmployeeId(string $employeeId): ?Employee
    {
        return $this->findOneBy(['employeeId' => $employeeId]);
    }

    /**
     * Returns the employee only when it has not been deleted in PIM.
     */
    public function findOneActiveByEmployeeId(string $employeeId): ?Employee
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.employeeId = :employeeId')
            ->andWhere('e.deletedAt IS NULL')
            ->setParameter('employeeId', $employeeId)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
