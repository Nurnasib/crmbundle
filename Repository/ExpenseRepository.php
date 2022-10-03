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
use Terminalbd\CrmBundle\Entity\DmsFile;
use Terminalbd\CrmBundle\Entity\Expense;
use function Doctrine\ORM\QueryBuilder;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ExpenseRepository extends EntityRepository
{

    public function getExpenseReport($filterBy, User $loggedUser)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d 00:00:00') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d 23:59:59') : null;
        $employeeId = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmVisit', 'crm_visit');
        $qb->join('e.visitingArea', 'visiting_area');
        $qb->leftJoin('e.purpose', 'purpose');
        $qb->leftJoin('e.vehicle', 'vehicle');
        $qb->join('crm_visit.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->leftJoin('employee.lineManager', 'lineManager');

        $qb->select('e AS details');
        $qb->addSelect('crm_visit.id AS visitId','crm_visit.created AS visitedDate', 'visiting_area.name AS visitingAreaName');
        $qb->addSelect('employee.userId', 'employee.name', 'designation.name AS designationName');
        $qb->addSelect('purpose.id AS purposeId','purpose.name AS purposeName');
        $qb->addSelect('vehicle.id AS vehicleId','vehicle.name AS vehicleName');

        $rolesString = implode('_', $loggedUser->getRoles());

        if ($employeeId){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }else{
            if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
                $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
            }elseif (in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
                if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
                    $qb->andWhere('lineManager.id = :lineManagerId')->setParameter('lineManagerId', $loggedUser->getId());
                }
            }
        }

        $qb->andWhere('crm_visit.created >= :start')->setParameter('start', $start);
        $qb->andWhere('crm_visit.created <= :end')->setParameter('end', $end);

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $key => $result) {

            $yearMonth = $result['visitedDate']->format('Y-m-F');

            $result['details']['visitId'] = $result['visitId'];
            $result['details']['visitedDate'] = $result['visitedDate'];
            $result['details']['visitingAreaName'] = $result['visitingAreaName'];
            $purpose[$result['visitId']][] = $result['purposeName'];
            $vehicle[$result['visitId']][] = $result['vehicleName'];

            $data[$yearMonth][$result['userId']]['details'][$result['visitId']] = $result['details'];
            $data[$yearMonth][$result['userId']]['details'][$result['visitId']]['purpose'] = array_unique(array_filter($purpose[$result['visitId']])); // remove all null element & make unique
            $data[$yearMonth][$result['userId']]['details'][$result['visitId']]['vehicle'] = array_unique(array_filter($vehicle[$result['visitId']])); // remove all null element & make unique

            $data[$yearMonth][$result['userId']]['employee'] = [
                'userId' => $result['userId'],
                'name' => $result['name'],
                'designation' => $result['designationName'],
            ];

            ksort($data);
        }

        return $data;
    }

    public function getExpenses(User $user){
        $qb = $this->createQueryBuilder('e');
//        $qb->select('SUM(e.conveyance) as totalConveyance','SUM(e.mobile) as totalMobile','SUM(e.dailyAllowance) as totalDailyAllowance','SUM(e.hotelRent) as totalHotelRent','SUM(e.tollBill) as totalTollBill','SUM(e.food) as totalFood','SUM(e.courier) as totalCourier','SUM(e.maintenace) as totalMaintenace','SUM(e.serviceCharge) as totalServiceCharge','SUM(e.photostate) as totalPhotostate','SUM(e.others) as totalOthers');
        $qb->select("DATE_FORMAT(e.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(e.expenseDate) as expenseYear');
        $qb->addSelect('employee.id as employeeAutoId','employee.userId as employeeId','employee.name as employeeName');
        $qb->join('e.employee','employee');

        $qb->where('e.status >=:status')->setParameter('status',1);
        $qb->andWhere('e.expenseDate IS NOT NULL');

        /*$rolesString = implode('_', $user->getRoles());
        if (!str_contains($rolesString, 'ADMIN')){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $user->getId());
        }*/
        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $user->getId());

        $qb->groupBy('expenseMonthYear');
//        $qb->addGroupBy('expenseYear');
        $qb->addGroupBy('employee.id');

        $result= $qb->getQuery()->getResult();

        return $result;
    }

    public function getExpensesByEmployeeAndYearMonth($employee , $yearMonth){
        if($employee && $yearMonth){
            $qb = $this->createQueryBuilder('e');
            $qb->join('e.employee','employee');
            $qb->where('e.status >=:status')->setParameter('status',1);
            $qb->andWhere('e.expenseDate IS NOT NULL');
            $qb->andWhere("DATE_FORMAT(e.expenseDate,'%Y-%m') =:yearMonth")->setParameter('yearMonth', $yearMonth);

            $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employee->getId());
            $qb->orderBy('e.expenseDate','ASC');

            $results= $qb->getQuery()->getResult();

            return $results;
        }
        return [];
    }
    
    public function getExpenseByEmployeeAndDate(Expense $entity, User $employee, $expenseDate){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id');
        $qb->join('e.employee','employee');

        $qb->where('e.status >=:status')->setParameter('status',1);
        $qb->andWhere('e.id !=:entityId')->setParameter('entityId', $entity->getId());
        $qb->andWhere('e.expenseDate IS NOT NULL');
        $qb->andWhere('e.expenseDate =:expenseDate')->setParameter('expenseDate',$expenseDate);
        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employee->getId());

        $results= $qb->getQuery()->getArrayResult();

        return $results;
    }



}
