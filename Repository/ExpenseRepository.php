<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use function Doctrine\ORM\QueryBuilder;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ExpenseRepository extends EntityRepository
{
    public function getExpense($filterBy, User $user)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d 00:00:00') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d 23:59:59') : null;
        $employeeId = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmVisit', 'crm_visit');
        $qb->leftJoin('e.visitingArea', 'visiting_area');
        $qb->join('crm_visit.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');

        $qb->select('e AS details');
        $qb->addSelect('crm_visit.created AS visitedDate', 'visiting_area.name AS visitingAreaName');
        $qb->addSelect('employee.userId', 'employee.name', 'designation.name AS designationName');


        if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }else{
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $user->getId());

        }

        $qb->andWhere('crm_visit.created >= :start')->setParameter('start', $start);
        $qb->andWhere('crm_visit.created <= :end')->setParameter('end', $end);

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $key => $result) {
            $month = $result['visitedDate']->format('m-F');

            $result['details']['visitedDate'] = $result['visitedDate'];
            $result['details']['visitingAreaName'] = $result['visitingAreaName'];

            $data[$result['visitedDate']->format('Y')][$month]['details'][] = $result['details'];
            $data[$result['visitedDate']->format('Y')][$month]['employee'] = [
                'userId' => $result['userId'],
                'name' => $result['name'],
                'designation' => $result['designationName'],
            ];

            ksort($data);
            ksort($data[$result['visitedDate']->format('Y')]);
        }
        return $data;
    }

}
