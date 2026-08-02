<?php

declare(strict_types=1);

namespace App\Pim\EmployeeAudit\Repository;

use App\Pim\EmployeeAudit\Dto\EmployeeAuditHistoryFilter;
use App\Pim\EmployeeAudit\Entity\EmployeeAuditRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EmployeeAuditRecord>
 */
final class EmployeeAuditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmployeeAuditRecord::class);
    }

    public function append(EmployeeAuditRecord $auditRecord, bool $flush = true): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($auditRecord);

        if ($flush) {
            $entityManager->flush();
        }
    }

    /**
     * @return list<EmployeeAuditRecord>
     */
    public function findByEmployee(int $employeeId, EmployeeAuditHistoryFilter $filter): array
    {
        $queryBuilder = $this->createFilteredQueryBuilder($employeeId, $filter)
            ->orderBy('auditRecord.occurredAt', 'DESC')
            ->addOrderBy('auditRecord.auditId', 'DESC')
            ->setFirstResult($filter->offset())
            ->setMaxResults($filter->pageSize);

        /** @var list<EmployeeAuditRecord> $records */
        $records = $queryBuilder->getQuery()->getResult();

        return $records;
    }

    public function countByEmployee(int $employeeId, EmployeeAuditHistoryFilter $filter): int
    {
        $queryBuilder = $this->createFilteredQueryBuilder($employeeId, $filter)
            ->select('COUNT(auditRecord.auditId)');

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    private function createFilteredQueryBuilder(int $employeeId, EmployeeAuditHistoryFilter $filter): \Doctrine\ORM\QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('auditRecord')
            ->andWhere('auditRecord.employeeId = :employeeId')
            ->setParameter('employeeId', $employeeId);

        if ($filter->eventType !== null) {
            $queryBuilder
                ->andWhere('auditRecord.eventType = :eventType')
                ->setParameter('eventType', $filter->eventType);
        }

        if ($filter->dateFrom !== null) {
            $queryBuilder
                ->andWhere('auditRecord.occurredAt >= :dateFrom')
                ->setParameter('dateFrom', $filter->dateFrom);
        }

        if ($filter->dateTo !== null) {
            $queryBuilder
                ->andWhere('auditRecord.occurredAt <= :dateTo')
                ->setParameter('dateTo', $filter->dateTo);
        }

        if ($filter->actorUserId !== null) {
            $queryBuilder
                ->andWhere('auditRecord.actorUserId = :actorUserId')
                ->setParameter('actorUserId', $filter->actorUserId);
        }

        return $queryBuilder;
    }
}
