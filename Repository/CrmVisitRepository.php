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

use App\Entity\Admin\Location;
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
class CrmVisitRepository extends EntityRepository
{
    public function findDailyReport($filterBy)
    {
        $employeeId = $filterBy['employeeId'];
        $startDate = $filterBy['startDate'];
        $endDate = $filterBy['endDate'];

        $qb = $this->createQueryBuilder('e');

        $qb->select('e.created', 'e.workingDuration', 'e.workingDurationTo');
        $qb->addSelect('employee.name AS employee_name', 'employee.id AS employee_id');
        $qb->addSelect('location.name AS location_name');
        $qb->addSelect('crm_visit_details.farmCapacity AS customer_farmCapacity', 'crm_visit_details.comments', 'crm_visit_details.process');
        $qb->addSelect('purpose.name AS purpose_name');
        $qb->addSelect('crm_customer.name AS customer_name', 'crm_customer.address AS customer_address');
        $qb->addSelect('agent.name AS agent_name', 'agent.address AS agent_address', 'agent.mobile AS agent_phone');

        $qb->leftJoin('e.employee', 'employee');
        $qb->leftJoin('e.location', 'location');
        $qb->leftJoin('e.crmVisitDetails', 'crm_visit_details');
        $qb->leftJoin('crm_visit_details.crmCustomer', 'crm_customer');
        $qb->leftJoin('crm_visit_details.purpose', 'purpose');
        $qb->leftJoin('crm_visit_details.agent', 'agent');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        $qb->andwhere('crm_visit_details.created >= :startDate')->setParameter('startDate', $startDate);
        $qb->andwhere('crm_visit_details.created <= :endDate')->setParameter('endDate', $endDate);

        return $qb->getQuery()->getArrayResult();
    }

    public function getVisits($startDate, $endDate, $employee, User $loggedUser)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.location', 'location');
        $qb->leftJoin('e.workingMode', 'workingMode');
        $qb->join('e.employee', 'employee');
        $qb->join('employee.userGroup', 'userGroup');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->select('e.id AS visitId', 'e.created AS visitDate', 'e.workingDuration AS visitBegin', 'e.workingDurationTo AS visitEnd', 'location.name AS locationName');
        $qb->addSelect('workingMode.id AS workingModeId', 'workingMode.name AS workingModeName', 'workingMode.slug AS workingModeSlug', 'e.remarks');
        $qb->addSelect('employee.id as empId', 'employee.name as employeeName', 'employee.userId');
        $qb->addSelect('designation.name as employeeDesignationName');

        $qb->where('e.created >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.created <= :endDate')->setParameter('endDate', $endDate);
        $qb->andWhere('userGroup.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');

        $roleSplitArray = [];

        foreach ($loggedUser->getRoles() as $role) {
            $roleSplitArray = array_merge(explode('_', $role), $roleSplitArray);
        }
        if (in_array('ADMIN', $roleSplitArray) && !$employee) {
            $userRole = [];
            if (in_array('ROLE_CRM_POULTRY_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_POULTRY_USER');
            }
            if (in_array('ROLE_CRM_CATTLE_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_CATTLE_USER');
            }
            if (in_array('ROLE_CRM_AQUA_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_AQUA_USER');
            }
            if (in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_SALES_MARKETING_USER');
            }
            $query = '';
//            dd($userRole);
            foreach ($userRole as $key => $role) {
                if ($key !== 0) {
                    $query .= " OR ";
                }
                $query .= "employee.roles LIKE '%" . $role . "%'";

            }
            $qb->andWhere($query);

        } elseif ((in_array('ADMIN', $roleSplitArray) || in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())) && $employee) {
            $qb->andWhere('e.employee = :employee')->setParameter('employee', $employee);
        } elseif (!in_array('ADMIN', $roleSplitArray) && !$employee) {
            $qb->andWhere('e.employee = :employee')->setParameter('employee', $loggedUser);
        }


        $results = $qb->getQuery()->getArrayResult();
        $data = [];

        foreach ($results as $result) {
            $data[$result['empId']]['userId'] = $result['userId'];
            $data[$result['empId']]['employeeName'] = $result['employeeName'];
            $data[$result['empId']]['employeeDesignationName'] = $result['employeeDesignationName'];
            $data[$result['empId']]['details'][$result['visitDate']->format('d-m-Y')][] = $result;
//            $data['visitDays'][$result['visitDate']->format('d-m-Y')]['visitIds'][] = $result['visitId'];
//            $data['visitIds'][] = $result['visitId'];

        }
        return $data;
    }

    public function getVisitsStatus($firstDayOfMonth, $lastDayOfMonth, $selectedEmployee, $lineManagersId)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->join('e.employee', 'employee');
        $qb->join('employee.lineManager', 'lineManager');
        $qb->leftJoin('e.workingMode', 'working_mode');

        $qb->select('e.created AS visitDate');
        $qb->addSelect('working_mode.id AS workingModeId', 'working_mode.name AS workingModeName', 'working_mode.slug AS workingModeSlug');
        $qb->addSelect('employee.id AS employeeId', 'employee.userId AS employeeUserId', 'employee.name AS employeeName');
        $qb->addSelect('lineManager.id AS lineManagerAutoIncId', 'lineManager.userId AS lineManagerUserId', 'lineManager.name AS lineManagerName');

        $qb->where('e.created BETWEEN :start AND :end')->setParameters([
            'start' => $firstDayOfMonth . ' 00:00:00',
            'end' => $lastDayOfMonth . ' 23:59:59',
        ]);
        if ($selectedEmployee) {
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $selectedEmployee->getId());
        }elseif (!empty($lineManagersId)){
            $qb->andWhere('lineManager.userId IN (:lineManagersId)')->setParameter('lineManagersId', $lineManagersId);

        }
        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result) {
            $day = $result['visitDate']->format('d');

/*            $data[$result['lineManagerUserId']]['lineManager'] = [
                'id' => $result['lineManagerAutoIncId'],
                'userId' => $result['lineManagerUserId'],
                'name' => $result['lineManagerName'],
            ];*/
            $data[$result['lineManagerUserId']]['teamMember'][$result['employeeUserId']]['employeeDetails']['id'] = $result['employeeId'];
            $data[$result['lineManagerUserId']]['teamMember'][$result['employeeUserId']]['employeeDetails']['employeeName'] = $result['employeeName'];
            $data[$result['lineManagerUserId']]['teamMember'][$result['employeeUserId']]['employeeDetails']['userId'] = $result['employeeUserId'];
            $data[$result['lineManagerUserId']]['teamMember'][$result['employeeUserId']]['days'][$day]['status'][] = $result['workingModeSlug'];
            $data[$result['lineManagerUserId']]['teamMember'][$result['employeeUserId']]['days'][$day]['visits'][] = $result;
        }
        return $data;






/*

        $data = [];
        foreach ($results as $result) {
            $day = $result['visitDate']->format('d');

            $data[$result['employeeId']][$day]['status'] = $result['workingModeName'];
            $data[$result['employeeId']][$day]['visits'][] = $result;
        }
        return $data;*/

    }
}