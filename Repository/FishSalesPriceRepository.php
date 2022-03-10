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

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FishSalesPriceRepository extends BaseRepository
{

    public function getFishSalesPriceByCreatedDateAndEmployee($year, $month, $employee)
    {
        if($year&&$employee){
            $query = $this->createQueryBuilder('fsp')
                ->where('fsp.year IN (:year)')
                ->andWhere('fsp.monthName IN (:month)')
                ->andWhere('fsp.employee = :employee')
                ->setParameters(array('year'=>$year, 'month'=>$month, 'employee'=>$employee));

            $resutls = $query->getQuery()->getResult();
            $returnArray=[];
            if($resutls){
                /* @var FishSalesPrice $value*/
                foreach ($resutls as $value){
                    $returnArray['items'][$value->getYear()][$value->getMonthName()][$value->getFishSize()->getId()]=$value;
                }
            }

            return $returnArray;
        }
        return array();
    }

    public function getFishSalesPriceReport($filterBy, User $loggedUser)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;
        $employeeId = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.employee', 'employee');
        $qb->join('e.fishSize', 'fishSize');
        $qb->leftJoin('employee.designation', 'designation');

        $qb->select('e.id AS eId','e.price','e.year','e.monthName');
        $qb->addSelect('employee.id empId','employee.userId', 'employee.name as employeeName', 'designation.name AS designationName');
        $qb->addSelect('fishSize.id as fsId');
        $rolesString = implode($loggedUser->getRoles(), '_');

        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }
        if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }
        if(isset($filterBy['year']) && $filterBy['year']){
            $currentYear= $filterBy['year'];
        }else{
            $currentYear = date('Y');
        }

        $qb->andWhere('e.year = :year')->setParameter('year',$currentYear);

        $results = $qb->getQuery()->getArrayResult();

        $returnArray = [];
        if($results){
            foreach ($results as $result){

                $returnArray[$result['year']][$result['empId']]['userId']=$result['userId'];
                $returnArray[$result['year']][$result['empId']]['employeeName']=$result['employeeName'];
                $returnArray[$result['year']][$result['empId']]['employeeDesignationName']=$result['designationName'];
                $returnArray[$result['year']][$result['empId']]['details'][$result['monthName']][$result['fsId']]=$result;
            }
        }

        return $returnArray;
    }
}
