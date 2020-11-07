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

    public function getFcrReportByReportingDateAndFeedType($data)
    {
        if(isset($data['reporting_month']) && isset($data['fcr_of_feed'])){
            $startDate = date('Y-m-01', strtotime($data['reporting_month']));
            $endDate = date('Y-m-t', strtotime($data['reporting_month']));
            $query = $this->createQueryBuilder('f')
                ->where('f.reportingMonth >= :startDate')
                ->andWhere('f.reportingMonth <= :endDate')
                ->andWhere('f.fcrOfFeed = :type')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'type'=>$data['fcr_of_feed']));

            return $query->getQuery()->getResult();
        }
        return array();
    }


}
