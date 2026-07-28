<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\EmployeeLeaveBalance;
use App\Entity\LeaveType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class LeaveBalanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmployeeLeaveBalance::class);
    }

    /** @return list<EmployeeLeaveBalance> */
    public function findByEmployee(Employee $employee): array
    {
        return $this->createQueryBuilder('balance')
            ->join('balance.leaveType', 'leaveType')
            ->addSelect('leaveType')
            ->andWhere('balance.employee = :employee')
            ->setParameter('employee', $employee)
            ->orderBy('leaveType.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByEmployeeAndLeaveType(Employee $employee, LeaveType $leaveType): ?EmployeeLeaveBalance
    {
        return $this->findOneBy([
            'employee' => $employee,
            'leaveType' => $leaveType,
        ]);
    }
}
