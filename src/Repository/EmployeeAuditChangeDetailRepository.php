<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EmployeeAuditChangeDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<EmployeeAuditChangeDetail> */
final class EmployeeAuditChangeDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmployeeAuditChangeDetail::class);
    }

    public function save(EmployeeAuditChangeDetail $detail, bool $flush = false): void
    {
        $this->getEntityManager()->persist($detail);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
