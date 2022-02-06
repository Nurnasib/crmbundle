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
class LayerLifeCycleRepository extends BaseRepository
{

    public function getMonthlyLayerLifeCycleTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select('COUNT(e) as totalReport');

        $qb->join('e.employee', 'employee');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.lifeCycleState = :lifecycleState')->setParameter('lifecycleState', 'IN_PROGRESS');
        $qb->andWhere('e.created >= :monthStart')->setParameter('monthStart', $filterBy['monthStart'] . ' 00:00:00');
        $qb->andWhere('e.created <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd'] . ' 23:59:59');

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }


}
