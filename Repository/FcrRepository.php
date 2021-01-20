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
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Repository\BaseRepository;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FcrRepository extends BaseRepository
{

    public function getFcrReportByReportingDateAndFeedType($data, $report, $employee)
    {
        if(isset($data) && $report && $employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('f')
                ->where('f.reportingMonth >= :startDate')
                ->andWhere('f.reportingMonth <= :endDate')
                ->andWhere('f.fcrOfFeed = :type')
                ->andWhere('f.report = :report')
                ->andWhere('f.employee = :employee')
//                ->andWhere('f.customer = :customer')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'type'=>$data, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }


    public function getFcrReportByReportingDateReportAndEmployeeForAfter($data, $report, $employee)
    {
        if(isset($data) && $report && $employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('f')
                ->where('f.reportingMonth >= :startDate')
                ->andWhere('f.reportingMonth <= :endDate')
                ->andWhere('f.fcrOfFeed = :type')
                ->andWhere('f.report = :report')
                ->andWhere('f.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'type'=>$data, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }


    public function getFcrReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select('e.fcrOfFeed', 'e.reportingMonth');
        $qb->addSelect('agent.name AS agent_name', 'agent.address AS agent_address');
        $qb->addSelect('employee.name AS employee_name');
        $qb->addSelect( 'district.name AS agent_district');

        $qb->addSelect('fcr_details.hatchingDate AS hatching_date', 'fcr_details.totalBirds AS total_birds', 'fcr_details.ageDay AS age', 'fcr_details.mortalityPes AS mortality_pes', 'fcr_details.mortalityPercent AS mortality_percent', 'fcr_details.weight', 'fcr_details.feedConsumptionTotalKg AS total_feed_cons', 'fcr_details.feedConsumptionPerBird AS feed_cons_per_bird', 'fcr_details.fcrWithoutMortality AS without_mortality', 'fcr_details.fcrWithMortality AS with_mortality', 'fcr_details.proDate AS pro_date', 'fcr_details.batchNo AS batch_no', 'fcr_details.remarks');

        $qb->addSelect('hatchery.name AS hatchery_name');

        $qb->addSelect('breed.name AS breed_name');
        $qb->addSelect('feed.name AS feed_name');
        $qb->addSelect('feed_mill.name AS feed_mill_name');
        $qb->addSelect('feed_type.name AS feed_type_name');

        $qb->leftJoin('e.fcrDetails', 'fcr_details');
        $qb->leftJoin('fcr_details.agent', 'agent');
        $qb->leftJoin('agent.district', 'district');
        $qb->leftJoin('fcr_details.hatchery', 'hatchery');
        $qb->leftJoin('fcr_details.breed', 'breed');
        $qb->leftJoin('fcr_details.feed', 'feed');
        $qb->leftJoin('fcr_details.feedMill', 'feed_mill');
        $qb->leftJoin('fcr_details.feedType', 'feed_type');

        $this->handleSearchFilterBetween($qb, $filterBy);

        $results = $qb->getQuery()->getArrayResult();
//        dd($results);
        return($results);


    }
}
