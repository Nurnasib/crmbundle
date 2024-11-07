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

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ChallengerRepository extends EntityRepository
{

    public function getChallengerByEmployee($report, $filterBy, User $loggedUser)
    {
        $returnArray=[];
//dd($filterBy);
        if(!empty($report)){
            $qb = $this->createQueryBuilder('e');

            $qb->select('e.id', 'e.createdAt','e.challengerType','e.name','e.description', 'e.problemOn');

            $qb->addSelect('employee.id AS employeeId');
            $qb->addSelect('employee.name AS employeeName');
            $qb->addSelect('designation.name AS designationName');
            $qb->addSelect('challengerFeed.name AS feedName');

            $qb->join('e.employee','employee');
            $qb->join('employee.designation','designation');
            $qb->leftJoin('e.challengerFeedName','challengerFeed');

            if($report=='challenges-problem'){
                $qb->where('e.challengerType =:challengerType')->setParameter('challengerType','Problems');
            }elseif ($report=='challenges-idea'){
                $qb->where('e.challengerType =:challengerType')->setParameter('challengerType','Idea');
            }elseif ($report=='competitors-activity'){
                $qb->where('e.challengerType =:challengerType')->setParameter('challengerType','Competitor Activity');
            }

            $employee = isset($filterBy['employeeId'])? $filterBy['employeeId']: '';
            if (!empty($employee)){
                $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee);
            }

            $rolesString = implode('_', $loggedUser->getRoles());
            if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
                $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
            }elseif (!str_contains($rolesString, 'ADMIN') && in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
                $employeeIdsByLineManager = $this->_em->getRepository(User::class)->getEmployeesByLineManager($loggedUser);
                $employeeIs=[];
                if($employeeIdsByLineManager){
                    $employeeIs=$employeeIdsByLineManager;
                }
                $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIs);
            }

            $startDate = isset($filterBy['startDate'])&&$filterBy['startDate']!=''? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00': '';
            $endDate = isset($filterBy['endDate'])&&$filterBy['endDate']!=''? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59': '';

            if (!empty($startDate) && !empty($endDate)){
                $qb->andWhere('e.createdAt >= :reportingMonthStart')->setParameter('reportingMonthStart', $startDate);
                $qb->andWhere('e.createdAt <= :reportingMonthEnd')->setParameter('reportingMonthEnd', $endDate);
            }

            $results = $qb->getQuery()->getArrayResult();
            if($results){
                foreach ($results as $result){
                    $reportingMonth = $result['createdAt']->format('m-Y');
                    if($report=='challenges-problem'){
                        $returnArray[$reportingMonth][]=$result;
                    }elseif ($report=='challenges-idea'){
                        $returnArray[$reportingMonth][]=$result;
                    }elseif ($report=='competitors-activity'){
                        $returnArray[$reportingMonth][]=$result;
                    }

                }
            }
        }
