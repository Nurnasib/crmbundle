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
class ChickLifeCycleRepository extends EntityRepository
{

    public function getChickLifeCycleByReportType($reportType){

        $query = $this->createQueryBuilder('chickLifeCycle')
            ->join('chickLifeCycle.report','r')
            ->andWhere('r.slug = :reportType')
            ->setParameter('reportType',$reportType);
        $results = $query->getQuery()->getResult();

        return $results;
    }

}
