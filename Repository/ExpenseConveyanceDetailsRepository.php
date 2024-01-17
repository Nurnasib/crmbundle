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
class ExpenseConveyanceDetailsRepository extends EntityRepository
{

    public function getTotalAmountConveyanceDetailsByExpense(User $user, $yearMonth=null, $batch=null){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.transportType', 'SUM(e.totalAmount) as totalAmount');
        $qb->addSelect('expense.id as expenseId');
        $qb->join('e.expense','expense');
        $qb->join('expense.employee','employee');
        if($yearMonth && $yearMonth!=''){
            $qb->andWhere("DATE_FORMAT(expense.expenseDate,'%Y-%m') =:yearMonth")->setParameter('yearMonth', $yearMonth);
        }

        if($batch && $batch!=''){
            $qb->andWhere('expense.expenseBatch =:batch')->setParameter('batch',$batch);
        }

        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $user->getId());


        $qb->groupBy('expense.id');
        $qb->addGroupBy('e.transportType');

        $results= $qb->getQuery()->getArrayResult();

        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $returnArray['grandTotal'][$result['transportType']][]=$result['totalAmount'];
                $returnArray['data'][$result['expenseId']][$result['transportType']]=$result;
            }
        }

        return $returnArray;
    }

    public function getTotalAmountMonthlyByEmployeeYear(User $user, $year=null){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.transportType', 'SUM(e.totalAmount) as totalAmount');
        $qb->addSelect('expense.id as expenseId');
        $qb->addSelect("DATE_FORMAT(expense.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(expense.expenseDate) as expenseYear', 'MONTH(expense.expenseDate) as expenseMonth');
        $qb->addSelect('employee.id as employeeAutoId');
        $qb->join('e.expense','expense');
        $qb->join('expense.employee','employee');

        $qb->where('expense.status >=:status')->setParameter('status',1);
        $qb->andWhere('expense.expenseDate IS NOT NULL');
        if($year && $year!=''){
            $qb->andWhere("DATE_FORMAT(expense.expenseDate,'%Y') =:year")->setParameter('year', $year);
        }
        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $user->getId());


        $qb->groupBy('expenseMonthYear');
        $qb->addGroupBy('e.transportType');

        $results= $qb->getQuery()->getArrayResult();

        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $returnArray[$result['employeeAutoId']][$result['expenseMonthYear']][$result['transportType']]=$result;
            }
        }

        return $returnArray;
    }

    public function getLastMileageByEmployeeDate($employeeId, $expenseDate)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.transportType', 'e.totalMileage', 'e.cumulativeTotalMileageOneHundred', 'e.cumulativeTotalMileageTwoHundred');
        $qb->join('e.expense', 'expense');
        $qb->join('expense.employee', 'employee');
        $qb->where('expense.expenseDate IS NOT NULL');
        $qb->andWhere("DATE_FORMAT(expense.expenseDate,'%Y-%m-%d') < :expenseDate")->setParameter('expenseDate', $expenseDate);

        $qb->andWhere('e.totalMileage > :totalMileage')->setParameter('totalMileage', 0);

        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);

        $qb->andWhere('e.transportType IN (:transportType)')->setParameter('transportType', ['car','motorcycle']);

        $qb->orderBy('expense.expenseDate', 'DESC');
        $qb->setMaxResults(1);
        $resutl = $qb->getQuery()->getOneOrNullResult();

        return $resutl;
    }


}
