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

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class CrmVIsitPlanRepository extends EntityRepository
{

    public function getMonthlyTourPlanByEmployeeAndDate($employeeId, $visitingDate=null, $type=null){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.visitingArea', 'e.visitDate', 'e.agentList', 'e.areaList', 'e.createdAt', 'e.agentInfo');
        $qb->addSelect('employee.id as employeeId', 'employee.name as employeeName' , 'employee.userId as employeeUserId' );
        $qb->addSelect('workingMode.id as workingModeId', 'workingMode.name as workingModeName');
        $qb->addSelect('designation.id as designationId', 'designation.name as designationName');
        $qb->join('e.employee','employee');
        $qb->leftJoin('employee.designation','designation');
        $qb->leftJoin('e.workingMode','workingMode');
        if($visitingDate && $visitingDate!=''){
            if($type=='monthly'){
                $qb->andWhere("DATE_FORMAT(e.visitDate,'%Y-%m') =:yearMonth")->setParameter('yearMonth', date('Y-m', strtotime($visitingDate.'-01')));
            }else {
                $qb->andWhere("DATE_FORMAT(e.visitDate,'%Y-%m-%d') =:yearMonth")->setParameter('yearMonth', date('Y-m-d', strtotime($visitingDate)));
            }
        }
        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);
        $qb->orderBy("DATE_FORMAT(e.visitDate,'%Y-%m')", "DESC");
        $qb->addOrderBy('e.visitDate', 'ASC');
        $results= $qb->getQuery()->getArrayResult();
        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $visitMonth=$result['visitDate']->format('Y-m');
                $visitDate=$result['visitDate']->format('Y-m-d');
                $returnArray['employee_id'] = $result['employeeId'];
                $returnArray['employeeInfo'] = [
                    'id' => $result['employeeId'],
                    'name' => $result['employeeName'],
                    'userId' => $result['employeeUserId'],
                    'designationId' => $result['designationId'],
                    'designationName' => $result['designationName']
                ];
                $returnArray['data'][$visitMonth][$visitDate]=[
                    'id' => $result['id'],
                    'visitingArea'=>$result['visitingArea'],
                    'agentList' => $result['agentList'],
                    'areaList' => $result['areaList']?$result['areaList']:[],
                    'agentInfo' => $result['agentInfo'],
                    'areaNameList' => $result['areaList'] && sizeof( $result['areaList'])>0?implode(', ', array_column($result['areaList'], 'areaName')):'',
                    'agentNameList' => $result['agentList'] && sizeof( $result['agentList'])>0?implode(', ', array_column($result['agentList'], 'agentName')):'',
                    'createdDate' => $result['createdAt']->format('Y-m-d'),
                    'workingMode'=>[
                        'id'=>$result['workingModeId'],
                        'name'=>$result['workingModeName']
                    ]
                ];
            }
        }
        return $returnArray;
    }

    public function getMonthlyTourPlanSummeryByEmployeeAndDate($employee, $visitingDate=null, User $loggedUser){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.visitingArea', 'e.visitDate', 'e.agentList', 'e.areaList', 'e.createdAt', 'e.agentInfo');
        $qb->addSelect('employee.id as employeeId', 'employee.name as employeeName' , 'employee.userId as employeeUserId' );
        $qb->addSelect('workingMode.id as workingModeId', 'workingMode.name as workingModeName');
        $qb->join('e.employee','employee');
        $qb->leftJoin('e.workingMode','workingMode');
        $qb->andWhere("DATE_FORMAT(e.visitDate,'%Y-%m') =:yearMonth")->setParameter('yearMonth', date('Y-m', strtotime($visitingDate.'-01')));
        $qb->andWhere('e.workingMode IS NOT NULL');


        $roleSplitArray = [];

        foreach ($loggedUser->getRoles() as $role) {
            $roleSplitArray = array_merge(explode('_', $role), $roleSplitArray);
        }
        if (in_array('ADMIN', $roleSplitArray) && !$employee) {
            $userRole = [];
            if (in_array('ROLE_CRM_POULTRY_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_POULTRY_USER');
            }
            if (in_array('ROLE_CRM_CATTLE_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_CATTLE_USER');
            }
            if (in_array('ROLE_CRM_AQUA_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_AQUA_USER');
            }
            if (in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_SALES_MARKETING_USER');
            }
            $query = '';
            foreach ($userRole as $key => $role) {
                if ($key !== 0) {
                    $query .= " OR ";
                }
                $query .= "employee.roles LIKE '%" . $role . "%'";

            }
            $qb->andWhere($query);

        } elseif (!in_array('ADMIN', $roleSplitArray) && in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles()) && !$employee) {
            $employeeIdsByLineManager = $this->_em->getRepository(User::class)->getEmployeesByLineManager($loggedUser);
            $employeeIs=[];
            if($employeeIdsByLineManager){
                $employeeIs=$employeeIdsByLineManager;
            }
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIs);
        } elseif (!in_array('ADMIN', $roleSplitArray) && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())) {
            $qb->andWhere('e.employee = :employee')->setParameter('employee', $loggedUser);
        }

        if($employee) {
            $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employee);
        }

        $qb->orderBy("DATE_FORMAT(e.visitDate,'%Y-%m')", "DESC");
        $qb->addOrderBy('e.visitDate', 'ASC');
        $results= $qb->getQuery()->getArrayResult();
        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $visitDate=$result['visitDate']->format('Y-m-d');
                $returnArray[$result['employeeId']][$visitDate]=$result;
            }
        }
        return $returnArray;
    }
    
    
    public function getFirstVisitPlanByEmployee($employeeId)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.visitingArea', 'e.visitDate', 'e.createdAt');
        $qb->addSelect('employee.id as employeeId', 'employee.name as employeeName', 'employee.userId as employeeUserId');
        $qb->addSelect('workingMode.id as workingModeId', 'workingMode.name as workingModeName');
        $qb->join('e.employee', 'employee');
        $qb->leftJoin('e.workingMode', 'workingMode');
        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);
        $qb->orderBy('e.visitDate', 'ASC');
        $qb->setMaxResults(1);
        $result = $qb->getQuery()->getOneOrNullResult();
        if ($result) {
            $result['expenseDate'] = $result['visitDate']->modify('-1 day');
        }
        return $result;
    }
    


}
