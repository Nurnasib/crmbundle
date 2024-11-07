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
class PoultryMeatEggPriceRepository extends EntityRepository
{
    public function processPrice($regions, $breedTypes, $employee)
    {

        foreach ($breedTypes as $type){
            $breedTypeId = $type->getId();

            foreach ($regions as $region ){
                $regionId = $region->getId();
                $reportingDate = (new \DateTime("now"))->format('Y-m-d');
                $exist = $this->checkExistRecord($regionId, $breedTypeId, $employee->getId(), $reportingDate);
                if(!$exist){
                    $sql = "INSERT INTO `crm_poultry_meat_egg_price`(`region_id`, `status`, `created_at`, `breed_type_id`, `price`, `employee_id`, `reporting_date`) VALUES (:regionId , :status, :createdAt, :breedTypeId , :price,  :employeeId, :reportingDate)";

                    $qb = $this->_em->getConnection()->prepare($sql);
                    $qb->bindValue('createdAt', (new \DateTime("now"))->format('Y-m-d H:s:i'));
                    $qb->bindValue('reportingDate', (new \DateTime("now"))->format('Y-m-d'));
                    $qb->bindValue('status', 1);
                    $qb->bindValue('price', 0);
                    $qb->bindValue('regionId', $regionId);
                    $qb->bindValue('breedTypeId', $breedTypeId);
                    $qb->bindValue('employeeId', $employee->getId());
                    $qb->execute();
                }
            }
        }


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.region','region');
        $qb->join('e.breedType','breedType');
        $qb->select('e.price','e.id AS recordId');
        $qb->addSelect('region.id AS regionId');
        $qb->addSelect('breedType.id AS breedTypeId');

        $qb->where('e.employee = :employee')->setParameter('employee', $employee);
        $qb->andWhere('e.reportingDate = :reportingDate')->setParameter('reportingDate', (new \DateTime("now"))->format('Y-m-d'));

        $results = $qb->getQuery()->getArrayResult();
        $array = [];
        if($results){
            foreach ($results as $result):
                $key = "{$result['regionId']}-{$result['breedTypeId']}";
                $array[$key] = $result;
            endforeach;
        }
        return $array;
    }

    public function checkExistRecord($regionId, $breedTypeId, $employee, $reportingDate)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.region','region');
        $qb->join('e.breedType','breedType');
        $qb->join('e.employee','employee');
        $qb->select('e.id AS recordId');
        $qb->where('employee.id = :employee')->setParameter('employee', $employee);
        $qb->andWhere('region.id = :regionId')->setParameter('regionId', $regionId);
        $qb->andWhere('breedType.id = :breedTypeId')->setParameter('breedTypeId', $breedTypeId);
        $qb->andWhere('e.reportingDate = :reportingDate')->setParameter('reportingDate', $reportingDate);


