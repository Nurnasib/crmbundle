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

}
