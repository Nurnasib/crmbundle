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
    public function getExpense($filterBy, User $loggedUser)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d 00:00:00') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d 23:59:59') : null;
        $employeeId = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmVisit', 'crm_visit');
        $qb->leftJoin('e.visitingArea', 'visiting_area');
        $qb->leftJoin('e.purpose', 'purpose');
        $qb->leftJoin('e.vehicle', 'vehicle');
        $qb->join('crm_visit.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->leftJoin('employee.lineManager', 'lineManager');

        $qb->select('e AS details');
        $qb->addSelect('crm_visit.id AS visitId','crm_visit.created AS visitedDate', 'visiting_area.name AS visitingAreaName');
        $qb->addSelect('employee.userId', 'employee.name', 'designation.name AS designationName');
        $qb->addSelect('purpose.id AS purposeId','purpose.name AS purposeName');
        $qb->addSelect('vehicle.id AS vehicleId','vehicle.name AS vehicleName');

//        $qb->groupBy('purpose.id');
//        $qb->addGroupBy('vehicle.id');
//        $qb->addGroupBy('crm_visit.id');
        $rolesString = implode($loggedUser->getRoles(), '_');

        if ($employeeId){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }else{

        }

        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }elseif (in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
                $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
            }else{
                $qb->andWhere('lineManager.id = :lineManagerId')->setParameter('lineManagerId', $loggedUser->getId());
            }
        }

        $qb->andWhere('crm_visit.created >= :start')->setParameter('start', $start);
        $qb->andWhere('crm_visit.created <= :end')->setParameter('end', $end);
//        $qb->andWhere('crm_visit.id = 196');

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $key => $result) {

            $yearMonth = $result['visitedDate']->format('Y-m-F');


            $result['details']['visitId'] = $result['visitId'];
            $result['details']['visitedDate'] = $result['visitedDate'];
            $result['details']['visitingAreaName'] = $result['visitingAreaName'];

            $data[$yearMonth][$result['userId']]['details'][$result['visitId']] = $result['details'];
            $data[$yearMonth][$result['userId']]['details'][$result['visitId']]['purpose'][] = $result['purposeName'];
//            $data[$yearMonth][$result['userId']]['details'][$result['visitId']]['purpose'][] = $result['purposeName'];
//            $data[$yearMonth][$result['userId']]['details'][$result['visitId']]['vehicle'][] = $result['vehicleName'];

            $data[$yearMonth][$result['userId']]['employee'] = [
                'userId' => $result['userId'],
                'name' => $result['name'],
                'designation' => $result['designationName'],
            ];

            ksort($data);
        }
        dd($data);
        return $data;
    }

}
