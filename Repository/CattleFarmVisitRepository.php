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
class CattleFarmVisitRepository extends EntityRepository
{

    public function getCattleFarmVisitReportByReportingDateCustomerAndEmployee($report, $customer, $employee)
    {
        if($report&&$customer&&$employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('cp')
                ->where('cp.reportingMonth >= :startDate')
                ->andWhere('cp.reportingMonth <= :endDate')
                ->andWhere('cp.report = :report')
                ->andWhere('cp.customer = :customer')
                ->andWhere('cp.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'customer'=>$customer, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }
}