        return $qb->getQuery()->getResult();
    }

    public function getMeatEggPriceReport($filterBy, User $loggedUser)
    {
        $start = isset($filterBy['startMonth']) ? (new \DateTime($filterBy['startMonth']))->format('Y-m-d') : date('Y-01-01');
        $end = isset($filterBy['endMonth']) ? (new \DateTime($filterBy['endMonth']))->format('Y-m-d') : date('Y-12-31');
        $employeeId = isset($filterBy['employeeId']) ? $filterBy['employeeId'] : null;
        $meatEggBreedType = isset($filterBy['meatEggBreedType']) ? $filterBy['meatEggBreedType']->getId() : null;
        $region = isset($filterBy['region']) && $filterBy['region']!='' ? $filterBy['region'] : '';

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.breedType', 'breed_type');
        $qb->join('e.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->join('e.region', 'region');
        $qb->join('employee.userGroup', 'user_group');


        $qb->select('AVG(e.price) AS avgPrice', 'MONTH(e.reportingDate) AS month', 'YEAR(e.reportingDate) AS year', 'e.reportingDate');
        $qb->addSelect('breed_type.id AS breedTypeId', 'breed_type.name AS breedTypeName');
        $qb->addSelect('employee.id as employeeAutoId', 'employee.userId', 'employee.name as employeeName');
        $qb->addSelect('region.id as regionId', 'group_concat(DISTINCT region.name) as regionName');
        $qb->addSelect('designation.name as designationName');

        $qb->where('e.reportingDate >= :start')->setParameter('start', $start);
        $qb->andWhere('e.reportingDate <= :end')->setParameter('end', $end);
        $qb->andWhere('user_group.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');
        $qb->andWhere('e.price > :price')->setParameter('price', 0);

        $qb->groupBy('month');
        $qb->addGroupBy('year');
        $qb->addGroupBy('breedTypeId');
        $qb->addGroupBy('employee.id');

        $qb->orderBy('employee.name', 'ASC');
        $qb->addOrderBy("DATE_FORMAT(e.reportingDate,'%Y-%m')", "ASC");


        $rolesString = implode('_', $loggedUser->getRoles());
        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }elseif (!str_contains($rolesString, 'ADMIN') && in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){

            $employeeIdsByLineManager = $this->_em->getRepository(User::class)->getEmployeesByLineManager($loggedUser);
            $employeeIs=[];
            if($employeeIdsByLineManager){
                $employeeIs=$employeeIdsByLineManager;
            }
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIs);
        }
        if (isset($filterBy['employeeId']) && $filterBy['employeeId'] !=''){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }
        if($meatEggBreedType){
            $qb->andWhere('breed_type.id = :breedTypeId')->setParameter('breedTypeId', $meatEggBreedType);
        }

        if($region){
            $qb->andWhere('region.id = :regionId')->setParameter('regionId', $region);
        }


        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result) {
            $reportingDate = $result['reportingDate']->format('F-Y');
            $data['records'][$result['breedTypeName']][$result['employeeAutoId']][$reportingDate] = $result['avgPrice'];
            $data['employeeInfo'][$result['breedTypeName']][$result['employeeAutoId']] = ['employeeId'=>$result['userId'], 'employeeName'=>$result['employeeName'], 'designationName'=>$result['designationName'], 'regionName'=>$result['regionName']];
        }
        $data['monthRange']=$this->getMonthBetweenDates($start, $end);
        return $data;

    }

    public function getDailyMeatEggPrice($filterBy, User $loggedUser)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : date('Y-m-d');
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : date('Y-m-d');
        $employeeId = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;
        $meatEggBreedType = isset($filterBy['meatEggBreedType']) ? $filterBy['meatEggBreedType']->getId() : null;
        $region = isset($filterBy['region']) && $filterBy['region']!='' ? $filterBy['region'] : '';

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.breedType', 'breed_type');
        $qb->join('e.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->join('e.region', 'region');
        $qb->join('employee.userGroup', 'user_group');


        $qb->select('e.price', 'MONTH(e.reportingDate) AS month', 'YEAR(e.reportingDate) AS year', 'e.reportingDate');
        $qb->addSelect('breed_type.id AS breedTypeId', 'breed_type.name AS breedTypeName');
        $qb->addSelect('employee.id as employeeAutoId','employee.userId', 'employee.name as employeeName');
        $qb->addSelect('region.id as regionId', 'region.name as regionName');
        $qb->addSelect('designation.name as designationName');

        $qb->where('e.reportingDate >= :start')->setParameter('start', $start);
        $qb->andWhere('e.reportingDate <= :end')->setParameter('end', $end);
        $qb->andWhere('user_group.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');
        $qb->andWhere('e.price > :price')->setParameter('price', 0);

        $rolesString = implode('_', $loggedUser->getRoles());
        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }elseif (!str_contains($rolesString, 'ADMIN') && in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){

            $employeeIdsByLineManager = $this->_em->getRepository(User::class)->getEmployeesByLineManager($loggedUser);
            $employeeIs=[];
            if($employeeIdsByLineManager){
                $employeeIs=$employeeIdsByLineManager;
            }
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIs);
        }
        if (isset($filterBy['employeeId']) && $filterBy['employeeId'] !=''){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        if($meatEggBreedType){
            $qb->andWhere('breed_type.id = :breedTypeId')->setParameter('breedTypeId', $meatEggBreedType);
        }
        if($region){
            $qb->andWhere('region.id = :regionId')->setParameter('regionId', $region);
        }

        $qb->orderBy('region.name', 'ASC');

        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result) {
            $reportingDate = $result['reportingDate']->format('d-m-Y');
            $data['records'][$result['breedTypeName']][$result['regionId']][$result['employeeAutoId']][$reportingDate] = $result['price'];
            $data['employeeInfo'][$result['breedTypeName']][$result['regionId']][$result['employeeAutoId']] = ['employeeId'=>$result['userId'], 'employeeName'=>$result['employeeName'], 'designationName'=>$result['designationName']];
            $data['regionInfo'][$result['breedTypeName']][$result['regionId']] = ['regionName'=>$result['regionName']];

        }
        $data['dateRange']=$this->getBetweenDates($start, $end);
