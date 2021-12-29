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
class FcrDetailsRepository extends BaseRepository
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

            return $query->getQuery()->getResult();
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

//        $qb->select('e.fcrOfFeed', 'e.reportingMonth');
        $qb->select('e.fcrOfFeed', 'e.reportingMonth', 'e.hatchingDate AS hatching_date', 'e.totalBirds AS total_birds', 'e.ageDay AS age', 'e.mortalityPes AS mortality_pes', 'e.mortalityPercent AS mortality_percent', 'e.weight', 'e.feedConsumptionTotalKg AS total_feed_cons', 'e.feedConsumptionPerBird AS feed_cons_per_bird', 'e.fcrWithoutMortality AS without_mortality', 'e.fcrWithMortality AS with_mortality', 'e.proDate AS pro_date', 'e.batchNo AS batch_no', 'e.remarks');

        $qb->addSelect('agent.name AS agent_name', 'agent.address AS agent_address');

        $qb->addSelect('employee.name AS employee_name');

        $qb->addSelect( 'district.name AS agent_district');

        $qb->addSelect('hatchery.name AS hatchery_name');

        $qb->addSelect('breed.name AS breed_name');
        $qb->addSelect('feed.name AS feed_name');
        $qb->addSelect('feed_mill.name AS feed_mill_name');
        $qb->addSelect('feed_type.name AS feed_type_name');

        $qb->leftJoin('e.agent', 'agent');
        $qb->leftJoin('agent.district', 'district');
        $qb->leftJoin('e.hatchery', 'hatchery');
        $qb->leftJoin('e.breed', 'breed');
        $qb->leftJoin('e.feed', 'feed');
        $qb->leftJoin('e.feedMill', 'feed_mill');
        $qb->leftJoin('e.feedType', 'feed_type');

        $this->handleSearchFilterBetween($qb, $filterBy);

        $results = $qb->getQuery()->getArrayResult();
//        dd($results);
        return($results);


    }


    public function getFcrDetailsByEmployee($report, $filterBy)
    {
        $results=null;
        if(!empty($report)){
            $qb = $this->createQueryBuilder('e');

            $qb->select('e.fcrOfFeed', 'e.reportingMonth', 'e.hatchingDate AS hatching_date', 'e.totalBirds AS total_birds', 'e.ageDay AS age', 'e.mortalityPes AS mortality_pes', 'e.mortalityPercent AS mortality_percent', 'e.weight', 'e.feedConsumptionTotalKg AS total_feed_cons', 'e.feedConsumptionPerBird AS feed_cons_per_bird', 'e.fcrWithoutMortality AS without_mortality', 'e.fcrWithMortality AS with_mortality', 'e.proDate AS pro_date', 'e.batchNo AS batch_no', 'e.remarks');

            $qb->addSelect('agent.name AS agent_name', 'agent.address AS agent_address');

            $qb->addSelect('employee.name AS employee_name');

            $qb->addSelect( 'district.name AS agent_district');

            $qb->addSelect('hatchery.name AS hatchery_name');

            $qb->addSelect('breed.name AS breed_name');
            $qb->addSelect('feed.name AS feed_name');
            $qb->addSelect('feed_mill.name AS feed_mill_name');
            $qb->addSelect('feed_type.name AS feed_type_name');

            $qb->leftJoin('e.employee','employee');
            $qb->leftJoin('e.agent', 'agent');
            $qb->leftJoin('agent.district', 'district');
            $qb->leftJoin('e.hatchery', 'hatchery');
            $qb->leftJoin('e.breed', 'breed');
            $qb->leftJoin('e.feed', 'feed');
            $qb->leftJoin('e.feedMill', 'feed_mill');
            $qb->leftJoin('e.feedType', 'feed_type');

            $startDate = isset($filterBy['startDate'])? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00': '';
            $endDate = isset($filterBy['endDate'])? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59': '';

            $employee = isset($filterBy['employeeId'])? $filterBy['employeeId']: '';
            if (!empty($employee)){
                $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee);
            }

            if (!empty($startDate) && !empty($endDate)){
                $qb->andWhere('e.reportingMonth >= :reportingMonthStart')->setParameter('reportingMonthStart', $startDate);
                $qb->andWhere('e.reportingMonth <= :reportingMonthEnd')->setParameter('reportingMonthEnd', $endDate);
            }

            $results = $qb->getQuery()->getArrayResult();
        }

        dd($results);
        return($results);


    }

    public function getMonthlyFcrAfterSaleTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');
        $qb->join('e.employee', 'employee');
        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.fcrOfFeed = :fcrFeed')->setParameter('fcrFeed', 'AFTER');
        $qb->andWhere('e.reportingMonth >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.reportingMonth <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }

    public function getMonthlyBroilerBeforeSaleTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');

        $qb->join('e.employee', 'employee');
        $qb->join('e.report', 'report');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.fcrOfFeed = :fcrFeed')->setParameter('fcrFeed', 'BEFORE');
        $qb->andWhere('e.reportingMonth >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.reportingMonth <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);
        $qb->andWhere('report.slug = :slug')->setParameter('slug', 'fcr-before-sale-boiler');

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }

}
