<?php

declare(strict_types=1);

namespace App\LeaveAlert\Infrastructure\Repository;

use App\LeaveAlert\Domain\Entity\LeaveAlertRule;
use App\LeaveAlert\Domain\Entity\LeaveAlertRuleRecipient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LeaveAlertRuleRecipient>
 */
class LeaveAlertRuleRecipientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaveAlertRuleRecipient::class);
    }

    /**
     * @return list<LeaveAlertRuleRecipient>
     */
    public function findByRule(LeaveAlertRule $rule): array
    {
        return $this->createQueryBuilder('rr')
            ->andWhere('rr.rule = :rule')
            ->setParameter('rule', $rule)
            ->orderBy('rr.recipientType', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
