<?php

namespace Terminalbd\CrmBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Terminalbd\CrmBundle\Entity\ExpenseChart;

class ExpenseChartRepository extends EntityRepository
{

    public function getExpenseChartByDesignation($designationId)
    {
        $returnArray=[];

        if (!$designationId) {
            return $returnArray;
        }

        $qb = $this->createQueryBuilder('e');

        $qb->select('e.id', 'e.createdAt');
        $qb->addSelect('expenseChartDetail.id as expenseChartDetailId','expenseChartDetail.amount as amount','expenseChartDetail.amount_type as amountType');
        $qb->addSelect('particular.id as particularId','particular.name as particularName','particular.expensePaymentType as expensePaymentType');
        $qb->addSelect('designation.id as designationId','designation.name as designationName');
        $qb->join('e.designation','designation');
        $qb->join('e.expenseChartDetail','expenseChartDetail');
        $qb->join('expenseChartDetail.particular','particular');

        $qb->where('designation.id =:designation')->setParameter('designation',$designationId);

        $qb->orderBy('expenseChartDetail.id', 'DESC');

        $results= $qb->getQuery()->getArrayResult();
        if($results){
            foreach ($results as $result) {
                $returnArray[$result['designationId']][$result['particularId']]=$result;
            }
        }

        return $returnArray;
    }

    public function getExpenseChartByEmployee($employeeId)
    {
        $returnArray=[];

        if (!$employeeId) {
            return $returnArray;
        }

        $qb = $this->createQueryBuilder('e');

        $qb->select('e.id', 'e.createdAt');
        $qb->addSelect('expenseChartDetail.id as expenseChartDetailId','expenseChartDetail.amount as amount','expenseChartDetail.amount_type as amountType', 'expenseChartDetail.paymentDuration');
        $qb->addSelect('particular.id as particularId','particular.name as particularName','particular.expensePaymentType as expensePaymentType');
        $qb->addSelect('employee.id as employeeId','employee.name as employeeName');
        $qb->addSelect('area.id as areaId','area.name as areaName');
        $qb->join('e.employee','employee');
        $qb->join('e.expenseChartDetails','expenseChartDetail');
        $qb->join('expenseChartDetail.particular','particular');
        $qb->leftJoin('expenseChartDetail.area','area');

        $qb->where('employee.id =:employee')->setParameter('employee',$employeeId);

        $qb->orderBy('area.sortOrder', 'ASC');
        $qb->addOrderBy('expenseChartDetail.id', 'DESC');

        $results= $qb->getQuery()->getArrayResult();
        if($results){
            foreach ($results as $result) {
                $returnArray[] = $result;
            }
        }

        return $returnArray;
    }

    public function getExpenseChart()
    {
        $returnArray=[];
        $qb = $this->createQueryBuilder('e');

        $qb->select('e.id', 'e.createdAt');
        $qb->addSelect('expenseChartDetail.id as expenseChartDetailId','expenseChartDetail.amount as amount','expenseChartDetail.amount_type as amountType', 'expenseChartDetail.paymentDuration');
        $qb->addSelect('particular.id as particularId','particular.name as particularName','particular.expensePaymentType as expensePaymentType');
        $qb->addSelect('area.id as areaId','area.name as areaName');
        $qb->join('e.expenseChartDetails','expenseChartDetail');
        $qb->join('expenseChartDetail.particular','particular');
        $qb->join('expenseChartDetail.area','area');

        $qb->orderBy('area.sortOrder', 'ASC');
        $qb->addOrderBy('expenseChartDetail.id', 'DESC');
        $qb->groupBy('expenseChartDetail.paymentDuration');
        $qb->addGroupBy('particular.id');
        $qb->addGroupBy('area.id');


        $results= $qb->getQuery()->getArrayResult();
        if($results){
            foreach ($results as $result) {
                $returnArray[] = $result;
            }
        }

        return $returnArray;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ExpenseChart $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(ExpenseChart $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return ExpenseChart[] Returns an array of ExpenseChart objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExpenseChart
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
