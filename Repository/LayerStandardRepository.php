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
class LayerStandardRepository extends EntityRepository
{
    public function getLayerStandardByWeek($breed){
        $arrayResult = array();
        $query = $this->createQueryBuilder('ls')
            ->andWhere('ls.breed = :breed')
            ->setParameter('breed',$breed)
            ->orderBy('ls.age','asc');

        $results = $query->getQuery()->getArrayResult();
        if($results){
            foreach ($results as $result){
                $arrayResult[$result['age']] = $result;
            }
        }

        return $arrayResult;
    }

    public function getLayerStandardByBreedAndWeek($breed,$age){

        $query = $this->createQueryBuilder('ls')
            ->andWhere('ls.breed = :breed')
            ->setParameter('breed',$breed)
            ->andWhere('ls.age = :age')
            ->setParameter('age',$age);

        $results = $query->getQuery()->getArrayResult();

        return $results;
    }

}
