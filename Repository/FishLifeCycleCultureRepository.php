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

use App\Entity\User;
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
class FishLifeCycleCultureRepository extends EntityRepository
{
    
    public function getFishLifeCycleCulture($lifeCycleSlug, $filterBy, User $loggedUser){
        $startDate = $filterBy['startDate'] ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : date('Y-m-01');
        $endDate = $filterBy['endDate'] ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : date('Y-m-t');
//dd($startDate);
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.fishLifeCycleCultureDetails','details');
        $qb->leftJoin('e.feed', 'feed');
        $qb->leftJoin('e.hatchery', 'hatchery');
        $qb->join('e.employee', 'employee');
        $qb->join('e.report', 'report');
        $qb->join('e.customer', 'customer');
        $qb->leftJoin('e.agent', 'agent');
        $qb->leftJoin('e.mainCultureSpecies', 'mainCultureSpecies');
        $qb->leftJoin('e.otherCultureSpecies', 'otherCultureSpecies');
        $qb->leftJoin('e.feedType', 'feedType');

        $qb->select('e');
        $qb->addSelect('details as detail');
        $qb->addSelect('employee');
        $qb->addSelect('customer');
        $qb->addSelect('agent');
        $qb->addSelect('feed');
        $qb->addSelect('hatchery');
        $qb->addSelect('feedType');
        $qb->addSelect('mainCultureSpecies');
        $qb->addSelect('otherCultureSpecies');
        $qb->addSelect('report');


        $results = $qb->getQuery()->getArrayResult();
        dd($results);
    }


}
