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
class SonaliStandardRepository extends EntityRepository
{
    public function getSonaliStandardByAge(){
        $qb = $this->createQueryBuilder('e');

        $qb->select('e.age', 'e.cumulativeFeedIntake', 'e.targetBodyWeight');
        $results = $qb->getQuery()->getArrayResult();
        $returnArray=[];
        if($results){
            foreach ($results as $result){
                $returnArray[$result['age']]=$result;
            }
        }
        return $returnArray;
    }


}
