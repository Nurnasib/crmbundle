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

use Doctrine\ORM\EntityRepository;

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


    public function getAntibioticFreeFarmReport($filterBy)
    {

        $qb = $this->createQueryBuilder('e');

        $this->handleSearchFilterBetween($qb, $filterBy);
        $qb->select('e.id','e.totalStockedChicksPcs', 'e.totalFeedUsedKg', 'e.mortality', 'e.totalBroilerWeightKg','e.ageDays', 'e.fcr', 'e.reportingMonth', 'e.hatchingDate');
        $qb->addSelect('agent.name AS agentName', 'agent.address AS agentAddress', 'agent.mobile AS agentMobile');
        $qb->addSelect('farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('hatchery.name AS hatcheryName');
        $qb->addSelect('breed.name AS breedName');

        $qb->join('e.agent', 'agent');
        $qb->join('e.customer', 'farmer');
        $qb->join('e.hatchery', 'hatchery');
        $qb->join('e.breed', 'breed');

        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result){
            $data[$result['agentName']][] = $result;
        }
        return $data;
    }

    public function getAntibioticFreeFarmCost()
    {

        $qb = $this->createQueryBuilder('e');

        $qb->select('e.id', 'antibioticFreeFarmMedicineOrCost.costType', 'SUM(antibioticFreeFarmMedicineOrCost.price) AS totalPrice');
        $qb->join('e.antibioticFreeFarmMedicineOrCost', 'antibioticFreeFarmMedicineOrCost');
        $qb->groupBy('antibioticFreeFarmMedicineOrCost.costType');
        $qb->addGroupBy('e.id');
        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result){
            $data[$result['id']][$result['costType']] = $result['totalPrice'];
        }
        return $data;
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

    public function getAntibioticFreeFarmByEmployeeAndDate($report, $filterBy)
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

            $qb->addSelect( 'district.name AS agentDistrictName');

            $qb->addSelect('hatchery.name AS hatcheryName');
            $qb->addSelect('breed.name AS breedBame');
            $qb->addSelect('feed.name AS feedName');

            $qb->join('e.employee', 'employee');
            $qb->leftJoin('employee.designation', 'designation');
            $qb->leftJoin('e.customer','customer');
            $qb->leftJoin('e.agent', 'agent');
            $qb->leftJoin('agent.district', 'district');
            $qb->leftJoin('e.hatchery', 'hatchery');
            $qb->leftJoin('e.breed', 'breed');
            $qb->leftJoin('e.feed', 'feed');
            $qb->where('e.report =:report')->setParameter('report',$report);

            $startDate = isset($filterBy['startDate'])&&$filterBy['startDate']!=''? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00': '';
            $endDate = isset($filterBy['endDate']) && $filterBy['endDate']!=''? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59': '';

            $employee = isset($filterBy['employeeId'])&&$filterBy['employeeId']!=''? $filterBy['employeeId']: '';
            if (!empty($employee)){
                $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee);
            }

            if (!empty($startDate) && !empty($endDate)){
                $qb->andWhere('e.reportingMonth >= :reportingMonthStart')->setParameter('reportingMonthStart', $startDate);
                $qb->andWhere('e.reportingMonth <= :reportingMonthEnd')->setParameter('reportingMonthEnd', $endDate);
            }
            $qb->orderBy('e.reportingMonth','ASC');

            $results = $qb->getQuery()->getArrayResult();
            if($results){
                foreach ($results as $result){
                    $monthYear = $result['reportingMonth']->format('F-Y');
                    $returnArray[$result['employeeId']]['name']=$result['employeeName'];
                    $returnArray[$result['employeeId']]['employeeDesignationName']=$result['employeeDesignationName'];
                    $returnArray[$result['employeeId']]['details'][$monthYear][]=$result;
                }
            }
        }
//        dd($returnArray);
        return $returnArray;
    }

}
