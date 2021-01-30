<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Repository\NewFarmerTouch;

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
class FarmerTouchRepository extends EntityRepository
{

    public function getFishFarmerTouchReportByDateAndEmployeeAndReport($report, $employee)
    {
        if($report&&$employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('fft')
                ->where('fft.createdAt >= :startDate')
                ->andWhere('fft.createdAt <= :endDate')
                ->andWhere('fft.reportParentParent = :report')
                ->andWhere('fft.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getResult();
        }
        return array();
    }

}
