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
use Terminalbd\KpiBundle\Entity\EmployeeBoard;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FishLifeCycleRepository extends EntityRepository
{

    public function getFishReportByReportingDateAndFeedType( $report, $customer, $employee)
    {
        if($report && $customer && $employee){
            $startDate = date('Y-m-d', strtotime("now"));
//            $endDate = date('Y-m-d', strtotime("now"));
            $query = $this->createQueryBuilder('f')
                ->where('f.reportingMonth = :startDate')
                ->andWhere('f.report = :report')
                ->andWhere('f.employee = :employee')
                ->andWhere('f.customer = :customer')
                ->setParameters(array('startDate'=>$startDate, 'report'=>$report, 'customer'=>$customer, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getNumberOfReportsForKpi($board)
    {
        /**
         * @var EmployeeBoard $board
         */
        $startDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-d');
        $endDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-t');

        $qb = $this->createQueryBuilder('e');


        $qb->where('e.employee = :employee')->setParameter('employee',$board->getEmployee());
        $qb->andWhere('e.reportingMonth >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.reportingMonth <= :endDate')->setParameter('endDate', $endDate);

        return count($qb->getQuery()->getArrayResult());
    }

}
