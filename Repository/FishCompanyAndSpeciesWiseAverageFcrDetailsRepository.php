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
        $employeeId = isset($filterBy['employeeId']) ?: null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.fishCompanyAndSpeciesWiseAverageFcr', 'parent');
        $qb->join('parent.employee', 'employee');
        $qb->join('parent.feedType', 'feed_type');
        $qb->join('parent.feed', 'feed');
        $qb->join('employee.userGroup', 'user_group');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->join('e.speciesName', 'species_name');

        $qb->select('AVG(e.quantity) AS avgQuantity','e.quantity');
        $qb->addSelect('employee.userId', 'employee.name');
        $qb->addSelect( 'designation.name AS designationName');
        $qb->addSelect('species_name.id AS speciesId', 'species_name.name AS speciesName');
        $qb->addSelect('feed.id AS feedId', 'feed.name AS feedName');
        $qb->addSelect('feed_type.id AS feedTypeId', 'feed_type.name AS feedTypeName');
        $qb->addSelect('MONTH(parent.reportingMonth) AS month', 'YEAR(parent.reportingMonth) AS year', 'parent.reportingMonth');


        $qb->where('parent.reportingMonth >= :start')->setParameter('start', $start);
        $qb->andWhere('parent.reportingMonth <= :end')->setParameter('end', $end);
        $qb->andWhere('user_group.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');
        $qb->andWhere('parent.fcrOfFeed = :fcrOfFeed')->setParameter('fcrOfFeed', $fcrOfFeed);

        $qb->groupBy('species_name.id');
        $qb->addGroupBy('feedId');
        $qb->addGroupBy('month');
        $qb->addGroupBy('year');

        $rolesString = implode('_', $loggedUser->getRoles());

        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }
        if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        $results = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($results as $result) {
            $month = $result['reportingMonth']->format('m-F');
            $data[$result['reportingMonth']->format('Y')][$month][$result['userId']]['employee'] = [
                'userId' => $result['userId'],
                'name' => $result['name'],
                'designation' => $result['designationName']
            ];
            $data[$result['reportingMonth']->format('Y')][$month][$result['userId']]['data'][$result['feedName']][$result['feedTypeName']][$result['speciesId']] = $result;

            ksort($data);
        }
        return $data;


    }



}
