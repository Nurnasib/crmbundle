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
class AntibioticFreeFarmRepository extends BaseRepository
{
    public function getAntibioticFreeFarmByReportingMonthEmployeeCustomerAndReport($report, $employee, $customer, $reportingMonth)
    {
        if($report&&$employee){
            $startDate = date('Y-m-01', strtotime($reportingMonth));
            $endDate = date('Y-m-t', strtotime($reportingMonth));
            $query = $this->createQueryBuilder('aff')
                ->where('aff.reportingMonth >= :startDate')
                ->andWhere('aff.reportingMonth <= :endDate')
                ->andWhere('aff.report = :report')
                ->andWhere('aff.customer = :customer')
                ->andWhere('aff.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'customer'=>$customer, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getMonthlyAntibioticFreeFarmTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');

        $qb->join('e.employee', 'employee');
        $qb->join('e.report', 'report');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.reportingMonth >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.reportingMonth <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);
        $qb->andWhere('report.settingType = :settingType')->setParameter('settingType', 'FARMER_REPORT');
        $qb->andWhere('report.slug = :slug')->setParameter('slug', 'antibiotic-free-farm-poultry');

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }

    public function getAntibioticFreeFarmByEmployeeAndDate($report, $filterBy, User $loggedUser)
    {
        $returnArray=[];

        if(!empty($report)){
            $qb = $this->createQueryBuilder('e');
            $qb->select('e.id as aId', 'e.hatchingDate', 'e.reportingMonth', 'e.totalStockedChicksPcs');
            $qb->addSelect('e.totalFeedUsedKg', 'e.totalBroilerWeightKg', 'e.ageDays', 'e.remarks');
            $qb->addSelect('e.mortality', 'e.fcr', 'e.medicineTotalCost', 'e.vaccineTotalCost');

            $qb->addSelect('agent.name AS agentName', 'agent.address AS agentAddress', 'agent.mobile AS agentMobile');

            $qb->addSelect('employee.id AS employeeId', 'employee.name AS employeeName');
            $qb->addSelect('designation.name AS employeeDesignationName');
            $qb->addSelect('customer.id AS customerId', 'customer.name AS customerName', 'customer.mobile AS customerMobile', 'customer.address AS customerAddress');
            $qb->addSelect( 'customerRegion.id AS regionId', 'customerRegion.name AS regionName');

            $qb->addSelect( 'district.name AS agentDistrictName');

            $qb->addSelect('hatchery.name AS hatcheryName');
            $qb->addSelect('breed.name AS breedBame');
            $qb->addSelect('feed.name AS feedName');

            $qb->join('e.employee', 'employee');
            $qb->leftJoin('employee.designation', 'designation');
            $qb->join('e.customer','customer');
            $qb->join('customer.location','customerUpazila');
            $qb->join('customerUpazila.parent', 'customerDistrict');
            $qb->join('customerDistrict.parent', 'customerRegion');
            $qb->leftJoin('e.agent', 'agent');
            $qb->leftJoin('agent.district', 'district');
            $qb->leftJoin('e.hatchery', 'hatchery');
            $qb->leftJoin('e.breed', 'breed');
            $qb->leftJoin('e.feed', 'feed');
            $qb->where('e.report =:report')->setParameter('report',$report);

            $employee = isset($filterBy['employeeId'])&&$filterBy['employeeId']!=''? $filterBy['employeeId']: '';
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
            $endDate = isset($filterBy['endDate']) && $filterBy['endDate']!=''? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59': '';

            if (!empty($startDate) && !empty($endDate)){
                $qb->andWhere('e.reportingMonth >= :reportingMonthStart')->setParameter('reportingMonthStart', $startDate);
                $qb->andWhere('e.reportingMonth <= :reportingMonthEnd')->setParameter('reportingMonthEnd', $endDate);
            }


            $feedCompany = isset($filterBy['feedCompany'])&& $filterBy['feedCompany']!=''? $filterBy['feedCompany']: '';
            if (!empty($feedCompany)){
                if($feedCompany=='NOURISH'){
                    $qb->andWhere('hatchery.name = :feed_name')->setParameter('feed_name','Nourish');
                }elseif ($feedCompany=='OTHERS'){
                    $qb->andWhere('hatchery.name IS NULL OR hatchery.name != :feed_name')->setParameter('feed_name','Nourish');
                }
            }
            
            $qb->orderBy('e.reportingMonth','ASC');

            $results = $qb->getQuery()->getArrayResult();
            if($results){
                foreach ($results as $result){
                    $monthYear = $result['reportingMonth']->format('F-Y');
                    $returnArray['totalRecord'][]=$result;
                    $returnArray['details'][$monthYear][$result['regionId']][$result['employeeId']][]=$result;
                    $returnArray['regionRecord'][$monthYear][$result['regionId']][]=$result;
                    $returnArray['monthRecord'][$monthYear][]=$result;
                }
            }
        }
//        dd($returnArray);
        return $returnArray;
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
