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
use Terminalbd\KpiBundle\Entity\EmployeeBoard;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class CattleLifeCycleRepository extends EntityRepository
{
    public function getCattleLifeCycleByReportType($reportType){

        $query = $this->createQueryBuilder('chickLifeCycle')
            ->join('chickLifeCycle.report','r')
            ->andWhere('r.slug = :reportType')
            ->setParameter('reportType',$reportType);
        $results = $query->getQuery()->getResult();

        return $results;
    }


    public function getNumberOfReportsForKpi($board, $type)
    {
        /**
         * @var EmployeeBoard $board
         */
        $startDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-d');
        $endDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-t');

        $qb = $this->createQueryBuilder('e');


        $qb->where('e.employee = :employee')->setParameter('employee',$board->getEmployee());
        $qb->andWhere('e.reportingDate >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.reportingDate <= :endDate')->setParameter('endDate', $endDate);
        $qb->andWhere('e.lifeCycleState = :status')->setParameter('status', $type);
        return count($qb->getQuery()->getArrayResult());
    }

}