//        dd($returnArray);
        return $returnArray;
    }

    public function getChallengerByEmployeeForKpiMonthlyReport($report, $filterBy)
    {
        $returnArray=[];
        if(!empty($report) && $filterBy['employee'] !=""){
            $qb = $this->createQueryBuilder('e');

            $qb->select('e.id', 'e.createdAt','e.challengerType','e.name','e.description', 'e.problemOn');

            $qb->addSelect('employee.id AS employeeId');
            $qb->addSelect('employee.name AS employeeName');
            $qb->addSelect('designation.name AS designationName');
            $qb->addSelect('challengerFeed.name AS feedName');

            $qb->join('e.employee','employee');
            $qb->join('employee.designation','designation');
            $qb->leftJoin('e.challengerFeedName','challengerFeed');

            if($report=='challenges-problem'){
                $qb->where('e.challengerType =:challengerType')->setParameter('challengerType','Problems');
            }elseif ($report=='challenges-idea'){
                $qb->where('e.challengerType =:challengerType')->setParameter('challengerType','Idea');
            }elseif ($report=='competitors-activity'){
                $qb->where('e.challengerType =:challengerType')->setParameter('challengerType','Competitor Activity');
            }

            $qb->andWhere('employee = :employee')->setParameter('employee', $filterBy['employee']);

            $year = isset( $filterBy['year']) &&  $filterBy['year']!='' ?  $filterBy['year'] : date('Y');
            $startMonth = date('Y-m-d', strtotime($year . '-' . $filterBy['startMonth'] . '-01'));
            $endMonth = date('Y-m-t', strtotime($year . '-' . $filterBy['endMonth'] . '-01'));

            $startDate = isset($filterBy['startMonth']) ? (new \DateTime($startMonth))->format('Y-m-d 00:00:00') : date('Y-m-d 00:00:00');
            $endDate = isset($filterBy['endMonth']) ? (new \DateTime($endMonth))->format('Y-m-d 23:59:59') : date('Y-m-t 23:59:59');

            $qb->andWhere('e.createdAt >= :reportingMonthStart')->setParameter('reportingMonthStart', $startDate);
            $qb->andWhere('e.createdAt <= :reportingMonthEnd')->setParameter('reportingMonthEnd', $endDate);

            $results = $qb->getQuery()->getArrayResult();
            if($results){
                foreach ($results as $result){
                    $reportingMonth = $result['createdAt']->format('F-Y');
                    if($report=='challenges-problem'){
                        $returnArray[$reportingMonth][$result['feedName']][]=$result;
                    }elseif ($report=='challenges-idea'){
                        $returnArray[$reportingMonth][]=$result;
                    }elseif ($report=='competitors-activity'){
                        $returnArray[$reportingMonth][]=$result;
                    }

                }
            }
        }
        return $returnArray;
    }

    public function getChallengerByMonthRangeAndEmployeeIdsForKpiMonthlyReport($report, $startDate, $endDate, $employeeIds)
    {
        $returnArray=[];
//        dd($employeeIds);
        if(!empty($report) && !empty($employeeIds)){
            $qb = $this->createQueryBuilder('e');

            $qb->select('e.id', 'COUNT(e.id) as totalRecords', 'e.createdAt',"DATE_FORMAT(e.createdAt,'%Y-%m') as reportMonthYear", 'e.challengerType','e.name','e.description', 'e.problemOn');

            $qb->addSelect('employee.id AS employeeId');
            $qb->addSelect('employee.name AS employeeName');
            $qb->addSelect('designation.name AS designationName');
            $qb->addSelect('challengerFeed.name AS feedName');

            $qb->join('e.employee','employee');
            $qb->join('employee.designation','designation');
            $qb->leftJoin('e.challengerFeedName','challengerFeed');

            if($report=='challenges-problem'){
                $qb->where('e.challengerType =:challengerType')->setParameter('challengerType','Problems');
            }elseif ($report=='challenges-idea'){
                $qb->where('e.challengerType =:challengerType')->setParameter('challengerType','Idea');
            }elseif ($report=='competitors-activity'){
                $qb->where('e.challengerType =:challengerType')->setParameter('challengerType','Competitor Activity');
            }

            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);

            $qb->andWhere('e.createdAt >= :reportingMonthStart')->setParameter('reportingMonthStart', $startDate);
            $qb->andWhere('e.createdAt <= :reportingMonthEnd')->setParameter('reportingMonthEnd', $endDate);

            $qb->groupBy('reportMonthYear');
            $qb->addGroupBy('employeeId');

            $results = $qb->getQuery()->getArrayResult();
            if($results){
                foreach ($results as $result){
                    $reportingMonth = $result['createdAt']->format('Y-m');
                    if($report=='challenges-problem'){
                        $returnArray[$reportingMonth][$result['employeeId']]=$result;
                    }elseif ($report=='challenges-idea'){
                        $returnArray[$reportingMonth][$result['employeeId']]=$result;
                    }elseif ($report=='competitors-activity'){
                        $returnArray[$reportingMonth][$result['employeeId']]=$result;
                    }

                }
            }
        }
        return $returnArray;
    }

}
