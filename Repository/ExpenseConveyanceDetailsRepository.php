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
class ExpenseConveyanceDetailsRepository extends EntityRepository
{

    public function deleteAllConveyanceDetailByExpense(Expense $expense)
    {
        $query = $this->createQueryBuilder('e')
            ->delete()
            ->andWhere('e.expense =:expense')
            ->setParameter('expense', $expense)
            ->getQuery();

        return $query->execute();
    }

    public function getTotalAmountConveyanceDetailsByExpense(User $user, $yearMonth=null, $batch=null){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.transportType', 'SUM(e.totalAmount) as totalAmount', 'e.totalMileage');
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
        $qb->addSelect('SUM(e.mobilBill) as totalMobileBill','SUM(e.maintenanceBill) as totalMaintenanceBill','SUM(e.tollBill) as totalTollBill','SUM(e.servicingBill) as totalServicingBill','SUM(e.fuelBill) as totalFuelBill', 'SUM(e.parkingBill) as totalParkingBill', 'SUM(e.othersBill) as totalOthersBill', 'SUM(e.amount) as amount');
        $qb->addSelect('expense.id as expenseId');
        $qb->addSelect("DATE_FORMAT(expense.expenseDate,'%Y-%b') as expenseWordMonthYear", "DATE_FORMAT(expense.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(expense.expenseDate) as expenseYear', 'MONTH(expense.expenseDate) as expenseMonth');
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
        $qb->select('e.id', 'e.transportType', 'e.meterReadingFrom', 'e.meterReadingTo', 'e.totalMileage', 'e.cumulativeTotalMileageOneHundred', 'e.cumulativeTotalMileageTwoHundred');
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

    public function getConveyanceDetailsByExpense(Expense $entity)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.transportType', 'e.totalAmount', 'e.totalMileage', 'e.cumulativeTotalMileageOneHundred', 'e.cumulativeTotalMileageTwoHundred');
        $qb->addSelect('e.amount','e.maintenanceBill', 'e.mobilBill', 'e.servicingBill', 'e.fuelBill', 'e.parkingBill', 'e.othersBill', 'e.tollBill');
        $qb->addSelect('expense.id as expenseId');
        $qb->join('e.expense', 'expense');
        $qb->where('expense.id =:expenseId')->setParameter('expenseId', $entity->getId());
        $results = $qb->getQuery()->getArrayResult();

        $returnArray = [];
        if($results){
            foreach ($results as $result) {
                $returnArray[$result['transportType']][] = $result;
            }
        }

        return $returnArray;

    }


}
