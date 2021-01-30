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
class CrmCustomerRepository extends EntityRepository
{

    public function getLocationWise(User $user,$pram)
    {

        $arrs = array();
        foreach ($user->getUpozila() as $location){
            $arrs[] = $location->getId();
        }
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.location','location');
        $qb->join('e.customerGroup','s');
        $qb->select('e.id as id','e.name as name','e.address as address','e.mobile as mobile');
        $qb->where('s.slug = :slug')->setParameter('slug',$pram);
        $qb->andWhere('location.id IN (:upozils)')->setParameter('upozils',$arrs);
        $result = $qb->getQuery()->getArrayResult();
        return $result;

    }

    public function getAgentWise(Agent $agent,$pram='farmer')
    {

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup','s');
        $qb->join('e.agent','a');
        $qb->join('e.location','l');
        $qb->select('e.id as id','e.name as name','e.address as address','e.mobile as mobile');
        $qb->addSelect('l.name as locationName');
        $qb->where('s.slug = :slug')->setParameter('slug',$pram);
        $qb->andWhere('a.id = :agent')->setParameter('agent',$agent);
        $results = $qb->getQuery()->getArrayResult();

        $returnArray = [];

        foreach ($results as $result){
            $returnArray[$result['locationName']][]= $result;
        }

        return $returnArray;

    }

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