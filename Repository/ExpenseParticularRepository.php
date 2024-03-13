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
use Terminalbd\CrmBundle\Entity\Expense;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ExpenseParticularRepository extends EntityRepository
{

    public function deleteAllParticularByExpense(Expense $expense)
    {
        $query = $this->createQueryBuilder('e')
            ->delete()
            ->andWhere('e.expense =:expense')
            ->setParameter('expense', $expense)
            ->getQuery();

        return $query->execute();
    }
    

    public function getExpenseParticularsByExpense(Expense $entity){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.amount', 'e.path', 'e.expenseChartDetailId');
        $qb->addSelect('expense.id as expenseId');
        $qb->addSelect('particular.id as particularId','particular.name as particularName');
        $qb->join('e.particular','particular');
        $qb->join('e.expense','expense');

        $qb->where('expense.id =:expense')->setParameter('expense',$entity->getId());

        $results= $qb->getQuery()->getArrayResult();

        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $returnArray[$result['expenseId']][$result['expenseChartDetailId']][$result['particularId']]=$result;
            }
        }

        return $returnArray;
    }



    public function getTotalAmountExpenseParticular(User $user){
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.amount) as totalAmount', 'e.expenseChartDetailId');
        $qb->addSelect("DATE_FORMAT(expense.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(expense.expenseDate) as expenseYear', 'MONTH(expense.expenseDate) as expenseMonth');
        $qb->addSelect('employee.id as employeeAutoId');
        $qb->addSelect('particular.id as particularId');
        $qb->join('e.expense','expense');
        $qb->join('e.particular','particular');
        $qb->join('expense.employee','employee');

        $qb->where('expense.status >=:status')->setParameter('status',1);
        $qb->andWhere('e.expenseChartDetailId IS NOT NULL');
        $qb->andWhere('expense.expenseDate IS NOT NULL');

        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $user->getId());

        $qb->groupBy('expenseMonthYear');

        $qb->addGroupBy('employee.id');
        $qb->addGroupBy('particular.id');
        $qb->addGroupBy('e.expenseChartDetailId');

        $results= $qb->getQuery()->getArrayResult();
        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $returnArray[$result['employeeAutoId']][$result['expenseMonthYear']][$result['expenseChartDetailId']][$result['particularId']]=$result;
            }
        }

        return $returnArray;
    }

    public function getTotalAmountExpenseParticularWithoutChartDetail(User $user, $year){
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.amount) as totalAmount', 'e.expenseChartDetailId');
        $qb->addSelect("DATE_FORMAT(expense.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(expense.expenseDate) as expenseYear', 'MONTH(expense.expenseDate) as expenseMonth');
        $qb->addSelect('employee.id as employeeAutoId');
        $qb->addSelect('particular.id as particularId', 'particular.name as particularName');
        $qb->join('e.expense','expense');
        $qb->join('e.particular','particular');
        $qb->join('expense.employee','employee');

        $qb->where('expense.status >=:status')->setParameter('status',1);
        $qb->andWhere('e.expenseChartDetailId IS NOT NULL');
        $qb->andWhere('expense.expenseDate IS NOT NULL');
        $qb->andWhere("DATE_FORMAT(expense.expenseDate,'%Y') =:year")->setParameter('year', $year);
        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $user->getId());

        $qb->groupBy('expenseMonthYear');

        $qb->addGroupBy('employee.id');
        $qb->addGroupBy('particular.id');

        $results= $qb->getQuery()->getArrayResult();
        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $returnArray[$result['employeeAutoId']][$result['expenseMonthYear']][$result['particularId']]=$result;
            }
        }

        return $returnArray;
    }


    public function getDailyExpenseParticularAmount(User $user, $yearMonth=null, $batch=null){
        $returnArray=[];
        if($user){

            $qb = $this->createQueryBuilder('e');
            $qb->select('e.id as expenseParticularId','e.amount', 'e.path', 'e.expenseChartDetailId');
            $qb->addSelect("DATE_FORMAT(expense.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(expense.expenseDate) as expenseYear');
            $qb->addSelect('employee.id as employeeAutoId');
            $qb->addSelect('particular.id as particularId', 'particular.name as particularName');
            $qb->addSelect('expense.id as expenseId');
            $qb->join('e.expense','expense');
            $qb->join('e.particular','particular');
            $qb->join('expense.employee','employee');

            $qb->where('expense.status >=:status')->setParameter('status',1);
            $qb->andWhere('expense.expenseDate IS NOT NULL');
            if($yearMonth && $yearMonth!=''){
                $qb->andWhere("DATE_FORMAT(expense.expenseDate,'%Y-%m') =:yearMonth")->setParameter('yearMonth', $yearMonth);
            }

            if($batch && $batch!=''){
                $qb->andWhere('expense.expenseBatch =:batch')->setParameter('batch',$batch);
            }

            $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $user->getId());

            $qb->orderBy('particular.sortOrder', 'ASC');

            $results= $qb->getQuery()->getArrayResult();

            if($results){
                foreach ($results as $result) {
                    $returnArray['inLine'][$result['employeeAutoId']][$result['expenseId']][$result['expenseChartDetailId']][$result['particularId']]=$result;
                    $returnArray['grandTolalPerColumn'][$result['expenseChartDetailId']][$result['particularId']][]=$result;
                    if(isset($result['amount']) && $result['amount']){
                        $returnArray['expenseParticularAttributes'][$result['particularId']]=['id'=>$result['particularId'], 'name'=>$result['particularName']];
                    }
                }
            }
        }

        return $returnArray;
    }

    public function getMonthlyExpenseParticularAmount(User $user, $yearMonth){
        $returnArray=[];
        if($user && $yearMonth){

            $qb = $this->createQueryBuilder('e');
            $qb->select('e.amount','e.path');
            $qb->addSelect("DATE_FORMAT(expenseBatch.expenseMonth,'%Y-%m') as expenseMonthYear", 'YEAR(expenseBatch.expenseMonth) as expenseYear');
            $qb->addSelect('employee.id as employeeAutoId');
            $qb->addSelect('particular.id as particularId');
            $qb->addSelect('expenseBatch.id as expenseBatchId');
            $qb->join('e.expenseBatch','expenseBatch');
            $qb->join('e.particular','particular');
            $qb->join('expenseBatch.employee','employee');

            $qb->where('expenseBatch.status >=:status')->setParameter('status',1);
            $qb->andWhere('expenseBatch.expenseMonth IS NOT NULL');

            $qb->andWhere("DATE_FORMAT(expenseBatch.expenseMonth,'%Y-%m') =:yearMonth")->setParameter('yearMonth', $yearMonth);

            $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $user->getId());

            $results= $qb->getQuery()->getArrayResult();

            if($results){
                foreach ($results as $result) {
                    $returnArray['inLine'][$result['employeeAutoId']][$result['particularId']]=$result;
                    $returnArray['grandTolalPerColumn'][]=$result;
                }
            }
        }

        return $returnArray;
    }


}