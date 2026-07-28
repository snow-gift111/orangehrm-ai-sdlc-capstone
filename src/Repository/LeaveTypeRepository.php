<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LeaveType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class LeaveTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaveType::class);
    }

    /** @return list<LeaveType> */
    public function findActive(): array
    {
        return $this->createQueryBuilder('lt')
            ->andWhere('lt.active = :active')
            ->setParameter('active', true)
            ->orderBy('lt.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
