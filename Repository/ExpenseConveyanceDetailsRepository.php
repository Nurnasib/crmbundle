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
        $qb->select('e.id', 'e.transportType', 'CAST(SUM(e.totalAmount) as decimal(10,2)) as totalAmount');
        $qb->addSelect('CAST(SUM(e.mobilBill) as decimal(10,2)) as totalMobileBill','CAST(SUM(e.maintenanceBill) as decimal(10,2)) as totalMaintenanceBill','CAST(SUM(e.tollBill) as decimal(10,2)) as totalTollBill','CAST(SUM(e.servicingBill) as decimal(10,2)) as totalServicingBill','CAST(SUM(e.fuelBill) as decimal(10,2)) as totalFuelBill', 'CAST(SUM(e.parkingBill) as decimal(10,2)) as totalParkingBill', 'CAST(SUM(e.othersBill) as decimal(10,2)) as totalOthersBill', 'CAST(SUM(e.amount) as decimal(10,2)) as amount', 'CAST(SUM(e.totalMileage) as decimal(10,2)) as totalMileage');
        $qb->addSelect('expense.id as expenseId');
        $qb->addSelect("DATE_FORMAT(expense.expenseDate,'%b,%y') as expenseWordMonthYear", "DATE_FORMAT(expense.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(expense.expenseDate) as expenseYear', 'MONTH(expense.expenseDate) as expenseMonth');
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

    public function getLastMileageByEmployeeDate($employeeId, $expenseDate, $transportType = null)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.transportType', 'e.meterReadingFrom', 'e.meterReadingTo', 'e.totalMileage', 'e.cumulativeTotalMileageOneHundred', 'e.cumulativeTotalMileageTwoHundred');
        $qb->join('e.expense', 'expense');
        $qb->join('expense.employee', 'employee');
        $qb->where('expense.expenseDate IS NOT NULL');
        $qb->andWhere("DATE_FORMAT(expense.expenseDate,'%Y-%m-%d') < :expenseDate")->setParameter('expenseDate', $expenseDate);

        $qb->andWhere('e.totalMileage > :totalMileage')->setParameter('totalMileage', 0);

        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);

        // When a specific transport type is requested, restrict the running total to that
        // vehicle only (the motorcycle bonus must be based on motorcycle mileage alone).
        $transportTypes = $transportType ? [$transportType] : ['car','motorcycle'];
        $qb->andWhere('e.transportType IN (:transportType)')->setParameter('transportType', $transportTypes);

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


    /**
     * @param int|int[] $companyId one company, or the set of companies reported as one
     */
    public function getConveyanceDetailsTotalAmount($companyId, $yearMonth, $employeeIds=null){
        $qb = $this->createQueryBuilder('e');
        $qb->select('SUM(e.totalAmount) as totalAmount', 'SUM(e.totalMileage) as totalMileage');
        $qb->addSelect('employee.id as employeeAutoId');
        $qb->addSelect("DATE_FORMAT(expense.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(expense.expenseDate) as expenseYear', 'MONTH(expense.expenseDate) as expenseMonth');
        $qb->join('e.expense','expense');
        $qb->join('expense.expenseBatch','expenseBatch');
        $qb->join('expense.employee','employee');
        $qb->leftJoin('employee.company','company');
        $qb->where('expense.expenseDate IS NOT NULL');
        $qb->andWhere('expenseBatch.status >=:status')->setParameter('status',2);
        $qb->andWhere("DATE_FORMAT(expense.expenseDate,'%Y-%m') =:yearMonth")->setParameter('yearMonth', $yearMonth);
        // Left join: the merged group stands for the whole organisation, so employees
        // with no company assigned are reported with it. A single-company filter still
        // excludes them, exactly as before. orX() is used rather than a raw "a OR b"
        // string because Doctrine does not parenthesise string parts, which would let
        // the OR escape the surrounding ANDs.
        if (is_array($companyId)) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('company.id', ':companyId'),
                $qb->expr()->isNull('company.id')
            ))->setParameter('companyId', $companyId);
        } else {
            $qb->andWhere('company.id =:companyId')->setParameter('companyId', $companyId);
        }

        if($employeeIds){
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);
        }

        $qb->groupBy('employee.id');
        $qb->addGroupBy('expenseMonthYear');


        $results= $qb->getQuery()->getArrayResult();

        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $returnArray[$result['employeeAutoId']]=$result;
            }
        }

        return $returnArray;
    }




    public function getConveyanceDetailsByMonthEmployee($employeeId, $expenseDate)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select('e.id', 'e.transportType', 'group_concat(
    IF(e.totalMileage > 0, e.meterReadingFrom, NULL)) as meterReadingFromConcat', 'group_concat(
    IF(e.meterReadingTo > 0, e.meterReadingTo, NULL)) as meterReadingToConcat', 'CAST(SUM(e.totalAmount) as decimal(10,2)) as grandTotalAmount');
        $qb->addSelect('group_concat(e.details) as detailsConcat', 'group_concat(e.destination) AS destinationConcat', 'group_concat(e.totalAmount) as totalAmountConcat');
        $qb->addSelect('CAST(SUM(e.mobilBill) as decimal(10,2)) as totalMobileBill','CAST(SUM(e.maintenanceBill) as decimal(10,2)) as totalMaintenanceBill','CAST(SUM(e.tollBill) as decimal(10,2)) as totalTollBill','CAST(SUM(e.servicingBill) as decimal(10,2)) as totalServicingBill','CAST(SUM(e.fuelBill) as decimal(10,2)) as totalFuelBill', 'CAST(SUM(e.parkingBill) as decimal(10,2)) as totalParkingBill', 'CAST(SUM(e.othersBill) as decimal(10,2)) as totalOthersBill', 'CAST(SUM(e.amount) as decimal(10,2)) as amount', 'CAST(SUM(e.totalMileage) as decimal(10,2)) as totalMileage');

