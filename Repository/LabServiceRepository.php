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
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $query = $this->createQueryBuilder('ls')
                ->select('ls.id')
                ->where('ls.createdAt >= :startDate')
                ->andWhere('ls.createdAt <= :endDate')
                ->andWhere('ls.employee = :employee')
                ->andWhere('ls.lab = :lab')
                ->andWhere('ls.service = :labService')
                ->andWhere('ls.breedName = :breed_name')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'lab'=>$lab, 'labService'=>$labService, 'employee'=>$employee, 'breed_name'=>$breed_name));

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

    public function getLabServiceSummaryReport($filterBy, User $loggedUser)
    {
        $start = isset($filterBy['year']) && $filterBy['year'] ? (new \DateTime($filterBy['year'].'-01-01'))->format('Y-m-d') . ' 00:00:00' : date('Y-01-01'). ' 00:00:00';
        $end = isset($filterBy['year']) && $filterBy['year'] ? (new \DateTime($filterBy['year'].'-12-31'))->format('Y-m-d') . ' 23:59:59' : date('Y-12-31'). ' 23:59:59';
        $employeeId = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;

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

        $qb->andWhere('e.createdAt >= :start')->setParameter('start', $start);
        $qb->andWhere('e.createdAt <= :end')->setParameter('end', $end);

        $rolesString = implode($loggedUser->getRoles(), '_');
        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }
        if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
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
}
