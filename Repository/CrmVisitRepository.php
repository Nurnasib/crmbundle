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

    public function getVisits($startDate, $endDate, $employee, $serviceMode, User $loggedUser, $process)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.location', 'location');
        $qb->leftJoin('e.workingMode', 'workingMode');
        $qb->join('e.employee', 'employee');
        $qb->join('employee.serviceMode', 'serviceMode');
        $qb->join('employee.userGroup', 'userGroup');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->select('e.id AS visitId', 'e.created AS visitCreatedDate', 'e.workingDuration AS visitBegin', 'e.workingDurationTo AS visitEnd', 'location.name AS locationName', 'e.visitDate', 'e.visitTime');
        $qb->addSelect('workingMode.id AS workingModeId', 'workingMode.name AS workingModeName', 'workingMode.slug AS workingModeSlug', 'e.remarks');
        $qb->addSelect('employee.id as empId', 'employee.name as employeeName', 'employee.userId');
        $qb->addSelect('designation.name as employeeDesignationName');

        $qb->where('e.created >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.created <= :endDate')->setParameter('endDate', $endDate);
        $qb->andWhere('userGroup.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');

        if($process && $process != ''){
            $qb->leftJoin('e.crmVisitDetails', 'crmVisitDetails');
            $qb->andWhere('crmVisitDetails.process = :process')->setParameter('process', $process);
            $qb->groupBy('e.id');
        }

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
            foreach ($userRole as $key => $role) {
                if ($key !== 0) {
                    $query .= " OR ";
                }
                $query .= "employee.roles LIKE '%" . $role . "%'";

            }
            $qb->andWhere($query);

        } elseif (!in_array('ADMIN', $roleSplitArray) && in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles()) && !$employee) {
            $employeeIdsByLineManager = $this->_em->getRepository(User::class)->getEmployeesByLineManager($loggedUser);
            $employeeIs=[];
            if($employeeIdsByLineManager){
                $employeeIs=$employeeIdsByLineManager;
            }
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIs);
        } elseif (!in_array('ADMIN', $roleSplitArray) && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())) {
            $qb->andWhere('e.employee = :employee')->setParameter('employee', $loggedUser);
        }
        
        if($employee) {
            $qb->andWhere('e.employee = :employee')->setParameter('employee', $employee);
        }else{
//            dd($serviceMode->getId());
            $qb->andWhere('serviceMode.id =:serviceModeId')->setParameter('serviceModeId', $serviceMode->getId());
        }

        $qb->orderBy('e.created', 'ASC');


        $results = $qb->getQuery()->getArrayResult();
        $data = [];

        foreach ($results as $result) {
            $data[$result['empId']]['userId'] = $result['userId'];
            $data[$result['empId']]['employeeName'] = $result['employeeName'];
            $data[$result['empId']]['employeeDesignationName'] = $result['employeeDesignationName'];
            $data[$result['empId']]['details'][$result['visitCreatedDate']->format('d-m-Y')][] = $result;
//            $data['visitDays'][$result['visitCreatedDate']->format('d-m-Y')]['visitIds'][] = $result['visitId'];
//            $data['visitIds'][] = $result['visitId'];

        }
        return $data;
    }

    public function getVisitsStatus($firstDayOfMonth, $lastDayOfMonth, $selectedEmployee, $lineManagersId, $employeeIds)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->join('e.employee', 'employee');
        $qb->join('employee.lineManager', 'lineManager');
        $qb->leftJoin('e.workingMode', 'working_mode');

        $qb->select('e.created AS visitCreatedDate', 'e.remarks');
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
        if($employeeIds){
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);
        }
        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result) {
            $day = $result['visitCreatedDate']->format('d');

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


    }

    /**
     * Employee Monthly Activity Report.
     *
     * Additive helper - used only by the employee_monthly_activity_report route.
     * Nothing else calls it, so no existing report is affected.
     *
     * Returns one block per calendar month covered by the range. Each block holds the
     * seven parameter rows keyed by day-of-month:
     *   agent | sub-agent | farmer | other-agent  -> visit counts from CrmVisitDetails.process
     *   leave | office-work | holiday             -> day flags from CrmVisit.workingMode
     *
     * Visit rows distinguish blank from zero:
     *   null -> employee filed nothing that day, or the day is leave/office work/holiday
     *   0    -> employee worked but logged no visit of that type
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEmployeeMonthlyActivity($employeeId, \DateTime $startDate, \DateTime $endDate): array
    {
        $processRows = $this->createQueryBuilder('v')
            ->select('v.visitDate AS visitDate', 'vd.process AS process', 'COUNT(vd.id) AS total')
            ->join('v.crmVisitDetails', 'vd')
            ->where('v.employee = :employee')->setParameter('employee', $employeeId)
            ->andWhere('v.visitDate >= :startDate')->setParameter('startDate', $startDate)
            ->andWhere('v.visitDate <= :endDate')->setParameter('endDate', $endDate)
            ->groupBy('v.visitDate')->addGroupBy('vd.process')
            ->getQuery()->getArrayResult();

        $modeRows = $this->createQueryBuilder('v')
            ->select('v.visitDate AS visitDate', 'workingMode.slug AS slug')
            ->join('v.workingMode', 'workingMode')
            ->where('v.employee = :employee')->setParameter('employee', $employeeId)
            ->andWhere('v.visitDate >= :startDate')->setParameter('startDate', $startDate)
            ->andWhere('v.visitDate <= :endDate')->setParameter('endDate', $endDate)
            ->groupBy('v.visitDate')->addGroupBy('workingMode.slug')
            ->getQuery()->getArrayResult();

        $counts = [];
        foreach ($processRows as $row) {
            if (empty($row['visitDate']) || !$row['visitDate'] instanceof \DateTimeInterface) {
                continue;
            }
            $counts[$row['visitDate']->format('Y-n-j')][$row['process']] = (int) $row['total'];
        }

        $modes = [];
        foreach ($modeRows as $row) {
            if (empty($row['visitDate']) || !$row['visitDate'] instanceof \DateTimeInterface) {
                continue;
            }
            $modes[$row['visitDate']->format('Y-n-j')][] = $row['slug'];
        }

        $visitKeys = ['agent', 'sub-agent', 'farmer', 'other-agent'];
        $flagKeys = ['leave', 'office-work', 'holiday'];

        $months = new \DatePeriod(
            new \DateTime($startDate->format('Y-m-01')),
            new \DateInterval('P1M'),
            new \DateTime($endDate->format('Y-m-t'))
        );

        $blocks = [];
        foreach ($months as $month) {
            $daysInMonth = (int) $month->format('t');
            $rows = array_fill_keys(array_merge($visitKeys, $flagKeys), []);

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $key = $month->format('Y-n-') . $day;
                $dayModes = $modes[$key] ?? [];
                $nonWorking = (bool) array_intersect($flagKeys, $dayModes);
                $hasRecord = isset($modes[$key]) || isset($counts[$key]);

                foreach ($visitKeys as $process) {
                    if (isset($counts[$key])) {
                        // Details exist, so always show them - this also keeps the
                        // count visible on days carrying more than one working mode.
                        $rows[$process][$day] = $counts[$key][$process] ?? 0;
                    } elseif ($nonWorking || !$hasRecord) {
                        $rows[$process][$day] = null;
                    } else {
                        $rows[$process][$day] = 0;
                    }
                }

                foreach ($flagKeys as $flag) {
                    $rows[$flag][$day] = in_array($flag, $dayModes, true) ? 1 : 0;
                }
            }

            $blocks[] = [
                'year' => (int) $month->format('Y'),
                'month' => (int) $month->format('n'),
                'label' => $month->format('M'),
                'daysInMonth' => $daysInMonth,
                'rows' => $rows,
            ];
        }

        return $blocks;
    }
}