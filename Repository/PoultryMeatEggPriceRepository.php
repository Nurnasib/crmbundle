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
class PoultryMeatEggPriceRepository extends EntityRepository
{
    public function processPrice($regions, $breedTypes, $visitId)
    {
        $exist = $this->findBy(['crmVisit' => $visitId]);
        if (empty($exist)){
            foreach ($breedTypes as $type){
                $breedTypeId=$type->getId();

                foreach ($regions as $region ){
                    $regionId = $region->getId();
                        $sql ="INSERT INTO 
                                crm_poultry_meat_egg_price (`crm_visit_id`, `region_id`,`breed_type_id`,`created_at`,`price`,`status`) 
                                VALUE ($visitId , $regionId , $breedTypeId , now() , 0, 1)";
                        $qb = $this->_em->getConnection()->prepare($sql);
                        $qb->execute();
                }
            }
        }


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmVisit','crmVisit');
        $qb->join('e.region','region');
        $qb->join('e.breedType','breedType');
        $qb->select('e.price','e.id AS recordId');
        $qb->addSelect('crmVisit.id AS visitId');
        $qb->addSelect('region.id AS regionId');
        $qb->addSelect('breedType.id AS breedTypeId');
        $qb->where('crmVisit.id = :crmVisitId')->setParameter('crmVisitId', $visitId);
        $results = $qb->getQuery()->getArrayResult();
        $array = [];
        if($results){
            foreach ($results as $result):
                $key = "{$result['regionId']}-{$result['breedTypeId']}";
                $array[$key] = $result;
            endforeach;
        }
        return $array;
    }

}
