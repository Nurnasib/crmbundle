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



}
