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
class CostBenefitAnalysisForLessCostingFarmRepository extends BaseRepository
{
    public function getCostBenefitAnalysisByReportingMonthEmployeeCustomerAndReport($report, $employee, $customer, $reportingMonth)
    {
        if($report&&$employee&&$customer&&$reportingMonth){
            $startDate = date('Y-m-01', strtotime($reportingMonth));
            $endDate = date('Y-m-t', strtotime($reportingMonth));
            $query = $this->createQueryBuilder('cba')
                ->where('cba.reportingMonth >= :startDate')
                ->andWhere('cba.reportingMonth <= :endDate')
                ->andWhere('cba.report = :report')
                ->andWhere('cba.customer = :customer')
                ->andWhere('cba.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'customer'=>$customer, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getCostBenefitAnalysisReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.hatchingDate','e.totalStockedChicksPcs','e.totalFeedUsedKg','e.totalBroilerWeightKg','e.mortality','e.ageDays','e.fcr','e.itemPricePerPcs','e.feedPricePerKg','e.broilerOrFishPricePerKg','e.totalMedicineCost','e.totalVaccineCost','e.litterOrPondRentCost','e.electricityAndFuelCost','e.labourCost','e.transportCost','e.reportingMonth');
        $qb->addSelect('agent.name AS agentName','agent.address AS agentAddress', 'agent.mobile AS agentMobile');
        $qb->addSelect('farmer.name AS farmerName','farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('hatchery.name AS hatcheryName');
        $qb->addSelect('breed.name AS breedName');

        $qb->leftJoin('e.agent', 'agent');
        $qb->leftJoin('e.hatchery', 'hatchery');
        $qb->leftJoin('e.breed', 'breed');

        $this->handleSearchFilterBetween($qb, $filterBy);
        $results = $qb->getQuery()->getResult();
        $data = [];
        foreach ($results as $result){
//            $month = $result['reportingMonth']->format('F-Y');
            $data[$result['farmerName']] = $result;
        }
//        dd($data);
        return $data;
    }

    public function getMonthlyLessCostingFarmOrSkillFarmDevelopTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');

        $qb->join('e.employee', 'employee');
        $qb->join('e.report', 'report');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.reportingMonth >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.reportingMonth <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);
        $qb->andWhere('report.settingType = :settingType')->setParameter('settingType', 'FARMER_REPORT');
        $qb->andWhere('report.slug = :slug')->setParameter('slug', 'less-costing-farm-poultry');

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }

}