//        $qb->select('e.id', 'e.transportType', 'e.details', 'e.destination', 'e.totalAmount', 'e.totalMileage', 'e.cumulativeTotalMileageOneHundred', 'e.cumulativeTotalMileageTwoHundred');
//        $qb->addSelect('e.amount','e.maintenanceBill', 'e.mobilBill', 'e.servicingBill', 'e.fuelBill', 'e.parkingBill', 'e.othersBill', 'e.tollBill');
        $qb->addSelect('expense.id as expenseId', 'expense.expenseDate', 'expense.visitLocation');
        $qb->join('e.expense', 'expense');
        $qb->join('expense.employee', 'employee');
        $qb->join('expense.expenseBatch', 'expenseBatch');
        $qb->where('expense.expenseDate IS NOT NULL');
        $qb->andWhere('expenseBatch.status >=:status')->setParameter('status',2);
        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);
        $qb->andWhere("DATE_FORMAT(expense.expenseDate,'%Y-%m') =:expenseDate")->setParameter('expenseDate', $expenseDate);
//        $qb->andWhere('e.totalAmount > 0');
        $qb->groupBy('e.transportType');
        $qb->addGroupBy('expense.id');
        $results = $qb->getQuery()->getArrayResult();
//dd($results);
        $returnArray = [];
        if($results){
            foreach ($results as $result) {

                $jsonString = $result['destinationConcat'];
                $validJsonString = '[' . rtrim($jsonString, ', ') . ']';
                $dataArray = json_decode($validJsonString, true);

                $result['destinationConcat'] = $dataArray;

                $date = $result['expenseDate']->format('Y-m-d');

                if($result['transportType'] == 'motorcycle'){
                    if($result['meterReadingFromConcat']!=""){
                        $returnArray[$result['transportType']][$date] = $result;
                    }
                    if( $result['totalTollBill'] > 0 ){
                        $returnArray[$result['transportType']]['tollBill'][$date] = $result;
                    }
                }
                else{
                    if($result['grandTotalAmount'] > 0){
                        $returnArray[$result['transportType']][$date] = $result;
                    }
                }
//                $returnArray[$result['transportType']][$date] = $result;

            }
        }

        return $returnArray;

    }



    public function getTotalAmountMonthlyByDateRange($employeeIds, $startDate, $endDate){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.transportType', 'CAST(SUM(e.totalAmount) as decimal(10,2)) as totalAmount');
        $qb->addSelect('CAST(SUM(e.mobilBill) as decimal(10,2)) as totalMobileBill','CAST(SUM(e.maintenanceBill) as decimal(10,2)) as totalMaintenanceBill','CAST(SUM(e.tollBill) as decimal(10,2)) as totalTollBill','CAST(SUM(e.servicingBill) as decimal(10,2)) as totalServicingBill','CAST(SUM(e.fuelBill) as decimal(10,2)) as totalFuelBill', 'CAST(SUM(e.parkingBill) as decimal(10,2)) as totalParkingBill', 'CAST(SUM(e.othersBill) as decimal(10,2)) as totalOthersBill', 'CAST(SUM(e.amount) as decimal(10,2)) as amount', 'CAST(SUM(e.totalMileage) as decimal(10,2)) as totalMileage');
        $qb->addSelect('SUM(IF(e.transportType=\'motorcycle\', e.totalMileage, 0)) as totalMotorcycleMileage', 'SUM(IF(e.transportType=\'car\', e.totalMileage, 0)) as totalCarMileage');
        $qb->addSelect('expense.id as expenseId');
        $qb->addSelect("DATE_FORMAT(expense.expenseDate,'%b,%y') as expenseWordMonthYear", "DATE_FORMAT(expense.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(expense.expenseDate) as expenseYear', 'MONTH(expense.expenseDate) as expenseMonth');
        $qb->addSelect('employee.id as employeeAutoId');
        $qb->join('e.expense','expense');
        $qb->join('expense.employee','employee');

        $qb->where('expense.status >=:status')->setParameter('status',1);
        $qb->andWhere('expense.expenseDate IS NOT NULL');
        $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);

        $qb->andWhere('expense.expenseDate >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('expense.expenseDate <= :endDate')->setParameter('endDate', $endDate);

        $qb->groupBy('expenseMonthYear');
        $qb->addGroupBy('e.transportType');
        $qb->addGroupBy('employee.id');

        $results= $qb->getQuery()->getArrayResult();

        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $returnArray[$result['employeeAutoId']][$result['expenseMonthYear']][$result['transportType']]=$result;
            }
        }

        return $returnArray;
    }


}
