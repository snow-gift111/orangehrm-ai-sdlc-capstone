<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LeaveAlertThreshold;
use App\Entity\LeaveType;
use App\Enum\ThresholdScopeType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AlertThresholdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaveAlertThreshold::class);
    }

    public function findActiveGlobal(): ?LeaveAlertThreshold
    {
        return $this->findOneBy([
            'scopeType' => ThresholdScopeType::GLOBAL,
            'active' => true,
            'leaveType' => null,
        ]);
    }

    public function findActiveForLeaveType(LeaveType $leaveType): ?LeaveAlertThreshold
    {
        return $this->findOneBy([
            'scopeType' => ThresholdScopeType::LEAVE_TYPE,
            'active' => true,
            'leaveType' => $leaveType,
        ]);
    }

    /** @return list<LeaveAlertThreshold> */
    public function findActive(?ThresholdScopeType $scopeType = null, ?LeaveType $leaveType = null): array
    {
        $qb = $this->createQueryBuilder('threshold')
            ->leftJoin('threshold.leaveType', 'leaveType')
            ->addSelect('leaveType')
            ->andWhere('threshold.active = :active')
            ->setParameter('active', true)
            ->orderBy('threshold.scopeType', 'ASC')
            ->addOrderBy('leaveType.name', 'ASC');

        if ($scopeType !== null) {
            $qb->andWhere('threshold.scopeType = :scopeType')->setParameter('scopeType', $scopeType);
        }

        if ($leaveType !== null) {
            $qb->andWhere('threshold.leaveType = :leaveType')->setParameter('leaveType', $leaveType);
        }

        return $qb->getQuery()->getResult();
    }
}
