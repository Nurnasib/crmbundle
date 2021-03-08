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

}
