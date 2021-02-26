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
class CrmVisitRepository extends EntityRepository
{
    public function findDailyReport($filterBy)
    {
//        dd($filterBy);
//        die();
        $employeeId = $filterBy['employeeId'];
        $startDate = $filterBy['startDate'];
        $endDate = $filterBy['endDate'];

        $qb = $this->createQueryBuilder('e');

        $qb->select('e.created', 'e.workingDuration','e.workingDurationTo');
        $qb->addSelect('employee.name AS employee_name', 'employee.id AS employee_id');
        $qb->addSelect('location.name AS location_name');
        $qb->addSelect('crm_visit_details.farmCapacity AS customer_farmCapacity','crm_visit_details.comments', 'crm_visit_details.process');
        $qb->addSelect('purpose.name AS purpose_name');
        $qb->addSelect('crm_customer.name AS customer_name', 'crm_customer.address AS customer_address');
        $qb->addSelect('agent.name AS agent_name', 'agent.address AS agent_address','agent.mobile AS agent_phone');

        $qb->leftJoin('e.employee', 'employee');
        $qb->leftJoin('e.location', 'location');
        $qb->leftJoin('e.crmVisitDetails', 'crm_visit_details');
        $qb->leftJoin('crm_visit_details.crmCustomer', 'crm_customer');
        $qb->leftJoin('crm_visit_details.purpose', 'purpose');
        $qb->leftJoin('crm_visit_details.agent', 'agent');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        $qb->andwhere('crm_visit_details.created >= :startDate')->setParameter('startDate', $startDate);
        $qb->andwhere('crm_visit_details.created <= :endDate')->setParameter('endDate', $endDate);

        return $qb->getQuery()->getArrayResult();
    }
}