//        dd($data);
        return $data;

    }


    private function getBetweenDates($startDate, $endDate)
    {
        $rangArray = [];

        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        for ($currentDate = $startDate; $currentDate <= $endDate;
             $currentDate += (86400)) {

            $date = date('d-m-Y', $currentDate);
            $rangArray[] = $date;
        }

        return $rangArray;
    }

    private function getMonthBetweenDates($startDate, $endDate)
    {
        $rangArray = [];
        
        $startDate= date('Y-m-d',strtotime($startDate));
        $endDate= date('Y-m-t', strtotime($endDate));

        $start    = (new \DateTime($startDate))->modify('first day of this month');
        $end      = (new \DateTime($endDate))->modify('last day of this month');
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);

        foreach ($period as $dt) {
            $date= $dt->format("F-Y");
            $rangArray[] = $date;
        }

        return $rangArray;
    }



    public function getMeatEggPriceEmployeeAndDateRangeWiseReport($filterBy)
    {
        $year = isset( $filterBy['year']) &&  $filterBy['year']!='' ?  $filterBy['year'] : date('Y');

        $startMonth = date('Y-m-d', strtotime($year . '-' . $filterBy['startMonth'] . '-01'));
        $endMonth = date('Y-m-t', strtotime($year . '-' . $filterBy['endMonth'] . '-01'));

        $start = isset($filterBy['startMonth']) ? (new \DateTime($startMonth))->format('Y-m-d') : date('Y-m-d');
        $end = isset($filterBy['endMonth']) ? (new \DateTime($endMonth))->format('Y-m-d') : date('Y-m-t');

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.breedType', 'breed_type');
        $qb->join('e.employee', 'employee');
//        $qb->leftJoin('employee.designation', 'designation');
        $qb->join('e.region', 'region');
//        $qb->join('employee.userGroup', 'user_group');


        $qb->select('AVG(e.price) AS avgPrice', 'MONTH(e.reportingDate) AS month', 'YEAR(e.reportingDate) AS year', 'e.reportingDate');
        $qb->addSelect('breed_type.id AS breedTypeId', 'breed_type.name AS breedTypeName');
        $qb->addSelect('employee.id as employeeAutoId', 'employee.userId', 'employee.name as employeeName');
        $qb->addSelect('region.id as regionId', 'group_concat(DISTINCT region.name) as regionName');
//        $qb->addSelect('designation.name as designationName');

        $qb->where('e.reportingDate >= :start')->setParameter('start', $start);
        $qb->andWhere('e.reportingDate <= :end')->setParameter('end', $end);
//        $qb->andWhere('user_group.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');
        $qb->andWhere('e.price > :price')->setParameter('price', 0);

        $qb->groupBy('month');
        $qb->addGroupBy('year');
        $qb->addGroupBy('breedTypeId');

        $qb->orderBy("DATE_FORMAT(e.reportingDate,'%Y-%m')", "ASC");

        $qb->andWhere('employee = :employee')->setParameter('employee', $filterBy['employee']);

        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result) {
            $reportingDate = $result['reportingDate']->format('F');
            $data['records'][$result['breedTypeName']][$reportingDate] = $result['avgPrice'];
        }
        return $data;

    }

    public function getMeatEggPriceByEmployeeIdsAndDateRangeWiseReport($startDate, $endDate, $employeeIds)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.breedType', 'breed_type');
        $qb->join('e.employee', 'employee');
        $qb->select('COUNT(e.id) AS totalRecords', 'MONTH(e.reportingDate) AS month', 'YEAR(e.reportingDate) AS year', 'e.reportingDate' , "DATE_FORMAT(e.reportingDate,'%Y-%m') as reportingMonthYear");
        $qb->addSelect('breed_type.id AS breedTypeId', 'breed_type.name AS breedTypeName');
        $qb->addSelect('employee.id as employeeAutoId', 'employee.userId', 'employee.name as employeeName');

        $qb->where('e.reportingDate >= :start')->setParameter('start', $startDate);
        $qb->andWhere('e.reportingDate <= :end')->setParameter('end', $endDate);
//        $qb->andWhere('user_group.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');
        $qb->andWhere('e.price > :price')->setParameter('price', 0);

        $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);

        $qb->groupBy('reportingMonthYear');
        $qb->addGroupBy('breedTypeId');
        $qb->addGroupBy('employee.id');


        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result) {
            $reportingDate = $result['reportingDate']->format('Y-m');
            $data[$reportingDate][$result['employeeAutoId']][$result['breedTypeId']] = $result;
        }
        return $data;

    }

}