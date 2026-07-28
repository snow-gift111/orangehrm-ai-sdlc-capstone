<?php

declare(strict_types=1);

namespace App\LeaveAlert\Infrastructure\Repository;

use App\LeaveAlert\Domain\Entity\LeaveBalance;
use App\Pim\Domain\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LeaveBalance>
 */
class LeaveBalanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaveBalance::class);
    }

    public function findOneByEmployeeAndLeaveType(Employee $employee, string $leaveTypeCode): ?LeaveBalance
    {
        return $this->findOneBy([
            'employee' => $employee,
            'leaveTypeCode' => $leaveTypeCode,
        ]);
    }

    /**
     * @return list<LeaveBalance>
     */
    public function findByEmployee(Employee $employee): array
    {
        return $this->createQueryBuilder('lb')
            ->andWhere('lb.employee = :employee')
            ->setParameter('employee', $employee)
            ->orderBy('lb.leaveTypeCode', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(LeaveBalance $leaveBalance, bool $flush = false): void
    {
        $this->getEntityManager()->persist($leaveBalance);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
