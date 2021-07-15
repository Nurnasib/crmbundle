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
use Terminalbd\CrmBundle\Entity\CompanyWiseFeedSale;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class CompanyWiseFeedSaleRepository extends BaseRepository
{
    public function getExitingCheckCompanyWiseFeedSaleByMonthYearEmployeeAndCompany($year, $month, $feedCompany, $employee, $breed_name)
    {
        if($year&&$month&&$feedCompany&&$employee&&$breed_name){
            $query = $this->createQueryBuilder('cwfs')
                ->select('cwfs.id')
                ->where('cwfs.year = :year')
                ->andWhere('cwfs.monthName = :month')
                ->andWhere('cwfs.employee = :employee')
                ->andWhere('cwfs.feedCompany = :feedCompany')
                ->andWhere('cwfs.breedName = :breed_name')
                ->setParameters(array('year'=>$year, 'month'=>$month, 'feedCompany'=>$feedCompany, 'employee'=>$employee, 'breed_name'=>$breed_name));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getCompanyWiseFeedSaleByCreatedDateAndEmployee($year, $month, $employee, $breed_name)
    {
        if($year&&$employee&&$breed_name){
            $query = $this->createQueryBuilder('cwfs')
                ->where('cwfs.year IN (:year)')
                ->andWhere('cwfs.monthName IN (:month)')
                ->andWhere('cwfs.employee = :employee')
                ->andWhere('cwfs.breedName = :breed_name')
                ->setParameters(array('year'=>$year, 'month'=>$month, 'employee'=>$employee, 'breed_name'=>$breed_name));

            $resutls = $query->getQuery()->getResult();
            $returnArray=[];
            if($resutls){
                /* @var CompanyWiseFeedSale $value*/
                foreach ($resutls as $value){
                    $decodeValue = json_decode($value->getProductWiseQty(),true);
                    $arraySum = $decodeValue?array_sum($decodeValue):0;
                    $returnArray['items'][$value->getYear()][$value->getMonthName()][$value->getFeedCompany()->getId()]=$value;
                    if (isset($returnArray['grand_total'][$value->getYear()][$value->getMonthName()]))
                    {
                        $returnArray['grand_total'][$value->getYear()][$value->getMonthName()] += $arraySum;
                    }
                    else
                    {
                        $returnArray['grand_total'][$value->getYear()][$value->getMonthName()] = $arraySum;
                    }
                }
            }

//            dd($returnArray);

            return $returnArray;
        }
        return array();
    }

}
