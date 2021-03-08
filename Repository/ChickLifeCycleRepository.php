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

    public function getMonthlyBroilerLifeCycleTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select('COUNT(e) as totalReport');

        $qb->join('e.employee', 'employee');
        $qb->join('e.report', 'report');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.reportingDate >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.reportingDate <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);
        $qb->andWhere('report.slug = :slug')->setParameter('slug', 'boiler-life-cycle');

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }


}
