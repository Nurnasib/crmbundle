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

use App\Entity\User;
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

    public function getExitingCheckTilapiaFrySalesForOtherAgentByCreatedDateEmployeeFeedMonthYear($employee, $month, $year, $feed, $type)
    {
        if($employee){
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
            $query = $this->createQueryBuilder('tfs')
                ->select('tfs.id')
                ->where('tfs.createdAt >= :startDate')
                ->andWhere('tfs.createdAt <= :endDate')
                ->andWhere('tfs.employee = :employee')
                ->andWhere('tfs.feed = :feed')
                ->andWhere('tfs.monthName = :month')
                ->andWhere('tfs.year = :year')
                ->andWhere('tfs.type = :type')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'feed'=>$feed, 'employee'=>$employee, 'month'=>$month, 'year'=>$year, 'type'=>$type));

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

    public function getCompetitorsTilapiaFrySalesOtherAgentByEmployeeMonthYear($employee, $month, $year)
    {
        if($year&&$employee){
            $query = $this->createQueryBuilder('tfs')
                ->select('tfs.id', 'tfs.monthName', 'tfs.year','SUM(tfs.quantity) as qty')
                ->addSelect('f.id as fId','f.name as feedName')
                ->join('tfs.feed','f')
                ->where('tfs.employee = :employee')
                ->andWhere('tfs.agent IS NULL')
                ->andWhere('tfs.monthName IN (:month)')
                ->andWhere('tfs.year = :year')
                ->andWhere('tfs.type = :type')
                ->setParameters(array('employee'=>$employee, 'month'=>$month, 'year'=>$year, 'type'=>TilapiaFrySales::TILAPIA_FRY_SALES_OTHER))
                ->groupBy('tfs.monthName','tfs.year','f.id')
            ;

            $results = $query->getQuery()->getResult();

            $returnArray=[];
            /* @var TilapiaFrySales $result*/
            foreach ($results as $result){
                $returnArray['items'][$result['fId']][$result['monthName']]=$result;
                $returnArray['feeds'][$result['fId']]= $result['feedName'];
                $returnArray['grandTotal'][$result['monthName']][]=$result['qty'];

            }

           return $returnArray;
        }
        return array();
    }

    public function getTilapiaFrySalesReport($filterBy, User $loggedUser)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;
        $employeeId = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;

        $queryNourishSales = $this->createQueryBuilder('tfs');
        $queryNourishSales->select('tfs.id', 'tfs.monthName', 'tfs.year','SUM(tfs.quantity) as qty');
        $queryNourishSales->addSelect('a.id as aId','a.name as agentName');
        $queryNourishSales->addSelect('employee.id as empId','employee.name as employeeName','employee.userId');
        $queryNourishSales->addSelect('designation.name as employeeDesignationName');
        $queryNourishSales->join('tfs.agent','a');
        $queryNourishSales->join('tfs.employee','employee');
        $queryNourishSales->leftJoin('employee.designation', 'designation');
        $queryNourishSales->where('tfs.type = :type')->setParameter('type',TilapiaFrySales::TILAPIA_FRY_SALES_NOURISH);

        $rolesString = implode($loggedUser->getRoles(), '_');
        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $queryNourishSales->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }
        if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
            $queryNourishSales->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        if(isset($filterBy['year']) && $filterBy['year']){
            $year= $filterBy['year'];
        }else{
            $year = date('Y');
        }
        $queryNourishSales->andWhere('tfs.year = :year')->setParameter('year',$year);

        $queryNourishSales->groupBy('employee.id','tfs.monthName','tfs.year','a.id');

        $nourishResults = $queryNourishSales->getQuery()->getResult();

        $returnArray=[];
        if($nourishResults){
            /* @var TilapiaFrySales $result*/
            foreach ($nourishResults as $result){
                $returnArray[$result['year']][$result['empId']]['userId']=$result['userId'];
                $returnArray[$result['year']][$result['empId']]['employeeName']=$result['employeeName'];
                $returnArray[$result['year']][$result['empId']]['employeeDesignationName']=$result['employeeDesignationName'];

                $returnArray[$result['year']][$result['empId']]['items'][$result['aId']][$result['monthName']]=$result;
                $returnArray[$result['year']][$result['empId']]['agents'][$result['aId']]= $result['agentName'];
                $returnArray[$result['year']][$result['empId']]['grandTotal'][$result['monthName']][]=$result['qty'];
            }
        }

