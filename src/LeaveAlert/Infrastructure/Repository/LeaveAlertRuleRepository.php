<?php

declare(strict_types=1);

namespace App\LeaveAlert\Infrastructure\Repository;

use App\LeaveAlert\Domain\Entity\LeaveAlertRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LeaveAlertRule>
 */
class LeaveAlertRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaveAlertRule::class);
    }

    /**
     * Retrieves all rules for administration purposes.
     *
     * @return list<LeaveAlertRule>
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.active', 'DESC')
            ->addOrderBy('r.ruleName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retrieves active rules applicable to the supplied leave type context.
     *
     * A rule with a NULL leave type code is a global/default rule and applies
     * to every leave type context. Inactive rules are never returned
     * (LBA-FR-010, US-003-AC-002).
     *
     * @return list<LeaveAlertRule>
     */
    public function findActiveRulesForLeaveType(string $leaveTypeCode): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.active = :active')
            ->andWhere('r.leaveTypeCode IS NULL OR r.leaveTypeCode = :leaveTypeCode')
            ->setParameter('active', true)
            ->setParameter('leaveTypeCode', $leaveTypeCode)
            ->orderBy('r.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(LeaveAlertRule $rule, bool $flush = false): void
    {
        $this->getEntityManager()->persist($rule);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
