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
     * Thin wrapper around getTeamMonthlyActivity() for the "Single Employee" mode
     * of that report; see that method for the query and block shape.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getEmployeeMonthlyActivity($employeeId, \DateTime $startDate, \DateTime $endDate): array
    {
        return $this->getTeamMonthlyActivity([$employeeId], $startDate, $endDate)[$employeeId] ?? [];
    }

    /**
     * Employee Monthly Activity Report - "Line Manager" mode.
     *
     * Same shape as getEmployeeMonthlyActivity(), but for many employees at once via
     * two grouped queries instead of one pair per employee, so a large team doesn't
     * turn into an N+1.
     *
     * Returns one block list per employee id. Each block list holds one block per
     * calendar month covered by the range, and each block holds the parameter rows
     * keyed by day-of-month:
     *   agent | sub-agent | farmer | other-agent  -> visit counts from CrmVisitDetails.process
     *   one flag row per WORKING_MODE            -> day flags from CrmVisit.workingMode
     *
     * $flagKeys carries every crm_setting row of setting_type WORKING_MODE, so all ten
     * modes get a row. Keep it in step with parameterOrder/parameterLabels/flagSymbols in
     * employee-monthly-activity.html.twig - a key missing there renders an unlabelled row.
     *
     * $nonWorkingKeys stays the original three on purpose: it drives only the blank-vs-zero
     * rule below, not which rows are drawn, so widening the row list leaves counts untouched.
     *
     * Visit rows distinguish blank from zero:
     *   null -> employee filed nothing that day, or the day is leave/office work/holiday
     *   0    -> employee worked but logged no visit of that type
     *
     * @param int[] $employeeIds
     * @return array<int, array<int, array<string, mixed>>>
     */
    public function getTeamMonthlyActivity(array $employeeIds, \DateTime $startDate, \DateTime $endDate): array
    {
        if (!$employeeIds) {
            return [];
        }

        $processRows = $this->createQueryBuilder('v')
            ->select('IDENTITY(v.employee) AS employeeId', 'v.visitDate AS visitDate', 'vd.process AS process', 'COUNT(vd.id) AS total')
            ->join('v.crmVisitDetails', 'vd')
            ->where('v.employee IN (:employeeIds)')->setParameter('employeeIds', $employeeIds)
            ->andWhere('v.visitDate >= :startDate')->setParameter('startDate', $startDate)
            ->andWhere('v.visitDate <= :endDate')->setParameter('endDate', $endDate)
            ->groupBy('v.employee')->addGroupBy('v.visitDate')->addGroupBy('vd.process')
            ->getQuery()->getArrayResult();

        $modeRows = $this->createQueryBuilder('v')
            ->select('IDENTITY(v.employee) AS employeeId', 'v.visitDate AS visitDate', 'workingMode.slug AS slug')
            ->join('v.workingMode', 'workingMode')
            ->where('v.employee IN (:employeeIds)')->setParameter('employeeIds', $employeeIds)
            ->andWhere('v.visitDate >= :startDate')->setParameter('startDate', $startDate)
            ->andWhere('v.visitDate <= :endDate')->setParameter('endDate', $endDate)
            ->groupBy('v.employee')->addGroupBy('v.visitDate')->addGroupBy('workingMode.slug')
            ->getQuery()->getArrayResult();

        $countsByEmployee = [];
        foreach ($processRows as $row) {
            if (empty($row['visitDate']) || !$row['visitDate'] instanceof \DateTimeInterface) {
                continue;
            }
            $countsByEmployee[$row['employeeId']][$row['visitDate']->format('Y-n-j')][$row['process']] = (int) $row['total'];
        }

        $modesByEmployee = [];
        foreach ($modeRows as $row) {
            if (empty($row['visitDate']) || !$row['visitDate'] instanceof \DateTimeInterface) {
                continue;
            }
            $modesByEmployee[$row['employeeId']][$row['visitDate']->format('Y-n-j')][] = $row['slug'];
        }

        $blocksByEmployee = [];
        foreach ($employeeIds as $employeeId) {
            $blocksByEmployee[$employeeId] = $this->buildMonthlyActivityBlocks(
                $countsByEmployee[$employeeId] ?? [],
                $modesByEmployee[$employeeId] ?? [],
                $startDate,
                $endDate
            );
        }

        return $blocksByEmployee;
    }

    /**
     * Turns the per-day counts/modes gathered by getTeamMonthlyActivity() into the
     * month blocks the template renders. Pulled out of getEmployeeMonthlyActivity()
     * so both the single-employee and team queries share the same block shape.
     *
     * @param array<string, array<string, int>> $counts day key ("Y-n-j") -> process -> total
     * @param array<string, string[]> $modes day key ("Y-n-j") -> working-mode slugs
     */
    private function buildMonthlyActivityBlocks(array $counts, array $modes, \DateTime $startDate, \DateTime $endDate): array
    {
        $visitKeys = ['agent', 'sub-agent', 'farmer', 'other-agent'];
        $flagKeys = [
            'leave', 'office-work', 'holiday',
            'meeting', 'training', 'workshop', 'conference', 'traveling', 'other-1', 'working',
        ];
        // Only these three suppress the visit counts; the modes added above are still
        // field-work days, so their counts keep rendering exactly as before.
        $nonWorkingKeys = ['leave', 'office-work', 'holiday'];

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
                $nonWorking = (bool) array_intersect($nonWorkingKeys, $dayModes);
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

    /**
     * Employee Agent & Sub-Agent Activity Report.
     *
     * For one employee over a date range, gathers every agent/sub-agent the
     * employee actually engaged with across the activity sources that carry BOTH
     * an employee and an agent (visits, service reports, farmer/fish-feed
     * complaints, farmer-touch, agent upgradation). Linkage is by real activity,
     * not territory. Orders are NOT included (kpi_agent_order has no employee).
     *
     * Returns:
     *   ['groups' => [ groupSlug => ['label' => ..., 'agents' => [
     *          ['agent' => Agent, 'counts' => [type=>n], 'events' => [ ['date'=>,'type'=>,'detail'=>] ] ]
     *      ]]], 'totalAgents' => int]
     */
    public function getEmployeeAgentActivity($employeeId, \DateTime $startDate, \DateTime $endDate): array
    {
        $em = $this->_em;
        $endOfDay = (clone $endDate)->setTime(23, 59, 59);

        $buckets = [];
        $add = function ($agentId, $type, $date, $detail = '') use (&$buckets) {
            if (!$agentId) {
                return;
            }
            if (!isset($buckets[$agentId])) {
                $buckets[$agentId] = ['counts' => [], 'events' => []];
            }
            $buckets[$agentId]['counts'][$type] = ($buckets[$agentId]['counts'][$type] ?? 0) + 1;
            $buckets[$agentId]['events'][] = ['date' => $date, 'type' => $type, 'detail' => $detail];
        };

        // 1) Visits (crm_visit.employee + crm_visit_details.agent, by visit_date)
        $rows = $this->createQueryBuilder('v')
            ->select('IDENTITY(vd.agent) AS agentId', 'v.visitDate AS date', 'purpose.name AS detail')
            ->join('v.crmVisitDetails', 'vd')
            ->leftJoin('vd.purpose', 'purpose')
            ->where('IDENTITY(v.employee) = :emp')->setParameter('emp', $employeeId)
            ->andWhere('v.visitDate BETWEEN :s AND :e')->setParameter('s', $startDate)->setParameter('e', $endDate)
            ->andWhere('vd.agent IS NOT NULL')
            ->getQuery()->getArrayResult();
        foreach ($rows as $r) {
            $add($r['agentId'], 'visit', $r['date'], $r['detail'] ?? '');
        }

        // 2-5) createdAt-based sources
        $simple = [
            'service'       => \Terminalbd\CrmBundle\Entity\CustomerServiceReport::class,
            'complain'      => \Terminalbd\CrmBundle\Entity\FarmerComplain::class,
            'fish_complain' => \Terminalbd\CrmBundle\Entity\FishFeedComplain::class,
            'upgrade'       => \Terminalbd\CrmBundle\Entity\AgentUpgradationReport::class,
        ];
        foreach ($simple as $type => $class) {
            $rows = $em->getRepository($class)->createQueryBuilder('a')
                ->select('IDENTITY(a.agent) AS agentId', 'a.createdAt AS date')
                ->where('IDENTITY(a.employee) = :emp')->setParameter('emp', $employeeId)
                ->andWhere('a.createdAt BETWEEN :s AND :e')->setParameter('s', $startDate)->setParameter('e', $endOfDay)
                ->andWhere('a.agent IS NOT NULL')
                ->getQuery()->getArrayResult();
            foreach ($rows as $r) {
                $add($r['agentId'], $type, $r['date'], '');
            }
        }

        // 6) Farmer touch (visiting_date; agent OR other_agent)
        $rows = $em->getRepository(\Terminalbd\CrmBundle\Entity\NewFarmerTouch\FarmerTouchReport::class)->createQueryBuilder('t')
            ->select('IDENTITY(t.agent) AS agentId', 'IDENTITY(t.otherAgent) AS otherAgentId', 't.visitingDate AS date')
            ->where('IDENTITY(t.employee) = :emp')->setParameter('emp', $employeeId)
            ->andWhere('t.visitingDate BETWEEN :s AND :e')->setParameter('s', $startDate)->setParameter('e', $endDate)
            ->getQuery()->getArrayResult();
        foreach ($rows as $r) {
            $add($r['agentId'] ?: $r['otherAgentId'], 'touch', $r['date'], '');
        }

        if (empty($buckets)) {
            return ['groups' => [], 'totalAgents' => 0];
        }

        $agents = $em->getRepository(\App\Entity\Core\Agent::class)->createQueryBuilder('ag')
            ->leftJoin('ag.agentGroup', 'grp')->addSelect('grp')
            ->leftJoin('ag.parent', 'par')->addSelect('par')
            ->leftJoin('ag.upozila', 'up')->addSelect('up')
            ->where('ag.id IN (:ids)')->setParameter('ids', array_keys($buckets))
            ->getQuery()->getResult();

        $groupLabels = [
            'feed'        => 'Feed Agent',
            'chick'       => 'Chick Agent',
            'other-agent' => 'Other Agent',
            'sub-agent'   => 'Sub-Agent',
        ];
        $groupOrder = ['feed', 'chick', 'other-agent', 'sub-agent'];

        $groups = [];
        foreach ($agents as $agent) {
            $gslug = $agent->getAgentGroup() ? $agent->getAgentGroup()->getSlug() : 'other';
            $b = $buckets[$agent->getId()];
            usort($b['events'], function ($x, $y) {
                return $y['date'] <=> $x['date'];
            });
            if (!isset($groups[$gslug])) {
                $groups[$gslug] = ['label' => $groupLabels[$gslug] ?? ucwords(str_replace('-', ' ', $gslug)), 'agents' => []];
            }
            $groups[$gslug]['agents'][] = ['agent' => $agent, 'counts' => $b['counts'], 'events' => $b['events']];
        }

        // deterministic group ordering: known groups first, then any extras
        uksort($groups, function ($x, $y) use ($groupOrder) {
            $ix = array_search($x, $groupOrder, true);
            $iy = array_search($y, $groupOrder, true);
            $ix = $ix === false ? 99 : $ix;
            $iy = $iy === false ? 99 : $iy;
            return $ix <=> $iy;
        });

        return ['groups' => $groups, 'totalAgents' => count($agents)];
    }
}