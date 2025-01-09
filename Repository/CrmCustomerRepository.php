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
        $rolesString = implode('_', $user->getRoles());

        $locationsId = array();
        foreach ($user->getUpozila() as $location){
            $locationsId[] = $location->getId();
        }
        $qb = $this->createQueryBuilder('e');

        $qb->leftJoin('e.location','location');
        $qb->join('e.customerGroup','s');
        $qb->leftJoin('e.agent','agent');
        $qb->join('e.farmerIntroduce','farmerIntroduce');
        $qb->leftJoin('farmerIntroduce.farmerType','farmerType');

        $qb->select('e.id as id','e.name as name','e.address as address','e.mobile as mobile', 'agent.name AS agentName', 'location.name AS locationName');
        $qb->addSelect('farmerType.name as farmerTypeName');

        $qb->where('s.slug = :slug')->setParameter('slug',$pram);
        if (!str_contains($rolesString, 'ADMIN')){
            $qb->andWhere('location.id IN (:upozilas)')->setParameter('upozilas',$locationsId);
        }
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        return $qb->getQuery()->getArrayResult();

    }

    public function getCustomerByLocationAndType( $filterBy, $customerType, User $user, $pram)
    {
//        print_r($filterBy);
        $rolesString = implode('_', $user->getRoles());

        $locationsId = array();
        foreach ($user->getUpozila() as $location){
            $locationsId[] = $location->getId();
        }
        $qb = $this->createQueryBuilder('e');

        $qb->leftJoin('e.location','location');
        $qb->join('e.customerGroup','s');
        $qb->leftJoin('e.agent','agent');

        $qb->leftJoin('agent.upozila','thana');
        $qb->leftJoin('thana.parent','district');

        $qb->join('e.farmerIntroduce','farmerIntroduce');
        $qb->leftJoin('farmerIntroduce.feed', 'feed');
        $qb->leftJoin('farmerIntroduce.farmerType','farmerType');

        $qb->select('e.id as id','e.name as name','e.address as address','e.mobile as mobile', 'agent.name AS agentName', 'agent.agentId as agentId','agent.address as agentLocation' , 'location.name AS locationName');
        $qb->addSelect('farmerType.name as farmerTypeName');
        $qb->addSelect('farmerIntroduce.cultureSpeciesItemAndQty');
        $qb->addSelect('feed.name as feedName');

        $qb->addSelect('thana.name as thanaName','district.name as districtName');

        $qb->where('s.slug = :slug')->setParameter('slug',$pram);
        $qb->andWhere('farmerType.slug =:farmerTypeSlug')->setParameter('farmerTypeSlug', $customerType.'-breed');
        if (!str_contains($rolesString, 'ADMIN')){
            $qb->andWhere('location.id IN (:upozilas)')->setParameter('upozilas',$locationsId);
        }
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');

        if(isset($filterBy['customerName']) && $filterBy['customerName'] != ""){
            $qb->andWhere(
                $qb->expr()->like('e.name', ':name')
            )->setParameter(':name', '%' . $filterBy['customerName'] . '%');
        }

        if(isset($filterBy['customerMobile']) && $filterBy['customerMobile'] != ""){
            $qb->andWhere(
                $qb->expr()->like('e.mobile', ':mobile')
            )->setParameter(':mobile', '%'.trim($filterBy['customerMobile']).'%');
        }

        if(isset($filterBy['customerAddress']) && $filterBy['customerAddress'] != ""){
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('e.address', ':address'),
                $qb->expr()->like('location.name', ':address')
            ))->setParameter(':address', trim($filterBy['customerAddress']) . '%');
        }

        if(isset($filterBy['feedCompany']) && $filterBy['feedCompany'] != ""){
            $qb->andWhere('feed.id = :feedId')->setParameter('feedId', $filterBy['feedCompany']->getId());
        }
        return $qb->getQuery()->getArrayResult();

    }

    public function getAgentWise(Agent $agent, User $user, $pram='farmer')
    {
        $serviceMode= $user->getServiceMode()->getSlug();
        $serviceModeExplode=explode('-', $serviceMode);
        $lastElement = end($serviceModeExplode);
//dd($lastElement);
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.farmerIntroduce','farmerIntroduce');
        $qb->join('farmerIntroduce.farmerType','farmerType');
        $qb->join('e.customerGroup','s');
        $qb->join('e.agent','a');
        $qb->join('a.upozila','l');
        $qb->select('e.id as id','e.name as name','e.address as address','e.mobile as mobile');
        $qb->addSelect('l.id as locationId', 'l.name as locationName');
        $qb->addSelect('a.name as agentName', 'a.agentId as agentId');
        $qb->addSelect('farmerIntroduce.cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug',$pram);
        $qb->andWhere('l.id = :locationId')->setParameter('locationId',$agent->getUpozila()->getId());
        $qb->andWhere('farmerType.slug = :farmerTypeSlug')->setParameter('farmerTypeSlug',$lastElement.'-breed');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        $results = $qb->getQuery()->getArrayResult();

        $returnArray = [];

        foreach ($results as $result){
            $returnArray[$result['locationName']][]= $result;
        }
        return $returnArray;

    }
    
    public function duplicateCustomerCheckByMobileAndType($mobile, $farmerType){
        if($mobile && $farmerType){

            $qb = $this->createQueryBuilder('e');
            $qb->join('e.farmerIntroduce','farmerIntroduce');
            $qb->join('farmerIntroduce.farmerType','farmerType');
            $qb->select('e.id as id','e.name as name','e.mobile as mobile');
            $qb->addSelect('farmerType.name as typeName');
            $qb->where('e.mobile = :mobile')->setParameter('mobile',$mobile);
            $qb->andWhere('farmerType.id = :farmerType')->setParameter('farmerType',$farmerType);

            $results = $qb->getQuery()->getResult();

            return $results;
        }
        return null;

    }

    public function getCustomerByChickAgent(){
        $sql ="SELECT c.id, c.name, c.agent_id, ca.mobile, ca.agentId as agentCode, ca.name as agentName, ca.upozila_id as agentUpozilaId, c.location_id as customerUpozilaId FROM `crm_customers` c 
JOIN crm_customer_introduce_details i on i.customer_id=c.id 
JOIN core_agent ca on ca.id=i.agent_id 
WHERE ca.agent_group_id=11 
HAVING ca.upozila_id=c.location_id
ORDER BY `c`.`agent_id` ASC";
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();

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