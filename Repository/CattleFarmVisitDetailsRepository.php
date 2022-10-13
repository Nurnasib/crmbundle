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
class CattleFarmVisitDetailsRepository extends BaseRepository
{
    public function getCattleFarmVisitReportByReportingDateCustomerAndEmployee($report, $employee)
    {
        if($report&&$employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('cp')
                ->where('cp.reportingMonth >= :startDate')
                ->andWhere('cp.reportingMonth <= :endDate')
                ->andWhere('cp.report = :report')
                ->andWhere('cp.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getArrayResult();
        }
        return array();
    }

    public function getCattleFarmVisitReport($report, $filterBy, User $loggedUser)
    {
        $returnArray = [];

        if($report){
            $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
            $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;
            $employeeId = isset($filterBy['employeeId']) ?: null;

            $qb = $this->createQueryBuilder('e');

            $qb->join('e.employee', 'employee');
            $qb->leftJoin('employee.designation', 'designation');
            $qb->join('e.report', 'report');
            $qb->leftJoin('e.agent', 'agent');
            $qb->join('e.customer', 'farmer');
            $qb->join('farmer.location','location');
            $qb->join('location.parent', 'district');
            $qb->join('district.parent', 'region');

            $qb->select('e AS details');
            $qb->addSelect('employee.id empId','employee.userId', 'employee.name as employeeName', 'designation.name AS designationName');
            $qb->addSelect('agent.id AS agentId', 'agent.name AS agentName');
            $qb->addSelect('farmer.id AS farmerId', 'farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
            $qb->addSelect( 'region.id AS regionId', 'region.name AS regionName');

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
            $employee = isset($filterBy['employeeId'])? $filterBy['employeeId']: '';
            if (!empty($employee)){
                $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee);
            }

            if($start&&$end){
                $qb->andWhere('e.reportingMonth >= :startDate')->setParameter('startDate',$start);
                $qb->andWhere('e.reportingMonth <= :endDate')->setParameter('endDate', $end);
            }

            $results = $qb->getQuery()->getArrayResult();

            if($results){
                foreach ($results as $result){
                    $result['details']['agentId'] = $result['agentId'];
                    $result['details']['agentName'] = $result['agentName'];
                    $result['details']['farmerId'] = $result['farmerId'];
                    $result['details']['farmerName'] = $result['farmerName'];
                    $result['details']['farmerAddress'] = $result['farmerAddress'];
                    $result['details']['farmerMobile'] = $result['farmerMobile'];
                    $result['details']['empId'] = $result['empId'];
                    $result['details']['employeeName'] = $result['employeeName'];
                    $result['details']['userId'] = $result['userId'];
                    $result['details']['regionId'] = $result['regionId'];

                    $monthYear = $result['details']['reportingMonth']->format('F-Y');

                    $returnArray['records'][$monthYear][$result['regionId']][$result['empId']]['data'][] = $result['details'];
                    $returnArray['regionRecords'][$monthYear][$result['regionId']][]=$result['details'];

                    /*$returnArray[$result['empId']]['userId']=$result['userId'];
                    $returnArray[$result['empId']]['employeeName']=$result['employeeName'];
                    $returnArray[$result['empId']]['employeeDesignationName']=$result['designationName'];
                    $returnArray[$result['empId']]['details'][$monthYear][]=$result['details'];*/
                }
            }
        }

        return $returnArray;

    }

}
