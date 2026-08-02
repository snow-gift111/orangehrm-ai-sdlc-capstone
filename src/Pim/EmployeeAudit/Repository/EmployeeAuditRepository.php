<?php

declare(strict_types=1);

namespace App\Pim\EmployeeAudit\Repository;

use App\Pim\EmployeeAudit\Entity\EmployeeAuditRecord;
use App\Pim\EmployeeAudit\Enum\EmployeeAuditActionType;
use App\Pim\EmployeeAudit\Exception\EmployeeAuditPersistenceException;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Throwable;

/**
 * @extends ServiceEntityRepository<EmployeeAuditRecord>
 */
final class EmployeeAuditRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmployeeAuditRecord::class);
    }

    public function save(EmployeeAuditRecord $auditRecord): void
    {
        try {
            $entityManager = $this->getEntityManager();
            $entityManager->persist($auditRecord);
            $entityManager->flush();
        } catch (Throwable $exception) {
            throw new EmployeeAuditPersistenceException('Failed to persist employee audit record.', 0, $exception);
        }
    }

    /**
     * @return list<EmployeeAuditRecord>
     */
    public function findByEmployee(
        int $employeeId,
        ?DateTimeImmutable $fromDate = null,
        ?DateTimeImmutable $toDate = null,
        ?EmployeeAuditActionType $actionType = null,
        ?string $actor = null,
        ?string $changedField = null,
        ?int $limit = null,
        ?int $offset = null,
    ): array {
        $queryBuilder = $this->createQueryBuilder('audit')
            ->andWhere('audit.employeeId = :employeeId')
            ->setParameter('employeeId', $employeeId)
            ->orderBy('audit.eventTimestamp', 'DESC')
            ->addOrderBy('audit.id', 'DESC');

        if ($fromDate !== null) {
            $queryBuilder
                ->andWhere('audit.eventTimestamp >= :fromDate')
                ->setParameter('fromDate', $fromDate);
        }

        if ($toDate !== null) {
            $queryBuilder
                ->andWhere('audit.eventTimestamp <= :toDate')
                ->setParameter('toDate', $toDate);
        }

        if ($actionType !== null) {
            $queryBuilder
                ->andWhere('audit.actionType = :actionType')
                ->setParameter('actionType', $actionType);
        }

        $normalizedActor = self::normalizeFilterValue($actor);
        if ($normalizedActor !== null) {
            if (ctype_digit($normalizedActor)) {
                $queryBuilder
                    ->andWhere('audit.actorUserId = :actorUserId OR LOWER(audit.actorDisplayName) LIKE :actorName')
                    ->setParameter('actorUserId', (int) $normalizedActor)
                    ->setParameter('actorName', '%' . mb_strtolower($normalizedActor) . '%');
            } else {
                $queryBuilder
                    ->andWhere('LOWER(audit.actorDisplayName) LIKE :actorName')
                    ->setParameter('actorName', '%' . mb_strtolower($normalizedActor) . '%');
            }
        }

        $normalizedChangedField = self::normalizeFilterValue($changedField);
        if ($normalizedChangedField !== null) {
            $queryBuilder
                ->andWhere('audit.changedField = :changedField')
                ->setParameter('changedField', $normalizedChangedField);
        }

        if ($limit !== null) {
            $queryBuilder->setMaxResults(max(1, $limit));
        }

        if ($offset !== null) {
            $queryBuilder->setFirstResult(max(0, $offset));
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function countByEmployee(
        int $employeeId,
        ?DateTimeImmutable $fromDate = null,
        ?DateTimeImmutable $toDate = null,
        ?EmployeeAuditActionType $actionType = null,
        ?string $actor = null,
        ?string $changedField = null,
    ): int {
        $queryBuilder = $this->createQueryBuilder('audit')
            ->select('COUNT(audit.id)')
            ->andWhere('audit.employeeId = :employeeId')
            ->setParameter('employeeId', $employeeId);

        if ($fromDate !== null) {
            $queryBuilder
                ->andWhere('audit.eventTimestamp >= :fromDate')
                ->setParameter('fromDate', $fromDate);
        }

        if ($toDate !== null) {
            $queryBuilder
                ->andWhere('audit.eventTimestamp <= :toDate')
                ->setParameter('toDate', $toDate);
        }

        if ($actionType !== null) {
            $queryBuilder
                ->andWhere('audit.actionType = :actionType')
                ->setParameter('actionType', $actionType);
        }

        $normalizedActor = self::normalizeFilterValue($actor);
        if ($normalizedActor !== null) {
            if (ctype_digit($normalizedActor)) {
                $queryBuilder
                    ->andWhere('audit.actorUserId = :actorUserId OR LOWER(audit.actorDisplayName) LIKE :actorName')
                    ->setParameter('actorUserId', (int) $normalizedActor)
                    ->setParameter('actorName', '%' . mb_strtolower($normalizedActor) . '%');
            } else {
                $queryBuilder
                    ->andWhere('LOWER(audit.actorDisplayName) LIKE :actorName')
                    ->setParameter('actorName', '%' . mb_strtolower($normalizedActor) . '%');
            }
        }

        $normalizedChangedField = self::normalizeFilterValue($changedField);
        if ($normalizedChangedField !== null) {
            $queryBuilder
                ->andWhere('audit.changedField = :changedField')
                ->setParameter('changedField', $normalizedChangedField);
        }

        return (int) $queryBuilder->getQuery()->getSingleScalarResult();
    }

    private static function normalizeFilterValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim($value);

        return $normalized === '' ? null : $normalized;
    }
}
