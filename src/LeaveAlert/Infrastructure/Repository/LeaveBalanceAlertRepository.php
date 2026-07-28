<?php

declare(strict_types=1);

namespace App\LeaveAlert\Infrastructure\Repository;

use App\LeaveAlert\Domain\Entity\LeaveAlertRule;
use App\LeaveAlert\Domain\Entity\LeaveBalanceAlert;
use App\LeaveAlert\Domain\Enum\AlertStatus;
use App\LeaveAlert\Domain\Enum\TriggerCondition;
use App\Pim\Domain\Entity\Employee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LeaveBalanceAlert>
 */
class LeaveBalanceAlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaveBalanceAlert::class);
    }

    /**
     * Duplicate active alert detection (LBA-FR-018).
     */
    public function findActiveDuplicate(
        Employee $employee,
        LeaveAlertRule $rule,
        string $leaveTypeCode,
        string $thresholdValue,
        TriggerCondition $triggerCondition,
    ): ?LeaveBalanceAlert {
        return $this->createQueryBuilder('a')
            ->andWhere('a.employee = :employee')
            ->andWhere('a.rule = :rule')
            ->andWhere('a.leaveTypeCode = :leaveTypeCode')
            ->andWhere('a.thresholdValueAtAlert = :thresholdValue')
            ->andWhere('a.triggerCondition = :triggerCondition')
            ->andWhere('a.status = :status')
            ->setParameter('employee', $employee)
            ->setParameter('rule', $rule)
            ->setParameter('leaveTypeCode', $leaveTypeCode)
            ->setParameter('thresholdValue', $thresholdValue)
            ->setParameter('triggerCondition', $triggerCondition->value)
            ->setParameter('status', AlertStatus::ACTIVE->value)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Active alerts across all employees, for HR visibility (LBA-FR-029, LBA-FR-034).
     *
     * @return list<LeaveBalanceAlert>
     */
    public function findAllActive(int $limit = 200): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.employee', 'e')
            ->addSelect('e')
            ->innerJoin('a.rule', 'r')
            ->addSelect('r')
            ->andWhere('a.status = :status')
            ->andWhere('e.deletedAt IS NULL')
            ->setParameter('status', AlertStatus::ACTIVE->value)
            ->orderBy('a.alertGeneratedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Active alerts restricted to a single employee, for employee self-view
     * (LBA-FR-037).
     *
     * @return list<LeaveBalanceAlert>
     */
    public function findActiveByEmployee(Employee $employee, int $limit = 200): array
    {
        return $this->createQueryBuilder('a')
            ->innerJoin('a.employee', 'e')
            ->addSelect('e')
            ->innerJoin('a.rule', 'r')
            ->addSelect('r')
            ->andWhere('a.status = :status')
            ->andWhere('a.employee = :employee')
            ->setParameter('status', AlertStatus::ACTIVE->value)
            ->setParameter('employee', $employee)
            ->orderBy('a.alertGeneratedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function save(LeaveBalanceAlert $alert, bool $flush = false): void
    {
        $this->getEntityManager()->persist($alert);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
