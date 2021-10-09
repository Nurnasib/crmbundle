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
use Doctrine\ORM\Query\Expr\Join;
use Terminalbd\CrmBundle\Repository\BaseRepository;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ApiDetailsRepository extends BaseRepository
{
    public function getReportsList()
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.batch', 'batch');
        $qb->select('e.id','e.process', 'e.status', 'batch.batchNo');
        $qb->where('e.status = 0');
//        $qb->andWhere('e.process NOT IN (:process)')->setParameter('process', ['crm_visit', 'crm_visit_details']);

        return $qb->getQuery()->getArrayResult();
    }
}
