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

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class AntibioticFreeFarmRepository extends EntityRepository
{
    public function getAntibioticFreeFarmByReportingMonthEmployeeCustomerAndReport($report, $employee, $customer, $reportingMonth)
    {
        if($report&&$employee){
            $startDate = date('Y-m-01', strtotime($reportingMonth));
            $endDate = date('Y-m-t', strtotime($reportingMonth));
            $query = $this->createQueryBuilder('aff')
                ->where('aff.reportingMonth >= :startDate')
                ->andWhere('aff.reportingMonth <= :endDate')
                ->andWhere('aff.report = :report')
                ->andWhere('aff.customer = :customer')
                ->andWhere('aff.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'customer'=>$customer, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

}
