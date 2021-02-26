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

//use Doctrine\ORM\EntityRepository;
use Terminalbd\CrmBundle\Repository\BaseRepository;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class CattleFarmVisitRepository extends BaseRepository
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

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getCattleFarmVisitReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('crmCattleFarmVisitDetails.visitingDate', 'crmCattleFarmVisitDetails.cattlePopulationOx', 'crmCattleFarmVisitDetails.cattlePopulationCow', 'crmCattleFarmVisitDetails.cattlePopulationCalf', 'crmCattleFarmVisitDetails.avgMilkYieldPerDay', 'crmCattleFarmVisitDetails.conceptionRate', 'crmCattleFarmVisitDetails.fodderGreenGrassKg', 'crmCattleFarmVisitDetails.fodderStrawKg','crmCattleFarmVisitDetails.typeOfConcentrateFeed','crmCattleFarmVisitDetails.marketPriceMilkPerLiter','crmCattleFarmVisitDetails.marketPriceMeatPerKg','crmCattleFarmVisitDetails.remarks AS comments');

        $qb->addSelect('customer.name AS customerName', 'customer.mobile AS cusomerMobile', 'customer.address AS customerAddress');
        $qb->addSelect('location.name AS customerUpazila');
        $qb->addSelect('locationParent.name AS customerDistrict');
        $qb->addSelect('employee.name AS employeeName');



        $qb->leftJoin('e.crmCattleFarmVisitDetails', 'crmCattleFarmVisitDetails');
        $qb->leftJoin('crmCattleFarmVisitDetails.customer', 'customer');
        $qb->leftJoin('customer.location', 'location');
        $qb->leftJoin('location.parent', 'locationParent');
        $this->handleSearchFilterBetween($qb, $filterBy);

        $results = $qb->getQuery()->getArrayResult();
        return $results;

//        dd($results);


    }
}
