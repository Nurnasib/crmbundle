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
    public function getProcessingItemLength($batch){

        $qb = $this->createQueryBuilder('e');
        
        $qb->select('COUNT(e.id) as totalNumberOfItem');
        
        $qb->join('e.batch', 'batch');
        
        $qb->where('batch.id = :batchId')->setParameter('batchId', $batch->getId());
        $qb->andWhere('e.status = :status')->setParameter('status', 1);
        
        return $qb->getQuery()->getOneOrNullResult();
    }

}
