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


    public function getCompanyWiseFeedSaleReport($breedName, $filterBy)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00' : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59' : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.feedCompany', 'feedCompany');

        $qb->select('e.monthName', 'e.year', 'e.breedName', 'e.productWiseQty', 'e.totalQty', 'e.createdAt');
        $qb->addSelect('feedCompany.id AS feedCompanyId', 'feedCompany.name AS feedCompanyName');

        $qb->where('e.employee = :employee')->setParameter('employee', $filterBy['employee']);
        $qb->andWhere('e.createdAt >= :start')->setParameter('start', $start);
        $qb->andWhere('e.createdAt <= :end')->setParameter('end', $end);
        $qb->andWhere('e.breedName = :breedName')->setParameter('breedName', $breedName);

        $results = $qb->getQuery()->getArrayResult();
        $data = [];

        foreach ($results as $result) {
            $data[$result['monthName'] . '-' . $result['year']]['details'][] = $result;
            if (array_key_exists('companyTotalQty', $data[$result['monthName'] . '-' . $result['year']])){
                $data[$result['monthName'] . '-' . $result['year']]['companyTotalQty'] += $result['totalQty'];
            }else{
                $data[$result['monthName'] . '-' . $result['year']]['companyTotalQty'] = $result['totalQty'];

            }
        }
        return $data;
    }

}
