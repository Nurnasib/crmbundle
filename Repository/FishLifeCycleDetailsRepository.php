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
use Terminalbd\CrmBundle\Entity\FishLifeCycleDetails;

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

    public function getFishLifeCycleDetails($lifeCycleSlug, $filterBy, User $loggedUser)
    {
        $startDate = $filterBy['startDate'] ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : date('Y-m-01');
        $endDate = $filterBy['endDate'] ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : date('Y-m-t');
//dd($startDate);
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.fishLifeCycle', 'fish_life_cycle');
//        $qb->leftJoin('e.fishLifeCycleDetailSpecies', 'fish_life_cycle_detail_species');
        $qb->leftJoin('e.feed', 'feed');
        $qb->leftJoin('e.hatchery', 'hatchery');
//        $qb->leftJoin('fish_life_cycle_detail_species.feedType', 'feed_type');
//        $qb->leftJoin('fish_life_cycle_detail_species.mainCultureSpecies', 'main_culture_species');
        $qb->join('fish_life_cycle.employee', 'employee');
        $qb->join('fish_life_cycle.report', 'report');
        $qb->join('fish_life_cycle.customer', 'customer');


        $qb->where('report.slug = :reportSlug')->setParameter('reportSlug', $lifeCycleSlug);

        $employee = isset($filterBy['employeeId'])? $filterBy['employeeId']: '';
        if (!empty($employee)){
            $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee);
        }

        $rolesString = implode('_', $loggedUser->getRoles());
        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }elseif (!str_contains($rolesString, 'ADMIN') && in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){

            $employeeIdsByLineManager = $this->_em->getRepository(User::class)->getEmployeesByLineManager($loggedUser);
            $employeeIs=[];
            if($employeeIdsByLineManager){
                $employeeIs=$employeeIdsByLineManager;
            }
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIs);
        }
//        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('fish_life_cycle.reportingMonth >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('fish_life_cycle.reportingMonth <= :endDate')->setParameter('endDate', $endDate);


        $results = $qb->getQuery()->getResult();

        $data = [];

        /* @var FishLifeCycleDetails $result*/
        foreach ($results as $result) {
            $month = $result->getFishLifeCycle()->getReportingMonth()->format('m-F-Y');
            $employeeId = $result->getFishLifeCycle()->getEmployee()->getId();
            $employeeUserId = $result->getFishLifeCycle()->getEmployee()->getUserId();
            $employeeName = $result->getFishLifeCycle()->getEmployee()->getName();
            $employeeDesignation = $result->getFishLifeCycle()->getEmployee()->getDesignation()?$result->getFishLifeCycle()->getEmployee()->getDesignation()->getName():'';
            $employeeRegion = $result->getFishLifeCycle()->getEmployee()->getRegional()?$result->getFishLifeCycle()->getEmployee()->getRegional()->getName():'';

            $data['employeeInfo'][$month][$employeeId] = ['name'=>$employeeName, 'employeeUserId'=>$employeeUserId, 'designationName'=>$employeeDesignation, 'regionName'=>$employeeRegion];
            $data['records'][$month][$employeeId][] = $result;
        }

        return $data;


    }

}
