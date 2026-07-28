<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\LeaveBalanceAlert;
use App\Entity\LeaveType;
use App\Enum\AlertAcknowledgementStatus;
use App\Enum\AlertCondition;
use App\Enum\AlertLifecycleStatus;
use App\Enum\AlertReadStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

final class LeaveBalanceAlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LeaveBalanceAlert::class);
    }

    public function findActiveDuplicate(Employee $employee, LeaveType $leaveType, AlertCondition $condition): ?LeaveBalanceAlert
    {
        return $this->findOneBy([
            'employee' => $employee,
            'leaveType' => $leaveType,
            'alertCondition' => $condition,
            'lifecycleStatus' => AlertLifecycleStatus::ACTIVE,
        ]);
    }

    /** @return list<LeaveBalanceAlert> */
    public function findForEmployee(Employee $employee, ?AlertLifecycleStatus $lifecycleStatus = null, ?AlertReadStatus $readStatus = null): array
    {
        $qb = $this->createQueryBuilder('alert')
            ->join('alert.leaveType', 'leaveType')
            ->addSelect('leaveType')
            ->andWhere('alert.employee = :employee')
            ->setParameter('employee', $employee)
            ->orderBy('alert.alertDate', 'DESC');

        if ($lifecycleStatus !== null) {
            $qb->andWhere('alert.lifecycleStatus = :lifecycleStatus')->setParameter('lifecycleStatus', $lifecycleStatus);
        }

        if ($readStatus !== null) {
            $qb->andWhere('alert.readStatus = :readStatus')->setParameter('readStatus', $readStatus);
        }

        return $qb->getQuery()->getResult();
    }

    /** @return array{data:list<LeaveBalanceAlert>, total:int, page:int, pageSize:int} */
    public function searchForHr(array $filters, int $page, int $pageSize): array
    {
        $qb = $this->createQueryBuilder('alert')
            ->join('alert.employee', 'employee')
            ->join('alert.leaveType', 'leaveType')
            ->addSelect('employee', 'leaveType')
            ->orderBy('alert.alertDate', 'DESC');

        if (($filters['lifecycleStatus'] ?? null) instanceof AlertLifecycleStatus) {
            $qb->andWhere('alert.lifecycleStatus = :lifecycleStatus')->setParameter('lifecycleStatus', $filters['lifecycleStatus']);
        }
        if (($filters['readStatus'] ?? null) instanceof AlertReadStatus) {
            $qb->andWhere('alert.readStatus = :readStatus')->setParameter('readStatus', $filters['readStatus']);
        }
        if (($filters['acknowledgementStatus'] ?? null) instanceof AlertAcknowledgementStatus) {
            $qb->andWhere('alert.acknowledgementStatus = :acknowledgementStatus')->setParameter('acknowledgementStatus', $filters['acknowledgementStatus']);
        }
        if (($filters['alertCondition'] ?? null) instanceof AlertCondition) {
            $qb->andWhere('alert.alertCondition = :alertCondition')->setParameter('alertCondition', $filters['alertCondition']);
        }
        if (!empty($filters['employeeId'])) {
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', (int) $filters['employeeId']);
        }
        if (!empty($filters['employeeNumber'])) {
            $qb->andWhere('employee.employeeNumber = :employeeNumber')->setParameter('employeeNumber', $filters['employeeNumber']);
        }
        if (!empty($filters['employeeName'])) {
            $qb->andWhere("LOWER(CONCAT(employee.firstName, ' ', COALESCE(employee.middleName, ''), ' ', employee.lastName)) LIKE :employeeName")
                ->setParameter('employeeName', '%' . mb_strtolower((string) $filters['employeeName']) . '%');
        }
        if (!empty($filters['leaveTypeId'])) {
            $qb->andWhere('leaveType.id = :leaveTypeId')->setParameter('leaveTypeId', (int) $filters['leaveTypeId']);
        }

        $query = $qb->setFirstResult(($page - 1) * $pageSize)->setMaxResults($pageSize)->getQuery();
        $paginator = new Paginator($query, true);

        return [
            'data' => iterator_to_array($paginator->getIterator(), false),
            'total' => count($paginator),
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }
}
