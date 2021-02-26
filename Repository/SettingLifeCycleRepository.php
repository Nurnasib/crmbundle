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
class SettingLifeCycleRepository extends EntityRepository
{

    public function getLifeCycleWeekByLifeCycle($slug){
        $return = array();
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.report','r');
        $qb->select('e.numberOfWeek');
        $qb->where('r.slug = :slug')->setParameter('slug',$slug);
        $result = $qb->getQuery()->getArrayResult();
        for($i=1; $i<=$result[0]['numberOfWeek'];$i++){
            $return[$i]= $i.' week';
        }
        return $return;
    }


}
