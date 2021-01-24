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

//use Doctrine\ORM\EntityRepository;
use Terminalbd\CrmBundle\Repository\BaseRepository;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ChickLifeCycleRepository extends BaseRepository
{
    public function getChickLifeCycleByReportType($filterBy){

        $qb = $this->createQueryBuilder('e');
        $this->handleSearchFilterBetween($qb, $filterBy);

        $results = $qb->getQuery()->getResult();

//        dd($results);
        $data=[];
        foreach ($results as $result){
            $month = $result->getCreatedAt()->format('F-Y') ;

            $data[$month][]= $result;
            $data['officer_name'] = $result->getEmployee()->getName();
            $data['officer_region'] = $result->getEmployee()->getRegional()->getName();
            $data['life_cycle'] = $result->getReport()->getParent()->getName();
        }
        return $data;
    }

}
