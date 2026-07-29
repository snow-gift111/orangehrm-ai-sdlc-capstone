<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EmployeeAuditEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<EmployeeAuditEvent> */
final class EmployeeAuditEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmployeeAuditEvent::class);
    }

    public function save(EmployeeAuditEvent $event, bool $flush = false): void
    {
        $this->getEntityManager()->persist($event);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @return list<EmployeeAuditEvent> */
    public function findHistoryByEmployeeIdentifier(string $employeeIdentifier): array
    {
        return $this->createQueryBuilder('event')
            ->andWhere('event.employeeIdSnapshot = :identifier OR event.employeeInternalId = :internalId')
            ->setParameter('identifier', $employeeIdentifier)
            ->setParameter('internalId', ctype_digit($employeeIdentifier) ? (int) $employeeIdentifier : -1)
            ->orderBy('event.occurredAt', 'DESC')
            ->addOrderBy('event.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
