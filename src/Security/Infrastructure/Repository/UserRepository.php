<?php

declare(strict_types=1);

namespace App\Security\Infrastructure\Repository;

use App\Pim\Domain\Entity\Employee;
use App\Security\Domain\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOneByUsername(string $username): ?User
    {
        return $this->findOneBy(['username' => $username]);
    }

    /**
     * @return list<User>
     */
    public function findActiveByRoleName(string $roleName): array
    {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.roles', 'r')
            ->andWhere('r.name = :roleName')
            ->andWhere('u.status = :status')
            ->setParameter('roleName', $roleName)
            ->setParameter('status', User::STATUS_ACTIVE)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findActiveByEmployee(Employee $employee): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.employee = :employee')
            ->andWhere('u.status = :status')
            ->setParameter('employee', $employee)
            ->setParameter('status', User::STATUS_ACTIVE)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
