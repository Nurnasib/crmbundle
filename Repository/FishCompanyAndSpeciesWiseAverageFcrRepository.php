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
class FishCompanyAndSpeciesWiseAverageFcrRepository extends EntityRepository
{

    public function getFishCompanySpeciesWiseFcrByReportingMonth( $beforAfter='BEFORE', $feedType, $reportingDate, $employee)
    {
        if($reportingDate && $employee){
            $startDate = date('Y-m-01', strtotime($reportingDate));
            $endDate = date('Y-m-t', strtotime($reportingDate));
            $query = $this->createQueryBuilder('fcaswaf')
                ->join('fcaswaf.feed','f')
                ->where('fcaswaf.employee = :employee')
                ->andWhere('fcaswaf.feedType = :feedType')
                ->andWhere('fcaswaf.fcrOfFeed = :beforAfter')
                ->andWhere('fcaswaf.reportingMonth >= :startDate')
                ->andWhere('fcaswaf.reportingMonth <= :endDate')
                ->setParameters(array('beforAfter'=>$beforAfter, 'feedType'=>$feedType, 'startDate'=>$startDate, 'endDate'=>$endDate, 'employee'=>$employee))
                ->groupBy('fcaswaf.feed')
                ->orderBy('f.name','ASC');

            return $query->getQuery()->getResult();
        }
        return array();
    }

    public function getFishCompanySpeciesWiseFcrByReportingDateAndIds( $ids, $reportingDate, $employee)
    {
        if($ids && $reportingDate && $employee){
            $startDate = date('Y-m-d', strtotime($reportingDate));
            $query = $this->createQueryBuilder('fcaswaf')
                ->where('fcaswaf.reportingMonth = :startDate')
                ->andWhere('fcaswaf.employee = :employee')
                ->andWhere('fcaswaf.id IN (:id)')
                ->setParameters(array('startDate'=>$startDate, 'id'=>$ids, 'employee'=>$employee));

            return $query->getQuery()->getResult();
        }
        return array();
    }


}
