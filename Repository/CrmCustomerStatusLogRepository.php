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
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class CrmCustomerStatusLogRepository extends EntityRepository
{
    /**
     * Get all the status logs of a customer
     *
     * @param int $customerId
     * @return array
     */
    public function getStatusLogsByCustomerId(int $customerId): array
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select('e.id')
            ->addSelect('c.name')
            ->addSelect('emp.name')
            ->join('e.customer','c')
            ->join('e.employee','emp')
            ->where('c.id = :customerId')
            ->setParameter('customerId', $customerId)
            ->orderBy('e.id', 'DESC');

        return $qb->getQuery()->getArrayResult();
    }

    // Uncomment if you need to implement broiler life cycle report

//    public function broilerLifeCycleReport()
//    {
//        $qb = $this->_em->createQueryBuilder();
//
//        $qb->from(ChickLifeCycle::class, 'chick_life_cycle')
//            ->select('chick_life_cycle')
//        ;
//
//        $result = $qb->getQuery()->getArrayResult();
//
//        return $result;
//    }
}