<?php

declare(strict_types=1);

namespace App\Audit\Infrastructure\Repository;

use App\Audit\Domain\Entity\AuditEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AuditEvent>
 */
class AuditEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuditEvent::class);
    }

    public function save(AuditEvent $auditEvent, bool $flush = false): void
    {
        $this->getEntityManager()->persist($auditEvent);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
