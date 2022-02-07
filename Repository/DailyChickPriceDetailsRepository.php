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
use Doctrine\ORM\NonUniqueResultException;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class DailyChickPriceDetailsRepository extends EntityRepository
{
    public function getDocPriceReport($filterBy, $loggedUser)
    {

        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;
        $employeeId = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmDailyChickPrice', 'parent');
        $qb->join('parent.employee', 'employee');
        $qb->join('employee.userGroup', 'user_group');
        $qb->join('e.chickType', 'chick_type');
        $qb->join('chick_type.parent', 'chick_type_parent');
        $qb->join('e.feed', 'feed');

        $qb->select('employee.id','employee.userId', 'employee.name');
        $qb->addSelect('AVG(e.price) AS avgPrice');
        $qb->addSelect('chick_type_parent.id AS chickTypeParentId', 'chick_type_parent.name AS chickTypeParentName');
        $qb->addSelect('feed.id AS feedId', 'feed.name AS feedName');
        $qb->addSelect('parent.reportingDate', 'MONTH(parent.reportingDate) AS month', 'YEAR(parent.reportingDate) AS year');

        $qb->where('parent.reportingDate >= :start')->setParameter('start', $start);
        $qb->andWhere('parent.reportingDate <= :end')->setParameter('end', $end);
        $qb->andWhere('user_group.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');

        $qb->groupBy('employee.userId');
        $qb->addGroupBy('month');
        $qb->addGroupBy('year');
        $qb->addGroupBy('chickTypeParentName');
        $qb->addGroupBy('feedName');
        $qb->orderBy('feed.name', 'ASC');

        $roleSplitArray = [];
        foreach ($loggedUser->getRoles() as $role) {
            $roleSplitArray = array_merge(explode('_', $role), $roleSplitArray);
        }
        if (!in_array('ADMIN', $roleSplitArray) && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }
        if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $month = $result['reportingDate']->format('m-F');
            $data['Year-' . $result['reportingDate']->format('Y')][$result['chickTypeParentName']][$result['userId'] . '~' . $result['name']][$result['feedName']][$month] = $result['avgPrice'];

            ksort($data['Year-' . $result['reportingDate']->format('Y')][$result['chickTypeParentName']][$result['userId'] . '~' . $result['name']][$result['feedName']]);
        }
        return $data;

    }

}





























