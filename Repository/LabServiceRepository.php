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
use Terminalbd\CrmBundle\Entity\LabService;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class LabServiceRepository extends EntityRepository
{
    public function getExitingCheckLabServiceByCreatedDateEmployeeAndCompany($employee, $lab, $labService, $breed_name)
    {
        if($lab&&$labService&&$employee){
            $year = date('Y');
            $query = $this->createQueryBuilder('ls')
                ->select('ls.id')
                ->where('ls.reportingYear >= :reportingYear')
                ->andWhere('ls.employee = :employee')
                ->andWhere('ls.lab = :lab')
                ->andWhere('ls.service = :labService')
                ->andWhere('ls.breedName = :breed_name')
                ->setParameters(array('reportingYear'=>$year, 'lab'=>$lab, 'labService'=>$labService, 'employee'=>$employee, 'breed_name'=>$breed_name));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getLabServiceByCreatedDateAndEmployee($employee, $breed_name)
    {
        if($employee){
            $startDate = date('Y-01-01');
//            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $query = $this->createQueryBuilder('ls')
                ->where('ls.createdAt >= :startDate')
                ->andWhere('ls.createdAt <= :endDate')
                ->andWhere('ls.employee = :employee')
                ->andWhere('ls.breedName = :breed_name')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'employee'=>$employee, 'breed_name'=>$breed_name));
            $returnArray = [];

            /* @var LabService $value*/
            foreach ($query->getQuery()->getResult() as $value){
                $returnArray[$value->getLab()->getId()][$value->getService()->getId()]=array(
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
//            dd($returnArray);
            return $returnArray;
        }
        return array();
    }


    public function getExitingLabService($employee, $lab, $labService, $breed_name, $year)
    {
        if($lab&&$labService&&$employee){
            $query = $this->createQueryBuilder('ls')
                ->join('ls.employee','employee')
                ->join('ls.lab','lab')
                ->join('ls.service','service')
                ->select('ls.id','ls.january','ls.february','ls.march','ls.april','ls.may','ls.june','ls.july','ls.august','ls.september','ls.october','ls.november','ls.december')
                ->where('ls.reportingYear >= :reportingYear')
                ->andWhere('employee.id = :employeeId')
                ->andWhere('lab.id = :lab')
                ->andWhere('service.id = :labService')
                ->andWhere('ls.breedName = :breed_name')
                ->setParameters(array('reportingYear'=>$year, 'lab'=>$lab, 'labService'=>$labService, 'employeeId'=>$employee, 'breed_name'=>$breed_name));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getLabServiceSummaryReport($filterBy, User $loggedUser)
    {
        $start = isset($filterBy['year']) && $filterBy['year'] ? (new \DateTime($filterBy['year'].'-01-01'))->format('Y-m-d') . ' 00:00:00' : date('Y-01-01'). ' 00:00:00';
        $end = isset($filterBy['year']) && $filterBy['year'] ? (new \DateTime($filterBy['year'].'-12-31'))->format('Y-m-d') . ' 23:59:59' : date('Y-12-31'). ' 23:59:59';

        $year = isset($filterBy['year']) && $filterBy['year'] ?$filterBy['year']:date('Y');

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.service', 'service');
        $qb->join('e.lab', 'lab');
        $qb->join('e.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');

        $qb->select('e.id as eId','e.january','e.february','e.march','e.april','e.may','e.june','e.july','e.august','e.september','e.october','e.november','e.december','e.createdAt as eCreatedAt');
        $qb->addSelect('service.id AS serviceId', 'service.name AS serviceName');
        $qb->addSelect('lab.id AS labId', 'lab.name AS labName');
        $qb->addSelect('employee.id as empId','employee.name as employeeName','employee.userId');
        $qb->addSelect('designation.name as employeeDesignationName');

        if(isset($filterBy['lab']) && $filterBy['lab']){
            $qb->andWhere('e.lab = :lab')->setParameter('lab', $filterBy['lab']);
        }
        $qb->andWhere('YEAR(e.createdAt) =:year')->setParameter('year',$year);

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
        $returnArray = [];

        foreach ($results as $result) {
            $returnArray[$year][$result['empId']]['userId']=$result['userId'];
            $returnArray[$year][$result['empId']]['employeeName']=$result['employeeName'];
            $returnArray[$year][$result['empId']]['employeeDesignationName']=$result['employeeDesignationName'];
            $returnArray[$year][$result['empId']]['labs'][$result['labId']]=$result['labName'];
            $returnArray[$year][$result['empId']]['services'][$result['serviceId']]=$result['serviceName'];
            $returnArray[$year][$result['empId']]['items'][$result['labId']][$result['serviceId']]=array(
                'january'=>$result['january'],
                'february'=>$result['february'],
                'march'=>$result['march'],
                'april'=>$result['april'],
                'may'=>$result['may'],
                'june'=>$result['june'],
                'july'=>$result['july'],
                'august'=>$result['august'],
                'september'=>$result['september'],
                'october'=>$result['october'],
                'november'=>$result['november'],
                'december'=>$result['december'],
            );
            
            $returnArray[$year][$result['empId']]['grandTotals'][$result['labId']][]=array(
                'january'=>$result['january'],
                'february'=>$result['february'],
                'march'=>$result['march'],
                'april'=>$result['april'],
                'may'=>$result['may'],
                'june'=>$result['june'],
                'july'=>$result['july'],
                'august'=>$result['august'],
                'september'=>$result['september'],
                'october'=>$result['october'],
                'november'=>$result['november'],
                'december'=>$result['december'],
            );
        }
        return $returnArray;
    }


    public function getLabServicesDataForApiImport($employeeId, $year)
    {
        $data = [];
        if($employeeId){

            $qb = $this->createQueryBuilder('e');

            $qb->join('e.lab', 'lab');
            $qb->join('e.service', 'service');
            $qb->join('e.employee', 'employee');

            $qb->select('e AS details');
            $qb->addSelect('lab.id AS labId', 'lab.name AS labName');
            $qb->addSelect('service.id AS serviceId', 'service.name AS serviceName');
            $qb->addSelect('employee.id AS employeeId','employee.userId', 'employee.name AS employeeName');

            $qb->andWhere('e.january > 0 OR e.february > 0 OR e.march > 0 OR e.april > 0 OR e.may > 0 
        OR e.june > 0 OR e.july > 0 OR e.august > 0 OR e.september > 0 OR e.october > 0 OR e.november > 0 OR e.december > 0');

            $qb->andWhere('employee.id = :employee')->setParameter('employee', $employeeId);

            $qb->andWhere('YEAR(e.createdAt) =:year')->setParameter('year',$year);

            $results = $qb->getQuery()->getArrayResult();

            foreach ($results as $result) {
                $data[] = [
                    "id"=> $result['details']['id'],
                    "employee_id"=> $result['employeeId'],
                    "lab_id"=> $result['labId'],
                    "service_id"=> $result['serviceId'],
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
