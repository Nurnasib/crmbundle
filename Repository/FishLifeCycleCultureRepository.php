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
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\KpiBundle\Entity\EmployeeBoard;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FishLifeCycleCultureRepository extends EntityRepository
{
    
    public function getFishLifeCycleCulture($lifeCycleSlug, $filterBy, User $loggedUser){
        $startDate = $filterBy['startDate'] ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : date('Y-m-01');
        $endDate = $filterBy['endDate'] ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : date('Y-m-t');

        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.fishLifeCycleCultureDetails','details');
        $qb->leftJoin('e.feed', 'feed');
        $qb->leftJoin('e.hatchery', 'hatchery');
        $qb->join('e.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->leftJoin('employee.regional', 'regional');
        $qb->join('e.report', 'report');
        $qb->join('e.customer', 'customer');
        $qb->leftJoin('e.agent', 'agent');
        $qb->leftJoin('e.mainCultureSpecies', 'mainCultureSpecies');
        $qb->leftJoin('e.otherCultureSpecies', 'otherCultureSpecies');
        $qb->leftJoin('e.feedType', 'feedType');

        $qb->select('e');
        $qb->addSelect('details as detail');
        $qb->addSelect('employee');
        $qb->addSelect('customer');
        $qb->addSelect('agent');
        $qb->addSelect('feed');
        $qb->addSelect('hatchery');
        $qb->addSelect('feedType');
        $qb->addSelect('mainCultureSpecies');
        $qb->addSelect('otherCultureSpecies');
        $qb->addSelect('report');
        $qb->addSelect('designation');
        $qb->addSelect('regional');

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
        $qb->andWhere('e.reportingDate >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.reportingDate <= :endDate')->setParameter('endDate', $endDate);


        $results = $qb->getQuery()->getArrayResult();
//dd($results);
        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $reportingMonth=$result['reportingDate']->format('m-F-Y');
                $employeeId= $result['employee']['id'];

                $employeeUserId = $result['employee']['userId'];
                $employeeName = $result['employee']['name'];
                $employeeDesignation = $result['employee']&&$result['employee']['designation']?$result['employee']['designation']['name']:'';
                $employeeRegion = $result['employee']&&$result['employee']['regional']?$result['employee']['regional']['name']:'';

                $returnArray['employeeInfo'][$reportingMonth][$employeeId] = ['name'=>$employeeName, 'employeeUserId'=>$employeeUserId, 'designationName'=>$employeeDesignation, 'regionName'=>$employeeRegion];
                $returnArray['records'][$reportingMonth][$employeeId][]=$result;

            }
        }
//        dd($returnArray);
        return $returnArray;
    }


}
