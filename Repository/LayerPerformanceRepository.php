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
class LayerPerformanceRepository extends BaseRepository
{

    public function getLayerPerformanceReportByReportingDateAndFeedType($report, $employee)
    {
        if($report&&$employee){
            $startDate = date('Y-m-01', strtotime('now'));
            $endDate = date('Y-m-t', strtotime('now'));
            $query = $this->createQueryBuilder('lpr')
                ->where('lpr.reportingMonth >= :startDate')
                ->andWhere('lpr.reportingMonth <= :endDate')
                ->andWhere('lpr.report = :report')
//                ->andWhere('lpr.customer = :customer')
                ->andWhere('lpr.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }
    public function getLayerPerformanceReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $this->handleSearchFilterBetween($qb, $filterBy);

        $results = $qb->getQuery()->getResult();

       // $results['month'] = $results[0]->getCreated()->format('F-Y');
//        dd($results);

        return $results;


    }
}
