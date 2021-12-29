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
class CattleLifeCycleDetailsRepository extends EntityRepository
{
    public function getCattleLifeCycleDetails($lifeCycleSlug, $filterBy)
    {
        $startDate = $filterBy['startDate'] ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
        $endDate = $filterBy['endDate'] ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmCattleLifeCycle', 'crm_cattle_life_cycle');
        $qb->join('crm_cattle_life_cycle.report', 'report');
        $qb->join('crm_cattle_life_cycle.customer', 'customer');
        $qb->join('crm_cattle_life_cycle.employee', 'employee');
        $qb->leftJoin('crm_cattle_life_cycle.feedType', 'feed_type');

        $qb->select('e AS details');
        $qb->addSelect('customer.id AS customerId', 'customer.name AS customerName', 'customer.address AS customerAddress', 'customer.mobile AS customerMobile');
        $qb->addSelect('crm_cattle_life_cycle.lifeCycleState AS reportStatus');
        $qb->addSelect('feed_type.name AS feedName');
        $qb->addSelect('report.name AS reportName');

        $qb->where('e.visitingDate >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.visitingDate <= :endDate')->setParameter('endDate', $endDate);
        $qb->andWhere('report.slug = :reportSlug')->setParameter('reportSlug', $lifeCycleSlug);
        $qb->andWhere('crm_cattle_life_cycle.lifeCycleState = :reportStatus')->setParameter('reportStatus', $filterBy['reportStatus']);
        $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
/*        if ($filterBy['farmerId']){
            $qb->andWhere('customer.id = :farmerId')->setParameter('farmerId', $filterBy['farmerId']);
        }*/


        $results = $qb->getQuery()->getArrayResult();
        $data = [];

        foreach ($results as $result) {
            $result['details']['feedName'] = $result['feedName'];
            $data[$result['customerId']]['details'][] = $result['details'];
            $data[$result['customerId']]['parent'] = [
                'customerName' => $result['customerName'],
                'customerAddress' => $result['customerAddress'],
                'customerMobile' => $result['customerMobile'],
                'reportStatus' => $result['reportStatus'],
                'reportName' => $result['reportName'],
            ];
        }
        return $data;
    }

}
