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
class LayerPerformanceRepository extends EntityRepository
{

    public function getLayerPerformanceReportByReportingDateAndFeedType($data, $employee)
    {
        if(isset($data['reporting_month'])){
            $startDate = date('Y-m-01', strtotime($data['reporting_month']));
            $endDate = date('Y-m-t', strtotime($data['reporting_month']));
            $query = $this->createQueryBuilder('lpr')
                ->where('lpr.reportingMonth >= :startDate')
                ->andWhere('lpr.reportingMonth <= :endDate')
                ->andWhere('lpr.employee = :employee')
                ->andWhere('lpr.breed = :breed')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'employee'=>$employee, 'breed'=>$data['breed']));

            return $query->getQuery()->getArrayResult();
        }
        return array();
    }
}
