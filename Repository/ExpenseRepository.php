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

    public function getExpensesByLineManager(User $loggedUser, $employeeId=null, $yearMonth=null){
        $qb = $this->createQueryBuilder('e');
//        $monthYear = date('Y-m', strtotime(date('Y-m')." -1 month"));
        $qb = $this->createQueryBuilder('e');
//        $qb->select('SUM(e.conveyance) as totalConveyance','SUM(e.mobile) as totalMobile','SUM(e.dailyAllowance) as totalDailyAllowance','SUM(e.hotelRent) as totalHotelRent','SUM(e.tollBill) as totalTollBill','SUM(e.food) as totalFood','SUM(e.courier) as totalCourier','SUM(e.maintenace) as totalMaintenace','SUM(e.serviceCharge) as totalServiceCharge','SUM(e.photostate) as totalPhotostate','SUM(e.others) as totalOthers');
        $qb->select( "e.expenseDate", "DATE_FORMAT(e.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(e.expenseDate) as expenseYear');
        $qb->addSelect('employee.id as employeeAutoId','employee.userId as employeeId','employee.name as employeeName');
        $qb->join('e.employee','employee');

        $qb->where('e.status >=:status')->setParameter('status',1);
        $qb->andWhere('e.expenseBatch IS NULL');
        $qb->andWhere('e.expenseDate IS NOT NULL');
        if($yearMonth){
            $qb->andWhere("DATE_FORMAT(e.expenseDate,'%Y-%m') =:monthYear")->setParameter('monthYear', $yearMonth);
        }
//        $qb->andWhere("DATE_FORMAT(e.expenseDate,'%Y-%m') =:monthYear")->setParameter('monthYear', $monthYear);

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
        if($employeeId){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        $qb->groupBy('expenseMonthYear');
//        $qb->addGroupBy('expenseYear');
        $qb->addGroupBy('employee.id');

        $result= $qb->getQuery()->getResult();
        $returnArray = [];
        if($result){
            foreach ($result as $key => $value) {
                $returnArray[$value['expenseMonthYear']][$value['employeeAutoId']] = $value;
            }
        }
        
        return $returnArray;
    }

    public function getExpensesByEmployeeAndYear(User $user, $year){
        $qb = $this->createQueryBuilder('e');
//        $qb->select('SUM(e.conveyance) as totalConveyance','SUM(e.mobile) as totalMobile','SUM(e.dailyAllowance) as totalDailyAllowance','SUM(e.hotelRent) as totalHotelRent','SUM(e.tollBill) as totalTollBill','SUM(e.food) as totalFood','SUM(e.courier) as totalCourier','SUM(e.maintenace) as totalMaintenace','SUM(e.serviceCharge) as totalServiceCharge','SUM(e.photostate) as totalPhotostate','SUM(e.others) as totalOthers');
        $qb->select("DATE_FORMAT(e.expenseDate,'%b,%y') as expenseWordMonthYear", "DATE_FORMAT(e.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(e.expenseDate) as expenseYear');
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

    /*function by raju*/
    public function getExpenseByEmployeeAndDateForUpdate($employeeId, $visitingDate)
    {
        return $this->createQueryBuilder('e')
            ->where('e.status >=:status')->setParameter('status',1)
            ->andWhere('e.employee = :employeeId')
            ->andWhere('e.expenseDate = :visitingDate')
            ->setParameter('employeeId', $employeeId)
            ->setParameter('visitingDate', new \DateTimeImmutable($visitingDate))
            ->getQuery()
            ->getOneOrNullResult();
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


    public function getExpensesByCompanyMonthYear($companyId, $yearMonth=null, $employeeIds=null){
        $qb = $this->createQueryBuilder('e');
        $qb->select( "e.expenseDate", "DATE_FORMAT(e.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(e.expenseDate) as expenseYear');
        $qb->addSelect('employee.id as employeeAutoId','employee.userId as employeeId','employee.name as employeeName', 'employee.bankBranch', 'employee.accountNumber');
        $qb->addSelect('bank.name as bankName');
        $qb->addSelect('company.companyName as companyName');
        $qb->addSelect('expenseChart.typeOfVehicle as typeOfVehicle');
        $qb->join('e.expenseBatch', 'expenseBatch');
        $qb->join('e.employee','employee');
        $qb->join('employee.company', 'company');
        $qb->leftJoin('employee.bank', 'bank');
        $qb->leftJoin('employee.expenseChart', 'expenseChart');

        $qb->where('expenseBatch.status >=:status')->setParameter('status',2);
        $qb->andWhere('e.expenseDate IS NOT NULL');
        $qb->andWhere("DATE_FORMAT(e.expenseDate,'%Y-%m') =:monthYear")->setParameter('monthYear', $yearMonth);
        $qb->andWhere("company.id =:companyId")->setParameter('companyId', $companyId);

        if($employeeIds){
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);
        }

        $qb->groupBy('expenseMonthYear');
//        $qb->addGroupBy('expenseYear');
        $qb->addGroupBy('employee.id');

        $result= $qb->getQuery()->getResult();
        $returnArray = [];
        if($result){
            foreach ($result as $key => $value) {
                $returnArray[$value['employeeAutoId']] = $value;
            }
        }

        return $returnArray;
    }

    public function getTotalDaysExpenseMonthWise($startDate, $endDate, $employeeIds=null)
    {

        $qb = $this->createQueryBuilder('e');
        $qb->select( "e.expenseDate", "DATE_FORMAT(e.expenseDate,'%Y-%m') as expenseMonthYear", 'YEAR(e.expenseDate) as expenseYear');
        $qb->addSelect('COUNT(e.id) as totalDays');
        $qb->addSelect('employee.id as employeeAutoId','employee.userId as employeeId','employee.name as employeeName');
        $qb->addSelect('workingArea.id as areaId','workingArea.name as areaName');

        $qb->join('e.employee','employee');
        $qb->join('e.workingArea','workingArea');
        $qb->where('e.status >= :status')->setParameter('status',1);
        $qb->andWhere('e.expenseDate IS NOT NULL');
        $qb->andWhere('e.expenseDate >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.expenseDate <= :endDate')->setParameter('endDate', $endDate);
        $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);

        $qb->groupBy('expenseMonthYear');
        $qb->addGroupBy('employee.id');
        $qb->addGroupBy('workingArea.id');

        $results= $qb->getQuery()->getArrayResult();
        $returnArray=[];
        if($results){
            $returnArray = array_reduce($results, function($carry, $result) {
                $carry[$result['employeeAutoId']][$result['expenseMonthYear']][$result['areaId']] = $result;
                return $carry;
            }, []);
        }

        return $returnArray;

    }

    public function getExpenseStatusByEmployeeAndDate($employee, $expenseDate=null, User $loggedUser){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.expenseDate', 'e.status' );
        $qb->addSelect('employee.id as employeeId', 'employee.name as employeeName', 'employee.userId as employeeUserId' );
        $qb->join('e.employee','employee' );
        
        $qb->andWhere("DATE_FORMAT(e.expenseDate,'%Y-%m') =:yearMonth")->setParameter('yearMonth', date('Y-m', strtotime($expenseDate)));
        $qb->andWhere('e.expenseDate IS NOT NULL');


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

        $qb->orderBy('e.expenseDate', 'ASC');

        $results= $qb->getQuery()->getArrayResult();
//        dd($results);
        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $visitDate= $result['expenseDate']->format('Y-m-d');
                $returnArray[$result['employeeId']][$visitDate]=$result;
            }
        }
        return $returnArray;
    }

    //getExpensesWaitingForApprovalByEmployeeId
    public function getExpensesWaitingForApprovalByEmployeeId($employeeId, $status = 1)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.status', 'e.comments', 'e.detailsComments', 'e.scheduleVisit', 'e.visitLocation', 'e.asPerAttachment', 'e.isAreaChange', 'e.isApproved');
        $qb->addSelect("DATE_FORMAT(e.expenseDate, '%Y-%m-%d') as expenseDate");
        $qb->addSelect("DATE_FORMAT(e.approvedAt, '%Y-%m-%d') as approvedAt");
        $qb->addSelect('employee.id as employeeId', 'employee.name as employeeName', 'employee.userId as employeeUserId');
        $qb->addSelect('workingArea.id as workingAreaId', 'workingArea.name as workingAreaName', 'workingArea.slug as workingAreaSlug');
        $qb->addSelect('expenseChart.typeOfVehicle as typeOfVehicle');
        $qb->join('e.employee', 'employee');
        $qb->join ( 'employee.expenseChart' , 'expenseChart' );
        $qb->leftJoin('e.workingArea', 'workingArea');

        $qb->where('e.expenseDate IS NOT NULL');
        $qb->andWhere( 'e.approvedAt IS NULL');
        $qb->andWhere('e.isApproved = :isApproved')->setParameter('isApproved', 0);
        $qb->andWhere('e.status = :status')->setParameter('status', $status);
        $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
//  get single result latest
        $qb->orderBy('e.expenseDate', 'DESC');
        $qb->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result) {
            $expenseParticulars = $this->_em->getRepository(ExpenseParticular::class)->findBy(['expense' => $result['id']]);
            $result['particulars'] = [];
            foreach ($expenseParticulars as $particular) {
                $result['particulars'][] = [
                    'id' => $particular->getId(),
                    'particular' => $particular->getParticular() ? $particular->getParticular()->getName() : '',
                    'amount' => (string)$particular->getAmount(),
                ];
            }

            // Get conveyance details
            $expenseConveyanceDetails = $this->_em->getRepository(ExpenseConveyanceDetails::class)->findBy(['expense' => $result['id']]);
            $result['conveyanceDetails'] = [];
            /** @var ExpenseConveyanceDetails $conveyanceDetail*/
            foreach ($expenseConveyanceDetails as $conveyanceDetail) {

                $employeeTransportType = $result['typeOfVehicle'];

                if ($employeeTransportType == 'car' && $conveyanceDetail->getTransportType() == 'motorcycle') {
                    continue; // Skip motorcycle details for car employees
                } elseif ($employeeTransportType == 'motorcycle' && $conveyanceDetail->getTransportType() == 'car') {
                    continue; // Skip car details for motorcycle employees
                }

                $result['conveyanceDetails'][] = [
                    'id' => $conveyanceDetail->getId(),
                    'destination' => $conveyanceDetail->getDestination() ? $conveyanceDetail->getDestination() : [],
                    'meterReadingFrom' => $conveyanceDetail->getMeterReadingFrom(),
                    'meterReadingTo' => $conveyanceDetail->getMeterReadingTo(),
                    'mileage' => $conveyanceDetail->getTotalMileage(),
                    'amount' => $conveyanceDetail->getAmount(),
                    'transportType' => $conveyanceDetail->getTransportType(),
                    'fuelBill' => $conveyanceDetail->getFuelBill(),
                    'parkingBill' => $conveyanceDetail->getParkingBill(),
                    'tollBill' => $conveyanceDetail->getTollBill(),
                    'othersBill' => $conveyanceDetail->getOthersBill(),
                    'mobilBill' => $conveyanceDetail->getMobilBill(),
                    'maintenanceBill' => $conveyanceDetail->getMaintenanceBill(),
                    'servicingBill' => $conveyanceDetail->getServicingBill(),
                    'meterReadingFrom' => $conveyanceDetail->getMeterReadingFrom(),
                    'meterReadingTo' => $conveyanceDetail->getMeterReadingTo(),
                    'totalMileage' => $conveyanceDetail->getTotalMileage(),
                    'details' => $conveyanceDetail->getDetails(),
                ];
            }
        }


        return $result;
    }



}
