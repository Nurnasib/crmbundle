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
use Terminalbd\CrmBundle\Entity\ExpenseConveyanceDetails;
use Terminalbd\CrmBundle\Entity\ExpenseParticular;
use function Doctrine\ORM\QueryBuilder;
use function Symfony\Component\String\s;

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

    public function getExpensesByLineManager(User $loggedUser){
        $monthYear = date('Y-m', strtotime(date('Y-m')." -1 month"));
        $qb = $this->createQueryBuilder('e');
//        $qb->select('SUM(e.conveyance) as totalConveyance','SUM(e.mobile) as totalMobile','SUM(e.dailyAllowance) as totalDailyAllowance','SUM(e.hotelRent) as totalHotelRent','SUM(e.tollBill) as totalTollBill','SUM(e.food) as totalFood','SUM(e.courier) as totalCourier','SUM(e.maintenace) as totalMaintenace','SUM(e.serviceCharge) as totalServiceCharge','SUM(e.photostate) as totalPhotostate','SUM(e.others) as totalOthers');
        $qb->select("DATE_FORMAT(e.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(e.expenseDate) as expenseYear');
        $qb->addSelect('employee.id as employeeAutoId','employee.userId as employeeId','employee.name as employeeName');
        $qb->join('e.employee','employee');

        $qb->where('e.status >=:status')->setParameter('status',1);
        $qb->andWhere('e.expenseBatch IS NULL');
        $qb->andWhere('e.expenseDate IS NOT NULL');
        $qb->andWhere("DATE_FORMAT(e.expenseDate,'%Y-%m') =:monthYear")->setParameter('monthYear', $monthYear);

        $roleSplitArray = [];

        foreach ($loggedUser->getRoles() as $role) {
            $roleSplitArray = array_merge(explode('_', $role), $roleSplitArray);
        }

        if (in_array('ADMIN', $roleSplitArray)) {
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
            if($userRole){

                foreach ($userRole as $key => $role) {
                    if ($key !== 0) {
                        $query .= " OR ";
                    }
                    $query .= "employee.roles LIKE '%" . $role . "%'";

                }
                $qb->andWhere($query);
            }

        } elseif (!in_array('ADMIN', $roleSplitArray) && in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())) {
            $employeeIdsByLineManager = $this->_em->getRepository(User::class)->getEmployeesByLineManager($loggedUser);
            $employeeIs=[];
            if($employeeIdsByLineManager){
                $employeeIs=$employeeIdsByLineManager;
            }
//            dd($employeeIs);
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIs);
        }

        $qb->groupBy('expenseMonthYear');
