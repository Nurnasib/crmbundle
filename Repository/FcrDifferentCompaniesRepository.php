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
use Terminalbd\CrmBundle\Entity\FcrDifferentCompanies;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FcrDifferentCompaniesRepository extends EntityRepository
{
    public function getExitingCheckFcrDifferentCompanyByCreatedDateEmployeeAndCompany($employee, $company, $breed_name)
    {
        if($company&&$employee){
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $query = $this->createQueryBuilder('fdc')
                ->select('fdc.id')
                ->where('fdc.createdAt >= :startDate')
                ->andWhere('fdc.createdAt <= :endDate')
                ->andWhere('fdc.employee = :employee')
                ->andWhere('fdc.hatchery = :company')
                ->andWhere('fdc.breedName = :breed_name')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'company'=>$company, 'employee'=>$employee, 'breed_name'=>$breed_name));
            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getFcrDifferentCompanyByCreatedDateAndEmployee($employee, $breed_name)
    {
        if($employee){
            $startDate = date('Y-01-01');
//            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $query = $this->createQueryBuilder('fdc')
                ->where('fdc.createdAt >= :startDate')
                ->andWhere('fdc.createdAt <= :endDate')
                ->andWhere('fdc.employee = :employee')
                ->andWhere('fdc.breedName = :breed_name')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'employee'=>$employee, 'breed_name'=>$breed_name));
            $returnArray = [];

            /* @var FcrDifferentCompanies $value*/
            foreach ($query->getQuery()->getResult() as $value){
                $returnArray[$value->getHatchery()->getId()]=array(
                    'january'=>['id'=>$value->getId(),'value'=>$value->getJanuary()],
                    'february'=>['id'=>$value->getId(),'value'=>$value->getFebruary()],
                    'march'=>['id'=>$value->getId(),'value'=>$value->getMarch()],
                    'april'=>['id'=>$value->getId(),'value'=>$value->getApril()],
                    'may'=>['id'=>$value->getId(),'value'=>$value->getMay()],
                    'june'=>['id'=>$value->getId(),'value'=>$value->getJune()],
                    'july'=>['id'=>$value->getId(),'value'=>$value->getJuly()],
                    'august'=>['id'=>$value->getId(),'value'=>$value->getAugust()],
                    'september'=>['id'=>$value->getId(),'value'=>$value->getSeptember()],
                    'october'=>['id'=>$value->getId(),'value'=>$value->getOctober()],
                    'november'=>['id'=>$value->getId(),'value'=>$value->getNovember()],
                    'december'=>['id'=>$value->getId(),'value'=>$value->getDecember()],
                );
            }
            return $returnArray;
        }
        return array();
    }

    public function getFcrDifferentCompaniesReport($filterBy, User $loggedUser)
    {
        $year = isset($filterBy['year']) && $filterBy['year']!=''?$filterBy['year']:date('Y');
        $startDate = date('Y-01-01');
        $endDate = date('Y-12-31');
        
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00' : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59' : null;


        $qb = $this->createQueryBuilder('e');

        $qb->join('e.hatchery', 'hatchery');
        $qb->join('e.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');

        $qb->select('e AS details');
        $qb->addSelect('hatchery.id AS companyId', 'hatchery.name AS companyName');
        $qb->addSelect('employee.id AS employeeId','employee.userId', 'employee.name AS employeeName');
        $qb->addSelect('designation.name AS employeeDesignationName');

        $qb->andWhere('e.january > 0 OR e.february > 0 OR e.march > 0 OR e.april > 0 OR e.may > 0 
        OR e.june > 0 OR e.july > 0 OR e.august > 0 OR e.september > 0 OR e.october > 0 OR e.november > 0 OR e.december > 0');

        if(isset($filterBy['otherReport']) && $filterBy['otherReport']=='fcr-different-companies-poultry'){
            $qb->andWhere('e.breedName =:breedName')->setParameter('breedName','poultry');
        }
        if(isset($filterBy['otherReport']) && $filterBy['otherReport']=='fcr-different-companies-sonali'){
            $qb->andWhere('e.breedName =:breedName')->setParameter('breedName','sonali');
        }

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

        $qb->andWhere('YEAR(e.createdAt) =:year')->setParameter('year',$year);

        $results = $qb->getQuery()->getArrayResult();
        $data = [];

        foreach ($results as $result) {
//            $month = $result['details']['createdAt']->format('Y-m-F');

            $result['details']['companyId'] = $result['companyId'];
            $result['details']['companyName'] = $result['companyName'];
            $result['details']['employeeId'] = $result['employeeId'];
            $result['details']['employeeName'] = $result['employeeName'];
            $result['details']['employeeDesignationName'] = $result['employeeDesignationName'];
            $data['records'][$result['employeeId']][] = $result['details'];
            $data['employeeInfo'][$result['employeeId']] = ['userId'=>$result['userId'], 'employeeName'=>$result['employeeName'], 'designationName'=>$result['employeeDesignationName']];
        }
//        dd($data);
//        ksort($data);
        return $data;
    }

    public function getExists($employeeId, $hatcheryId, $breedName, $createdAt)
    {
        $startDate = (new \DateTime($createdAt))->format('Y-01-01 00:00:00');
        $endDate = (new \DateTime($createdAt))->format('Y-12-31 23:59:59');

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.employee', 'employee');
        $qb->join('e.hatchery', 'hatchery');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        $qb->andWhere('hatchery.id = :hatcheryId')->setParameter('hatcheryId', $hatcheryId);
        $qb->andWhere('e.breedName = :breedName')->setParameter('breedName', strtolower($breedName));
        $qb->andWhere('e.createdAt >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.createdAt <= :endDate')->setParameter('endDate', $endDate);

        return $qb->getQuery()->getOneOrNullResult();

    }


    public function getFcrDifferentCompaniesApi($employeeId, $year)
    {
        $data = [];
        if($employeeId){

//            $year = isset($year) && $year!=''?$year:date('Y');

            $qb = $this->createQueryBuilder('e');

            $qb->join('e.hatchery', 'hatchery');
            $qb->join('e.employee', 'employee');

            $qb->select('e AS details');
            $qb->addSelect('hatchery.id AS companyId', 'hatchery.name AS companyName');
            $qb->addSelect('employee.id AS employeeId','employee.userId', 'employee.name AS employeeName');

            $qb->andWhere('e.january > 0 OR e.february > 0 OR e.march > 0 OR e.april > 0 OR e.may > 0 
        OR e.june > 0 OR e.july > 0 OR e.august > 0 OR e.september > 0 OR e.october > 0 OR e.november > 0 OR e.december > 0');

            $qb->andWhere('employee.id = :employee')->setParameter('employee', $employeeId);

            $qb->andWhere('YEAR(e.createdAt) =:year')->setParameter('year',$year);

            $results = $qb->getQuery()->getArrayResult();

            foreach ($results as $result) {
//            $month = $result['details']['createdAt']->format('Y-m-F');

//                $result['details']['hatchery_id'] = $result['companyId'];
//                $result['details']['employee_id'] = $result['employeeId'];
                $result['details']['created_at'] = $result['details']['createdAt']->format('Y-m-d H:i:s');
                $data[] = [
                    "id"=> $result['details']['id'],
                    "employee_id"=> $result['employeeId'],
                    "hatchery_id"=> $result['companyId'],
                    "breed_name"=> $result['details']['breedName'],
                    "january"=> (string)$result['details']['january'],
                    "february"=> (string)$result['details']['february'],
                    "march"=> (string)$result['details']['march'],
                    "april"=> (string)$result['details']['april'],
                    "may"=> (string)$result['details']['may'],
                    "june"=> (string)$result['details']['june'],
                    "july"=> (string)$result['details']['july'],
                    "august"=> (string)$result['details']['august'],
                    "september"=> (string)$result['details']['september'],
                    "october"=> (string)$result['details']['october'],
                    "november"=> (string)$result['details']['november'],
                    "december"=> (string)$result['details']['december'],
                    "created_at"=> $result['details']['createdAt']->format('Y-m-d H:i:s')
                ];
            }
        }
        return $data;
    }


}
