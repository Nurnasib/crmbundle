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
use Terminalbd\CrmBundle\Entity\CompanyWiseFeedSale;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class CompanyWiseFeedSaleRepository extends BaseRepository
{
    public function getExitingCheckCompanyWiseFeedSaleByMonthYearEmployeeAndCompany($year, $month, $feedCompany, $employee, $breed_name)
    {
        if($year&&$month&&$feedCompany&&$employee&&$breed_name){
            $query = $this->createQueryBuilder('cwfs')
                ->select('cwfs.id')
                ->where('cwfs.year = :year')
                ->andWhere('cwfs.monthName = :month')
                ->andWhere('cwfs.employee = :employee')
                ->andWhere('cwfs.feedCompany = :feedCompany')
                ->andWhere('cwfs.breedName = :breed_name')
                ->setParameters(array('year'=>$year, 'month'=>$month, 'feedCompany'=>$feedCompany, 'employee'=>$employee, 'breed_name'=>$breed_name));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getCompanyWiseFeedSaleByCreatedDateAndEmployee($year, $month, $employee, $breed_name)
    {
        if($year&&$employee&&$breed_name){
            $query = $this->createQueryBuilder('cwfs')
                ->where('cwfs.year IN (:year)')
                ->andWhere('cwfs.monthName IN (:month)')
                ->andWhere('cwfs.employee = :employee')
                ->andWhere('cwfs.breedName = :breed_name')
                ->setParameters(array('year'=>$year, 'month'=>$month, 'employee'=>$employee, 'breed_name'=>$breed_name));

            $resutls = $query->getQuery()->getResult();
            $returnArray=[];
            if($resutls){
                /* @var CompanyWiseFeedSale $value*/
                foreach ($resutls as $value){
                    $decodeValue = json_decode($value->getProductWiseQty(),true);
                    $arraySum = $decodeValue?array_sum($decodeValue):0;
                    $returnArray['items'][$value->getYear()][$value->getMonthName()][$value->getFeedCompany()->getId()]=$value;
                    if (isset($returnArray['grand_total'][$value->getYear()][$value->getMonthName()]))
                    {
                        $returnArray['grand_total'][$value->getYear()][$value->getMonthName()] += $arraySum;
                    }
                    else
                    {
                        $returnArray['grand_total'][$value->getYear()][$value->getMonthName()] = $arraySum;
                    }
                }
            }

//            dd($returnArray);

            return $returnArray;
        }
        return array();
    }


    public function getCompanyWiseFeedSaleReport($breedName, $filterBy, User $loggedUser)
    {

        $monthNum= isset($filterBy['month']) && $filterBy['month']!=''?$filterBy['month']:'';
        $monthName='';
        if($monthNum){
            $monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
        }

        $year = isset($filterBy['year']) && $filterBy['year']!=''?$filterBy['year']:date('Y');

//        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00' : null;
//        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59' : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.feedCompany', 'feedCompany');
        $qb->join('e.employee', 'employee');

        $qb->select('e.monthName', 'e.year', 'e.breedName', 'e.productWiseQty', 'e.totalQty', 'e.createdAt');
        $qb->addSelect('feedCompany.id AS feedCompanyId', 'feedCompany.name AS feedCompanyName');
        $qb->addSelect('employee.id AS employeeId','employee.userId','employee.name AS employeeName');

        $qb->where('e.totalQty >0');

        if($monthName && $monthName!=''){
            $qb->andWhere('e.monthName =:monthName')->setParameter('monthName', $monthName);
        }

        $qb->andWhere('e.year = :year')->setParameter('year', $year);

        $qb->andWhere('e.breedName = :breedName')->setParameter('breedName', $breedName);

        $employee = isset($filterBy['employeeId'])? $filterBy['employeeId']: '';
        if (!empty($employee)){
            $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee);
        }

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

        $results = $qb->getQuery()->getArrayResult();
        $data = [];

        foreach ($results as $result) {
            $data[$result['monthName'] . '-' . $result['year']][$result['employeeId']]['details'][] = $result;
            if (array_key_exists('companyTotalQty', $data[$result['monthName'] . '-' . $result['year']][$result['employeeId']])){
                $data[$result['monthName'] . '-' . $result['year']][$result['employeeId']]['companyTotalQty'] += $result['totalQty'];
            }else{
                $data[$result['monthName'] . '-' . $result['year']][$result['employeeId']]['companyTotalQty'] = $result['totalQty'];

            }
        }
        
        return $data;
    }
    
    public function getCompanyWiseFeedSaleDataForApiImport($employee, $year, $month){
        $query = $this->createQueryBuilder('cwfs')
            ->select('cwfs.id','cwfs.monthName', 'cwfs.year as yearName', 'cwfs.createdAt', 'cwfs.breedName', 'cwfs.productWiseQty', 'cwfs.totalQty')
            ->addSelect('employee.id as employeeId')
            ->addSelect('feedCompany.id as feedCompanyId')
            ->join('cwfs.employee','employee')
            ->join('cwfs.feedCompany','feedCompany')
            ->where('cwfs.year =:year')
            ->andWhere('cwfs.monthName =:month')
            ->andWhere('employee.id =:employeeId')
            ->setParameters(array('year'=>$year, 'month'=>$month, 'employeeId'=>$employee));

        $resutls = $query->getQuery()->getArrayResult();
        $returnArray=[];
        if($resutls){
            foreach ($resutls as $resutl) {
                $returnArray[]=[
                    "id"=> $resutl['id'],
                    "employee_id"=> $resutl['employeeId'],
                    "feed_company_id"=> $resutl['feedCompanyId'],
                    "month_name"=> $resutl['monthName'],
                    "year"=> $resutl['yearName'],
                    "breed_name"=>$resutl['breedName'],
                    "product_wise_qty"=> $resutl['productWiseQty'],
                    "total_qty"=> (string)$resutl['totalQty'],
                    "created_at"=> $resutl['createdAt']->format('Y-m-d H:i:s')
                ];
            }
        }
        return $returnArray;
    }

}
