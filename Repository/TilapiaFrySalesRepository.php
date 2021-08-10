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
use Terminalbd\CrmBundle\Entity\FishSalesPrice;
use Terminalbd\CrmBundle\Entity\TilapiaFrySales;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class TilapiaFrySalesRepository extends BaseRepository
{
    public function getExitingCheckTilapiaFrySalesByCreatedDateEmployeeAgentMonthYear($employee, $month, $year, $agent, $type)
    {
        if($agent&&$employee){
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
            $query = $this->createQueryBuilder('tfs')
                ->select('tfs.id')
                ->where('tfs.createdAt >= :startDate')
                ->andWhere('tfs.createdAt <= :endDate')
                ->andWhere('tfs.employee = :employee')
                ->andWhere('tfs.agent = :agent')
                ->andWhere('tfs.monthName = :month')
                ->andWhere('tfs.year = :year')
                ->andWhere('tfs.type = :type')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'agent'=>$agent, 'employee'=>$employee, 'month'=>$month, 'year'=>$year, 'type'=>$type));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getExitingCheckTilapiaFrySalesByCreatedDateEmployeeAgentFeedMonthYear($employee, $month, $year, $agent, $feed, $type)
    {
        if($agent&&$employee){
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
            $query = $this->createQueryBuilder('tfs')
                ->select('tfs.id')
                ->where('tfs.createdAt >= :startDate')
                ->andWhere('tfs.createdAt <= :endDate')
                ->andWhere('tfs.employee = :employee')
                ->andWhere('tfs.agent = :agent')
                ->andWhere('tfs.feed = :feed')
                ->andWhere('tfs.monthName = :month')
                ->andWhere('tfs.year = :year')
                ->andWhere('tfs.type = :type')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'agent'=>$agent, 'feed'=>$feed, 'employee'=>$employee, 'month'=>$month, 'year'=>$year, 'type'=>$type));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getTilapiaFrySalesByEmployeeMonthYear($employee, $month, $year)
    {
        if($year&&$employee){
            $query = $this->createQueryBuilder('tfs')
                ->select('tfs.id', 'tfs.monthName', 'tfs.year','SUM(tfs.quantity) as qty','a.id as aId','a.name as agentName')
                ->join('tfs.agent','a')
                ->where('tfs.employee = :employee')
                ->andWhere('tfs.monthName IN (:month)')
                ->andWhere('tfs.year = :year')
                ->andWhere('tfs.type = :type')
                ->setParameters(array('employee'=>$employee, 'month'=>$month, 'year'=>$year, 'type'=>TilapiaFrySales::TILAPIA_FRY_SALES_NOURISH))
                ->groupBy('tfs.monthName','tfs.year','a.id')
            ;

            $results = $query->getQuery()->getResult();

            $returnArray=[];
            /* @var TilapiaFrySales $result*/
            foreach ($results as $result){
                $returnArray['items'][$result['aId']][$result['monthName']]=$result;
                $returnArray['agents'][$result['aId']]= $result['agentName'];
                $returnArray['grandTotal'][$result['monthName']][]=$result['qty'];
            }

           return $returnArray;
        }
        return array();
    }

    public function getCompetitorsTilapiaFrySalesByEmployeeMonthYear($employee, $month, $year)
    {
        if($year&&$employee){
            $query = $this->createQueryBuilder('tfs')
                ->select('tfs.id', 'tfs.monthName', 'tfs.year','SUM(tfs.quantity) as qty')
                ->addSelect('a.id as aId','a.name as agentName')
                ->addSelect('f.id as fId','f.name as feedName')
                ->join('tfs.agent','a')
                ->join('tfs.feed','f')
                ->where('tfs.employee = :employee')
                ->andWhere('tfs.monthName IN (:month)')
                ->andWhere('tfs.year = :year')
                ->andWhere('tfs.type = :type')
                ->setParameters(array('employee'=>$employee, 'month'=>$month, 'year'=>$year, 'type'=>TilapiaFrySales::TILAPIA_FRY_SALES_OTHER))
                ->groupBy('tfs.monthName','tfs.year','a.id','f.id')
            ;

            $results = $query->getQuery()->getResult();

            $returnArray=[];
            /* @var TilapiaFrySales $result*/
            foreach ($results as $result){
                $returnArray['items'][$result['monthName']][$result['aId']][$result['fId']]=$result;
                $returnArray['grandTotal'][$result['monthName']][$result['fId']][]=$result['qty'];
                $returnArray['agents'][$result['monthName']][$result['aId']]= $result['agentName'];
                $returnArray['feeds'][$result['fId']]= $result['feedName'];
            }

           return $returnArray;
        }
        return array();
    }

}
