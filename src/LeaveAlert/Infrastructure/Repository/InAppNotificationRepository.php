<?php

declare(strict_types=1);

namespace App\LeaveAlert\Infrastructure\Repository;

use App\LeaveAlert\Domain\Entity\InAppNotification;
use App\Security\Domain\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InAppNotification>
 */
class InAppNotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InAppNotification::class);
    }

    /**
     * Notifications belonging to the supplied recipient only.
     *
     * @return list<InAppNotification>
     */
    public function findByRecipient(User $recipient, int $limit = 100): array
    {
        return $this->createQueryBuilder('n')
            ->innerJoin('n.alert', 'a')
            ->addSelect('a')
            ->innerJoin('a.employee', 'e')
            ->addSelect('e')
            ->andWhere('n.recipient = :recipient')
            ->setParameter('recipient', $recipient)
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function save(InAppNotification $notification, bool $flush = false): void
    {
        $this->getEntityManager()->persist($notification);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
