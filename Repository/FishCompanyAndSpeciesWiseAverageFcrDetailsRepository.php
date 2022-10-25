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
use Terminalbd\CrmBundle\Entity\Fcr;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FishCompanyAndSpeciesWiseAverageFcrDetailsRepository extends EntityRepository
{
    public function getCompanyAndSpeciesWiseFcrByEmployee($report, $fcrOfFeed, User $employee){
        $startDate = date('Y-m-01', strtotime("-1 month"));
        $endDate = date('Y-m-t');

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.fishCompanyAndSpeciesWiseAverageFcr', 'parent');
        $qb->join('parent.employee', 'employee');
        $qb->join('parent.feedType', 'feed_type');
        $qb->join('parent.feed', 'feed');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->join('e.speciesName', 'species_name');

        $qb->select('e.quantity',);
        $qb->addSelect('parent.id as fcrId','parent.createdAt as fcrCreatedAt');
        $qb->addSelect('employee.id as employeeId','employee.userId', 'employee.name as employeeName');
        $qb->addSelect( 'designation.name AS designationName');
        $qb->addSelect('species_name.id AS speciesId', 'species_name.name AS speciesName');
        $qb->addSelect('feed.id AS feedId', 'feed.name AS feedName');
        $qb->addSelect('feed_type.id AS feedTypeId', 'feed_type.name AS feedTypeName');
        $qb->addSelect('MONTH(parent.reportingMonth) AS month', 'YEAR(parent.reportingMonth) AS year', 'parent.reportingMonth as reportingMonth');

        $qb->where('parent.reportingMonth >= :start')->setParameter('start', $startDate);
        $qb->andWhere('parent.reportingMonth <= :end')->setParameter('end', $endDate);
        $qb->andWhere('parent.fcrOfFeed = :fcrOfFeed')->setParameter('fcrOfFeed', $fcrOfFeed);
        $qb->andWhere('employee.id =:employee')->setParameter('employee', $employee->getId());
        $qb->andWhere('parent.report =:report')->setParameter('report', $report);

        $results = $qb->getQuery()->getArrayResult();

        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $reportingMonth=$result['reportingMonth']->format('F Y');
                $returnArray['records'][$result['feedTypeId']][$reportingMonth][$result['fcrId']][$result['speciesId']]=$result;
                $returnArray['fcrInfo'][$result['feedTypeId']][$reportingMonth][$result['fcrId']]=['feedName'=>$result['feedName'],'createdAt'=>$result['fcrCreatedAt']->format('d-m-Y')];
            }
        }
        return $returnArray;

    }
    
    public function getCompanySpeciesWiseFcrDetailsByReportingMonth($beforeAfter, $feedType, $reportingMonth, $employee){
        $startDate = date('Y-m-01', strtotime($reportingMonth));
        $endDate = date('Y-m-t', strtotime($reportingMonth));
        $em = $this->getEntityManager();
        $sql = "SELECT fcrDetails.species_name_id, fcr.feed_type_id as feedTypeId, fcr.employee_id, cs.id as feedId, cs.name as feedName, AVG(fcrDetails.quantity) as avgQty FROM crm_fish_company_species_wise_average_fcr_details fcrDetails 
JOIN crm_fish_company_species_wise_average_fcr fcr ON fcr.id=fcrDetails.fish_company_and_species_wise_fcr_id
JOIN crm_setting cs ON cs.id=fcr.feed_id
WHERE fcrDetails.quantity>0 and fcr.employee_id = :employee_id and fcr.feed_type_id = :feed_type_id and fcr.fcr_of_feed = :beforeAfter and fcr.reporting_month >= :startDate and fcr.reporting_month <= :endDate GROUP BY fcrDetails.species_name_id, fcr.feed_id";
        $qb = $em->getConnection()->prepare($sql);
        $qb->bindValue('beforeAfter', $beforeAfter);
        $qb->bindValue('startDate', $startDate);
        $qb->bindValue('endDate', $endDate);
        $qb->bindValue('employee_id', $employee);
        $qb->bindValue('feed_type_id', $feedType);
        $qb->execute();

        $results =  $qb->fetchAll();

        $returnArray = [];
        foreach ($results as $result){
            $returnArray[$result['feedId']][$result['feedTypeId']][$result['species_name_id']]=$result;
        }

        return $returnArray;
    }

    public function getAverageFcrReport($fcrOfFeed, $filterBy, $loggedUser)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;
        $employeeId = isset($filterBy['employeeId']) && $filterBy['employeeId']!=''?$filterBy['employeeId']: null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.fishCompanyAndSpeciesWiseAverageFcr', 'parent');
        $qb->join('parent.employee', 'employee');
        $qb->join('parent.feedType', 'feed_type');
        $qb->join('parent.feed', 'feed');
        $qb->join('employee.userGroup', 'user_group');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->join('e.speciesName', 'species_name');

        $qb->select('e.quantity');
        $qb->addSelect('parent.id as fcrId','parent.createdAt as fcrCreatedAt');
        $qb->addSelect('employee.id as employeeId','employee.userId', 'employee.name as employeeName');
        $qb->addSelect( 'designation.name AS designationName');
        $qb->addSelect('species_name.id AS speciesId', 'species_name.name AS speciesName');
        $qb->addSelect('feed.id AS feedId', 'feed.name AS feedName');
        $qb->addSelect('feed_type.id AS feedTypeId', 'feed_type.name AS feedTypeName');
        $qb->addSelect('MONTH(parent.reportingMonth) AS month', 'YEAR(parent.reportingMonth) AS year', 'parent.reportingMonth');

        $qb->where('parent.reportingMonth >= :start')->setParameter('start', $start);
        $qb->andWhere('parent.reportingMonth <= :end')->setParameter('end', $end);
        $qb->andWhere('user_group.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');
        $qb->andWhere('parent.fcrOfFeed = :fcrOfFeed')->setParameter('fcrOfFeed', $fcrOfFeed);
        $qb->andWhere('e.quantity >:quantity')->setParameter('quantity',0);


        $rolesString = implode('_', $loggedUser->getRoles());

        if ($employeeId){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
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
            $month = $result['reportingMonth']->format('m-F-Y');

            $data['employeeInfo'][$result['employeeId']] = [
                'userId' => $result['userId'],
                'name' => $result['employeeName'],
                'designation' => $result['designationName']
            ];
            $data['feedTypeInfo'][$result['employeeId']][$month][$result['feedTypeId']]=$result['feedTypeName'];
            $data['records'][$result['employeeId']][$month][$result['feedTypeId']][$result['fcrId']][$result['speciesId']]=$result;
            $data['fcrInfo'][$result['employeeId']][$month][$result['feedTypeId']][$result['fcrId']]=['feedName'=>$result['feedName'],'createdAt'=>$result['fcrCreatedAt']->format('d-m-Y')];
            ksort($data['records'][$result['employeeId']]);
        }

        return $data;


    }



}