//        competitor nourish agent

        $queryCompetitorNourishAgent = $this->createQueryBuilder('tfs');
        $queryCompetitorNourishAgent->select('tfs.id', 'tfs.monthName', 'tfs.year','SUM(tfs.quantity) as qty');
        $queryCompetitorNourishAgent->addSelect('a.id as aId','a.name as agentName');
        $queryCompetitorNourishAgent->addSelect('employee.id as empId','employee.name as employeeName','employee.userId');
        $queryCompetitorNourishAgent->addSelect('designation.name as employeeDesignationName');
        $queryCompetitorNourishAgent->addSelect('feed.id as fId','feed.name as feedName');
        $queryCompetitorNourishAgent->join('tfs.agent','a');
        $queryCompetitorNourishAgent->join('tfs.feed','feed');
        $queryCompetitorNourishAgent->join('tfs.employee','employee');
        $queryCompetitorNourishAgent->leftJoin('employee.designation', 'designation');
        $queryCompetitorNourishAgent->where('tfs.type = :type')->setParameter('type',TilapiaFrySales::TILAPIA_FRY_SALES_OTHER);

        $rolesString = implode($loggedUser->getRoles(), '_');
        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $queryCompetitorNourishAgent->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }
        if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
            $queryCompetitorNourishAgent->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        if(isset($filterBy['year']) && $filterBy['year']){
            $year= $filterBy['year'];
        }else{
            $year = date('Y');
        }
        $queryCompetitorNourishAgent->andWhere('tfs.year = :year')->setParameter('year',$year);
        $queryCompetitorNourishAgent->groupBy('employee.id','tfs.monthName','tfs.year','a.id','feed.id');

        $competitorResults = $queryCompetitorNourishAgent->getQuery()->getResult();

        if($competitorResults){
            /* @var TilapiaFrySales $result*/
            foreach ($competitorResults as $result){
                $returnArray[$result['year']][$result['empId']]['userId']=$result['userId'];
                $returnArray[$result['year']][$result['empId']]['employeeName']=$result['employeeName'];
                $returnArray[$result['year']][$result['empId']]['employeeDesignationName']=$result['employeeDesignationName'];

                $returnArray[$result['year']][$result['empId']]['competitorItems'][$result['monthName']][$result['aId']][$result['fId']]=$result;
                $returnArray[$result['year']][$result['empId']]['competitorGrandTotal'][$result['monthName']][$result['fId']][]=$result['qty'];
                $returnArray[$result['year']][$result['empId']]['competitorAgents'][$result['monthName']][$result['aId']]= $result['agentName'];
                $returnArray[$result['year']][$result['empId']]['competitorFeeds'][$result['fId']]= $result['feedName'];
                $returnArray[$result['year']][$result['empId']]['competitorMonths'][$result['monthName']] = $result['monthName'];
            }
        }

//        competitor other agent

        $queryCompetitorOtherAgent = $this->createQueryBuilder('tfs');
        $queryCompetitorOtherAgent->select('tfs.id', 'tfs.monthName', 'tfs.year','SUM(tfs.quantity) as qty');
        $queryCompetitorOtherAgent->addSelect('employee.id as empId','employee.name as employeeName','employee.userId');
        $queryCompetitorOtherAgent->addSelect('designation.name as employeeDesignationName');
        $queryCompetitorOtherAgent->addSelect('feed.id as fId','feed.name as feedName');
        $queryCompetitorOtherAgent->join('tfs.feed','feed');
        $queryCompetitorOtherAgent->join('tfs.employee','employee');
        $queryCompetitorOtherAgent->leftJoin('employee.designation', 'designation');
        $queryCompetitorOtherAgent->where('tfs.type = :type')->setParameter('type',TilapiaFrySales::TILAPIA_FRY_SALES_OTHER);
        $queryCompetitorOtherAgent->andWhere('tfs.agent IS NULL');

        $rolesString = implode($loggedUser->getRoles(), '_');
        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $queryCompetitorOtherAgent->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }
        if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
            $queryCompetitorOtherAgent->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        if(isset($filterBy['year']) && $filterBy['year']){
            $year= $filterBy['year'];
        }else{
            $year = date('Y');
        }
        $queryCompetitorOtherAgent->andWhere('tfs.year = :year')->setParameter('year',$year);
        $queryCompetitorOtherAgent->groupBy('employee.id','tfs.monthName','tfs.year','feed.id');

        $otherAgentresults = $queryCompetitorOtherAgent->getQuery()->getResult();

        if($otherAgentresults){
            /* @var TilapiaFrySales $result*/
            foreach ($otherAgentresults as $result){
                $returnArray[$result['year']][$result['empId']]['userId']=$result['userId'];
                $returnArray[$result['year']][$result['empId']]['employeeName']=$result['employeeName'];
                $returnArray[$result['year']][$result['empId']]['employeeDesignationName']=$result['employeeDesignationName'];

                $returnArray[$result['year']][$result['empId']]['otherAgentMonths'][$result['monthName']]= $result['monthName'];
                $returnArray[$result['year']][$result['empId']]['otherAgentItems'][$result['fId']][$result['monthName']]=$result;
                $returnArray[$result['year']][$result['empId']]['otherAgentFeeds'][$result['fId']]= $result['feedName'];
                $returnArray[$result['year']][$result['empId']]['otherAgentGrandTotal'][$result['monthName']][]=$result['qty'];
            }
        }

        return $returnArray;



    }

}
