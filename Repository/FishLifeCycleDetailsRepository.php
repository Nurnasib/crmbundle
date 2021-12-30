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
class FishLifeCycleDetailsRepository extends EntityRepository
{
    public function getFishLifeCycleDetailsByReportingDateAndEmployee( $report, $employee)
    {
        if($report && $employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('flcd')
                ->join('flcd.fishLifeCycle','f')
                ->where('f.reportingMonth >= :startDate')
                ->andWhere('f.reportingMonth <= :endDate')
                ->andWhere('f.report = :report')
                ->andWhere('f.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'employee'=>$employee))
                ->orderBy('f.reportingMonth','DESC');

            return $query->getQuery()->getResult();
        }
        return array();
    }

    public function getFeedCompanyByFishLifeCycle($fishLifeCycle)
    {
        $query = $this->createQueryBuilder('flcd')
            ->addSelect('feed.id AS feedId')
            ->addSelect('feed.name AS feed_name')
            ->join('flcd.feed', 'feed')
            ->where('flcd.fishLifeCycle = :fishLifeCycle')
            ->setParameter('fishLifeCycle',$fishLifeCycle);

        $results = $query->getQuery()->getResult();

        $arrayReturn = [];

        foreach ($results as $result){
            $arrayReturn[$result['feedId']]= $result['feed_name'];
        }

        return $arrayReturn;
    }

    public function getFishLifeCycleDetailsByFishLifeCycle($fishLifeCycle)
    {
        $query = $this->createQueryBuilder('flcd')
            ->addSelect('flcd.id AS flcdId')
            ->addSelect('feed.id AS feedId')
            ->join('flcd.feed', 'feed')
            ->where('flcd.fishLifeCycle = :fishLifeCycle')
            ->setParameter('fishLifeCycle',$fishLifeCycle);

        $results = $query->getQuery()->getResult();

        $arrayReturn = [];

        foreach ($results as $result){
            $arrayReturn[$result['feedId']]= $result[0];
        }

        return $arrayReturn;
    }

    public function getFishLifeCycleDetails($lifeCycleSlug, $filterBy)
    {
        $startDate = $filterBy['startDate'] ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
        $endDate = $filterBy['endDate'] ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.fishLifeCycle', 'fish_life_cycle');
        $qb->leftJoin('e.fishLifeCycleDetailSpecies', 'fish_life_cycle_detail_species');
        $qb->leftJoin('e.feed', 'feed');
        $qb->leftJoin('e.hatchery', 'hatchery');
        $qb->leftJoin('fish_life_cycle_detail_species.feedType', 'feed_type');
        $qb->leftJoin('fish_life_cycle_detail_species.mainCultureSpecies', 'main_culture_species');
        $qb->join('fish_life_cycle.employee', 'employee');
        $qb->join('fish_life_cycle.report', 'report');
        $qb->join('fish_life_cycle.customer', 'customer');

        $qb->select('e AS details');
        $qb->addSelect('customer.id AS customerId', 'customer.name AS customerName', 'customer.address AS customerAddress', 'customer.mobile AS customerMobile');
        $qb->addSelect('report.name AS reportName');
        $qb->addSelect('fish_life_cycle.reportingMonth','fish_life_cycle.id AS lifeCycleId');
        $qb->addSelect('feed_type.name AS feedTypeName');
        $qb->addSelect('feed.name AS feedCompanyName');
        $qb->addSelect('main_culture_species.name AS speciesName');
        $qb->addSelect('hatchery.name AS hatcheryName');


        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('fish_life_cycle.reportingMonth >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('fish_life_cycle.reportingMonth <= :endDate')->setParameter('endDate', $endDate);
        $qb->andWhere('report.slug = :reportSlug')->setParameter('reportSlug', $lifeCycleSlug);


        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $month = $result['reportingMonth']->format('m-F-Y');

            $result['details']['feedTypeName'] = $result['feedTypeName'];
            $result['details']['feedCompanyName'] = $result['feedCompanyName'];
            $result['details']['customerName'] = $result['customerName'];
            $result['details']['customerAddress'] = $result['customerAddress'];
            $result['details']['customerMobile'] = $result['customerMobile'];
            $result['details']['speciesName'] = $result['speciesName'];
            $result['details']['hatcheryName'] = $result['hatcheryName'];

            $data[$month][] = $result['details'];
        }
        return $data;


    }

}