//        $qb->addGroupBy('expenseYear');
        $qb->addGroupBy('employee.id');

        $result= $qb->getQuery()->getResult();

        return $result;
    }

    public function getExpensesByEmployeeAndYear(User $user, $year){
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
        $qb->andWhere("DATE_FORMAT(e.expenseDate,'%Y') =:year")->setParameter('year', $year);

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
    
    public function getExpenseByEmployeeAndDate($employeeId, $expenseDate){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id');
        $qb->join('e.employee','employee');

        $qb->where('e.status >=:status')->setParameter('status',1);
        $qb->andWhere('e.expenseDate IS NOT NULL');
        $qb->andWhere('e.expenseDate =:expenseDate')->setParameter('expenseDate',$expenseDate);
        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);

        $results= $qb->getQuery()->getArrayResult();

        return $results;
    }
    
    public function getLastExpenseByEmployee($employeeId){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.expenseDate', 'e.createdAt');
        $qb->join('e.employee','employee');

        $qb->andWhere('e.expenseDate IS NOT NULL');
        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);
        $qb->orderBy('e.expenseDate','DESC');
        $qb->setMaxResults(1);

        $results= $qb->getQuery()->getOneOrNullResult();

        return $results;
    }
    
    public function duplicateExpenseCheckByEmployeeAndDate(Expense $entity, User $employee, $expenseDate){
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


    public function getAllExpenseByEmployeeAndMonth($employeeId, $yearMonth){
        if($employeeId && $yearMonth){
            $qb = $this->createQueryBuilder('e');
            $qb->join('e.employee','employee');
            $qb->where('e.status >=:status')->setParameter('status',1);
            $qb->andWhere('e.expenseDate IS NOT NULL');
            $qb->andWhere("DATE_FORMAT(e.expenseDate,'%Y-%m') =:yearMonth")->setParameter('yearMonth', $yearMonth);

            $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);
            $qb->orderBy('e.expenseDate','DESC');

            $results= $qb->getQuery()->getResult();

            $returnArray = [];
            /* @var Expense $result*/
            foreach ($results as $key => $result) {

                $detailsArray = [];
                $expenseConveyanceDetails = $result->getExpenseConveyanceDetails();
                /* @var ExpenseConveyanceDetails $expenseConveyanceDetail*/
                foreach ($expenseConveyanceDetails as $expenseConveyanceDetail) {
                    if ($expenseConveyanceDetail->getTransportType() == 'car') {
                        $detailsArray['car'] = [
                            'id' => $expenseConveyanceDetail->getId(),
                            "fuel_bill" => (string)$expenseConveyanceDetail->getFuelBill(),
                            "parking_bill" => (string)$expenseConveyanceDetail->getParkingBill(),
                            "toll_bill" => (string)$expenseConveyanceDetail->getTollBill(),
                            "other_bill" => (string)$expenseConveyanceDetail->getOthersBill(),
                            "mobil_bill" => (string)$expenseConveyanceDetail->getMobilBill(),
                            "maintenance_bill" => (string)$expenseConveyanceDetail->getMaintenanceBill(),
                            "servicing_bill" => (string)$expenseConveyanceDetail->getServicingBill(),
                            "meter_reading_start" => (string)$expenseConveyanceDetail->getMeterReadingFrom(),
                            "meter_reading_end" => (string)$expenseConveyanceDetail->getMeterReadingTo(),
                            "total_reading" => (string)$expenseConveyanceDetail->getTotalMileage(),
                            "destination" => $expenseConveyanceDetail->getDestination()?$expenseConveyanceDetail->getDestination():[],
                        ];
                    }elseif ($expenseConveyanceDetail->getTransportType() == 'motorcycle') {
                        $detailsArray['motorcycle'] = [
                            'id' => $expenseConveyanceDetail->getId(),
                            "fuel_bill" => (string)$expenseConveyanceDetail->getFuelBill(),
                            "parking_bill" => (string)$expenseConveyanceDetail->getParkingBill(),
                            "toll_bill" => (string)$expenseConveyanceDetail->getTollBill(),
                            "other_bill" => (string)$expenseConveyanceDetail->getOthersBill(),
                            "mobil_bill" => (string)$expenseConveyanceDetail->getMobilBill(),
                            "maintenance_bill" => (string)$expenseConveyanceDetail->getMaintenanceBill(),
                            "servicing_bill" => (string)$expenseConveyanceDetail->getServicingBill(),
                            "meter_reading_start" => (string)$expenseConveyanceDetail->getMeterReadingFrom(),
                            "meter_reading_end" => (string)$expenseConveyanceDetail->getMeterReadingTo(),
                            "total_reading" => (string)$expenseConveyanceDetail->getTotalMileage(),
                            "destination" => $expenseConveyanceDetail->getDestination()?$expenseConveyanceDetail->getDestination():[],
                        ];
                    } elseif ($expenseConveyanceDetail->getTransportType() == 'office-car') {
                        $detailsArray['office-car'] = [
                            'id' => $expenseConveyanceDetail->getId(),
                            "fuel_bill" => (string)$expenseConveyanceDetail->getFuelBill(),
                            "parking_bill" => (string)$expenseConveyanceDetail->getParkingBill(),
                            "toll_bill" => (string)$expenseConveyanceDetail->getTollBill(),
                            "other_bill" => (string)$expenseConveyanceDetail->getOthersBill(),
                            "mobil_bill" => (string)$expenseConveyanceDetail->getMobilBill(),
                            "maintenance_bill" => (string)$expenseConveyanceDetail->getMaintenanceBill(),
                            "servicing_bill" => (string)$expenseConveyanceDetail->getServicingBill(),
                            "details" => $expenseConveyanceDetail->getDetails(),
                        ];
                    } elseif ($expenseConveyanceDetail->getTransportType() == 'local-conveyance' ){
                        $detailsArray['local-conveyance'][] = [
                            'id' => $expenseConveyanceDetail->getId(),
                            "details" => $expenseConveyanceDetail->getDetails(),
                            "amount" => (string) $expenseConveyanceDetail->getAmount(),
                            "destination" => $expenseConveyanceDetail->getDestination()?$expenseConveyanceDetail->getDestination():[],
                        ];
                    } elseif ($expenseConveyanceDetail->getTransportType() == 'others'){
                        $detailsArray['others'][] = [
                            'id' => $expenseConveyanceDetail->getId(),
                            "details" => $expenseConveyanceDetail->getDetails(),
                            "amount" => (string)$expenseConveyanceDetail->getAmount(),
                        ];
                    }
                }

                $particularArray = [];
                $particulars = $result->getExpenseParticulars();

                if($particulars){
                    /* @var ExpenseParticular $particular*/
                    foreach ($particulars as $particular) {
                        $particularArray[] = [
                            'id' => $particular->getExpenseChartDetailId() ? $particular->getExpenseChartDetailId() : '',
                            'expense_particular_id' => $particular->getId(),
                            'particular_id' => $particular->getParticular() ? $particular->getParticular()->getId():'',
                            'particular_name' => $particular->getParticular() ? $particular->getParticular()->getName():'',
                            'particular_slug' => $particular->getParticular() ? $particular->getParticular()->getSlug():'',
                            'particular_setting_type' => $particular->getParticular() ? $particular->getParticular()->getSettingType():'',
                            'particular_expense_payment_type' => $particular->getParticular() ? $particular->getParticular()->getExpensePaymentType(): '',
                            'payment_duration'=> 'DAILY',
                            'amount' => (string)$particular->getAmount(),
                            'area_id' => $result->getWorkingArea() ? $result->getWorkingArea()->getId() : null,
                            'area_name' => $result->getWorkingArea() ? $result->getWorkingArea()->getName() : null,
                            'area_slug' => $result->getWorkingArea() ? $result->getWorkingArea()->getSlug() : null,
                        ];
                    }
                }

                $returnArray[] = [
                    "id" => $result->getId(),
                    "employee_id"=> $result->getEmployee()->getId(),
                    "visit_date" => $result->getExpenseDate()->format('Y-m-d'),
                    "visiting_area" => $result->getVisitLocation(),
                    "comment" => $result->getComments(),
                    "area" => [
                        'id' => $result->getWorkingArea()->getId(),
                        'name' => $result->getWorkingArea()->getName(),
                        'setting_type' => $result->getWorkingArea()->getSettingType(),
                        'slug' => $result->getWorkingArea()->getSlug(),
                        'parent_id' => $result->getWorkingArea()->getParent() ? $result->getWorkingArea()->getParent()->getId(): null,
                        'parent_name' => $result->getWorkingArea()->getParent() ? $result->getWorkingArea()->getParent()->getName(): null,
                        'parent_setting_type' => $result->getWorkingArea()->getParent() ? $result->getWorkingArea()->getParent()->getSettingType(): null,
                        'parent_slug' => $result->getWorkingArea()->getParent() ? $result->getWorkingArea()->getParent()->getSlug():null,
                        'sort_order' => $result->getWorkingArea()->getSortOrder(),
                    ],
                    "change_area" => $result->getIsAreaChange(),
                    "as_per_attachment" => $result->isAsPerAttachment(),
                    "details_comments" => $result->getDetailsComments(),
                    "mode_of_transport" => $detailsArray,
                    'particulars' => $particularArray,

                ];
            }

            return $returnArray;
        }
        return [];
    }



}
