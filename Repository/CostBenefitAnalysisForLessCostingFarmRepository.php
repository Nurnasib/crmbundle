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
use Terminalbd\KpiBundle\Entity\EmployeeBoard;

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

    public function getLessCostingFarmByEmployeeAndDate($report, $filterBy)
    {
        $returnArray=[];
        if(!empty($report)){
            $qb = $this->createQueryBuilder('e');
            $qb->select('e.id as eId', 'e.ageDays', 'e.hatchingDate', 'e.reportingMonth', 'e.pondSize', 'e.fingerlingSize', 'e.harvestingSize');
            $qb->addSelect('e.totalStockedChicksPcs', 'e.totalFeedUsedKg', 'e.totalBroilerWeightKg', 'e.mortality', 'e.fcr');
            $qb->addSelect('e.itemPricePerPcs', 'e.feedPricePerKg', 'e.broilerOrFishPricePerKg', 'e.totalMedicineCost', 'e.totalVaccineCost');
            $qb->addSelect('e.usedBagPricePerPcs', 'e.litterOrPondRentCost', 'e.electricityAndFuelCost', 'e.labourCost', 'e.transportCost', 'e.otherCost');
            $qb->addSelect('e.totalPondPreparationCost', 'e.electricityAndFuelCost', 'e.labourCost', 'e.transportCost', 'e.otherCost');

            $qb->addSelect('agent.name AS agentName', 'agent.address AS agentAddress', 'agent.mobile AS agentMobile');

            $qb->addSelect('employee.id AS employeeId', 'employee.name AS employeeName');
            $qb->addSelect('designation.name AS employeeDesignationName');
            $qb->addSelect('customer.id AS customerId', 'customer.name AS customerName', 'customer.mobile AS customerMobile', 'customer.address AS customerAddress');

            $qb->addSelect( 'district.name AS agentDistrictName');

            $qb->addSelect('hatchery.name AS hatcheryName');
            $qb->addSelect('breed.name AS breedBame');
            $qb->addSelect('feed.name AS feedName');
            $qb->addSelect('species.name AS speciesName');

            $qb->join('e.employee', 'employee');
            $qb->leftJoin('employee.designation', 'designation');
            $qb->leftJoin('e.customer','customer');
            $qb->leftJoin('e.agent', 'agent');
            $qb->leftJoin('agent.district', 'district');
            $qb->leftJoin('e.hatchery', 'hatchery');
            $qb->leftJoin('e.breed', 'breed');
            $qb->leftJoin('e.feed', 'feed');
            $qb->leftJoin('e.species', 'species');
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


            /*$qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
            $qb->andWhere('e.reportingMonth >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
            $qb->andWhere('e.reportingMonth <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);
            $qb->andWhere('report.settingType = :settingType')->setParameter('settingType', 'FARMER_REPORT');
            $qb->andWhere('report.slug = :slug')->setParameter('slug', 'less-costing-farm-poultry');*/

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
