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
class FcrRepository extends EntityRepository
{

    public function getFcrReportByReportingDateAndFeedType($data, $report, $customer, $employee)
    {
        if(isset($data) && $report && $customer && $employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('f')
                ->where('f.reportingMonth >= :startDate')
                ->andWhere('f.reportingMonth <= :endDate')
                ->andWhere('f.fcrOfFeed = :type')
                ->andWhere('f.report = :report')
                ->andWhere('f.employee = :employee')
                ->andWhere('f.customer = :customer')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'type'=>$data, 'report'=>$report, 'customer'=>$customer, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }


    public function getFcrReportByReportingDateReportAndEmployeeForAfter($data, $report, $employee)
    {
        if(isset($data) && $report && $employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('f')
                ->where('f.reportingMonth >= :startDate')
                ->andWhere('f.reportingMonth <= :endDate')
                ->andWhere('f.fcrOfFeed = :type')
                ->andWhere('f.report = :report')
                ->andWhere('f.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'type'=>$data, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }


}
