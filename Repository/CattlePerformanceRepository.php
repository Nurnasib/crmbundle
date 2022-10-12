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
use Terminalbd\KpiBundle\Entity\EmployeeBoard;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class CattlePerformanceRepository extends EntityRepository
{

    public function getCattlePerformanceReportByReportingDateAndFeedType($report, $employee)
    {
        if($report&&$employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('cp')
                ->where('cp.reportingMonth >= :startDate')
                ->andWhere('cp.reportingMonth <= :endDate')
                ->andWhere('cp.report = :report')
//                ->andWhere('cp.customer = :customer')
                ->andWhere('cp.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getResult();
        }
        return array();
    }

    public function getPerformanceReport($report, $filterBy, User $loggedUser)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;
        $employeeId = isset($filterBy['employeeId']) ?: null;

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');

        $qb->leftJoin('e.visit','visit');
        $qb->leftJoin('visit.location','location');
        $qb->leftJoin('location.parent','dist');
        $qb->leftJoin('dist.parent','region');

        $qb->join('e.report', 'report');
        $qb->leftJoin('e.agent', 'agent');
        $qb->join('e.customer', 'farmer');
        $qb->leftJoin('e.breedType', 'breed_type');
        $qb->leftJoin('e.feedType', 'feed_type');

        $qb->select('e AS details');
//        $qb->select('e.id as cpId','e.visitingDate', 'e.reportingMonth','e.ageOfCattleMonth');
//        $qb->addSelect('e.previousBodyWeight','e.presentBodyWeight','e.bodyWeightDifference');
        $qb->addSelect('employee.id as employeeAutoId', 'employee.userId', 'employee.name', 'designation.name AS designationName');
        $qb->addSelect('agent.id AS agentId', 'agent.name AS agentName', 'agent.address AS agentAddress');
        $qb->addSelect('farmer.id AS farmerId', 'farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('breed_type.id AS breedTypeId', 'breed_type.name AS breedTypeName');
        $qb->addSelect('feed_type.id AS feedTypeId', 'feed_type.name AS feedTypeName');
        $qb->addSelect('region.id AS regionId', 'region.name AS regionName');

        $qb->where('e.visitingDate >= :start')->setParameter('start', $start);
        $qb->andWhere('e.visitingDate <= :end')->setParameter('end', $end);
        $qb->andWhere('report.slug = :reportSlug')->setParameter('reportSlug', $report->getSlug());

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
        $region = isset($filterBy['region'])? $filterBy['region']: '';
        if (!empty($region)){
            $qb->andWhere('region.id = :regionId')->setParameter('regionId', $region);
        }

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $result['details']['agentId'] = $result['agentId'];
            $result['details']['agentName'] = $result['agentName'];
            $result['details']['farmerId'] = $result['farmerId'];
            $result['details']['farmerName'] = $result['farmerName'];
            $result['details']['farmerAddress'] = $result['farmerAddress'];
            $result['details']['farmerMobile'] = $result['farmerMobile'];
            $result['details']['breedTypeId'] = $result['breedTypeId'];
            $result['details']['breedTypeName'] = $result['breedTypeName'];
            $result['details']['feedTypeId'] = $result['feedTypeId'];
            $result['details']['feedTypeName'] = $result['feedTypeName'];
            $result['details']['regionId'] = $result['regionId'];
            $result['details']['regionName'] = $result['regionName'];
            $result['details']['employeeName'] = $result['name'];


            $month = $result['details']['visitingDate']->format('F-Y');
//            $year = $result['details']['visitingDate']->format('Y');
            /*$data[$month][$result['regionId']][$result['employeeAutoId']]['employeeDetails'] = [
                'userId' => $result['userId'],
                'name' => $result['name'],
                'designation' => $result['designationName'],
            ];*/
            $data['records'][$month][$result['regionId']][$result['employeeAutoId']]['data'][] = $result['details'];
            $data['regionRecords'][$month][$result['regionId']][]=$result['details'];

            /*ksort($data);
            ksort($data[$year][$result['employeeAutoId']]['data']);*/
        }
//        dd($data);
        return $data;
    }

    public function getNumberOfReportsForKpi($board)
    {
        /**
         * @var EmployeeBoard $board
         */
        $startDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-d');
        $endDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-t');

        $qb = $this->createQueryBuilder('e');


        $qb->where('e.employee = :employee')->setParameter('employee',$board->getEmployee());
        $qb->andWhere('e.reportingMonth >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.reportingMonth <= :endDate')->setParameter('endDate', $endDate);
        return count($qb->getQuery()->getArrayResult());
    }
}
