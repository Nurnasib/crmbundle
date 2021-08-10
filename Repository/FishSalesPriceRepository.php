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
use Terminalbd\CrmBundle\Entity\FishSalesPrice;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FishSalesPriceRepository extends BaseRepository
{

    public function getFishSalesPriceByCreatedDateAndEmployee($year, $month, $employee)
    {
        if($year&&$employee){
            $query = $this->createQueryBuilder('fsp')
                ->where('fsp.year IN (:year)')
                ->andWhere('fsp.monthName IN (:month)')
                ->andWhere('fsp.employee = :employee')
                ->setParameters(array('year'=>$year, 'month'=>$month, 'employee'=>$employee));

            $resutls = $query->getQuery()->getResult();
            $returnArray=[];
            if($resutls){
                /* @var FishSalesPrice $value*/
                foreach ($resutls as $value){
                    $returnArray['items'][$value->getYear()][$value->getMonthName()][$value->getFishSize()->getId()]=$value;
                }
            }

            return $returnArray;
        }
        return array();
    }
}
