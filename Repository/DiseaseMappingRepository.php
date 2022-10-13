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
class DiseaseMappingRepository extends EntityRepository
{
    public function getDiseaseMappingByCreatedDateEmployeeReport($report, $employee)
    {
        if($report&&$employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('dm')
                ->where('dm.createdAt >= :startDate')
                ->andWhere('dm.createdAt <= :endDate')
                ->andWhere('dm.report = :report')
                ->andWhere('dm.employee = :employee')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getResult();
        }
        return array();
    }

    public function getMonthlyTroubleshootingAndDiseasesMappingTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');
        $qb->join('e.employee', 'employee');
        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.visitingDate >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.visitingDate <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }

    public function getDiseasesMappingReportByEmployeeDate($report, $filterBy, User $loggedUser)
    {
        $returnArray=[];
        if(!empty($report)){
            $qb = $this->createQueryBuilder('e');
            $qb->select('e.id eId', 'e.visitingDate', 'e.flockSizeOrCapacity', 'e.ageDays', 'e.remarks', 'e.ageUnitType', 'e.treatment', 'e.cultureAreaForFish', 'e.dencityForFish', 'e.averageWeightForFish');

            $qb->addSelect('agent.name AS agentName', 'agent.address AS agentAddress', 'agent.mobile AS agentMobile');
            $qb->addSelect('breed.id AS breedId', 'breed.name AS breedName');

            $qb->addSelect('employee.id AS employeeId', 'employee.name AS employeeName');
            $qb->addSelect('designation.name AS employeeDesignationName');
            $qb->addSelect('customer.id AS customerId', 'customer.name AS customerName', 'customer.mobile AS customerMobile', 'customer.address AS customerAddress');

            $qb->addSelect( 'district.name AS agentDistrictName');
            $qb->addSelect( 'region.id AS agentRegionId', 'region.name AS agentRegionName');

            $qb->addSelect('hatchery.name AS hatcheryName');
            $qb->addSelect('feed.name AS feedName');
            $qb->addSelect('farmType.name AS feedTypeName');
            $qb->addSelect('disease.name AS diseaseName');

            $qb->join('e.employee', 'employee');
            $qb->leftJoin('employee.designation', 'designation');
            $qb->leftJoin('e.customer','customer');
            $qb->leftJoin('e.agent', 'agent');
            $qb->leftJoin('agent.district', 'district');
            $qb->leftJoin('district.parent', 'region');
            $qb->leftJoin('e.hatchery', 'hatchery');
            $qb->leftJoin('e.feed', 'feed');
            $qb->leftJoin('e.farmType', 'farmType');
            $qb->leftJoin('e.disease', 'disease');
            $qb->leftJoin('e.breed', 'breed');
            $qb->where('e.report =:report')->setParameter('report',$report);

            $startDate = isset($filterBy['startDate'])&&$filterBy['startDate']!=''? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00': '';
            $endDate = isset($filterBy['endDate']) && $filterBy['endDate']!=''? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59': '';

            $employee = isset($filterBy['employeeId'])&&$filterBy['employeeId']!=''? $filterBy['employeeId']: '';
            if (!empty($employee)){
                $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee);
            }

            if (!empty($startDate) && !empty($endDate)){
                $qb->andWhere('e.visitingDate >= :visitingDateStart')->setParameter('visitingDateStart', $startDate);
                $qb->andWhere('e.visitingDate <= :visitingDateEnd')->setParameter('visitingDateEnd', $endDate);
            }


            $rolesString = implode('_', $loggedUser->getRoles());

            if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
                $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
            }
            if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
                $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
            }
            
            $qb->orderBy('e.visitingDate','ASC');
            $results = $qb->getQuery()->getArrayResult();
            if($results){
                foreach ($results as $result){
                    $monthYear = $result['visitingDate']->format('F-Y');

                    $returnArray['records'][$monthYear][$result['agentRegionId']][$result['employeeId']]['details'][] = $result;
                    $returnArray['regionRecords'][$monthYear][$result['agentRegionId']][]=$result;


                    /*$returnArray[$monthYear][$result['employeeId']]['name']=$result['employeeName'];
                    $returnArray[$monthYear][$result['employeeId']]['employeeDesignationName']=$result['employeeDesignationName'];
                    $returnArray[$monthYear][$result['employeeId']]['details'][]=$result;*/
                }
            }
        }
        return $returnArray;
    }

}
