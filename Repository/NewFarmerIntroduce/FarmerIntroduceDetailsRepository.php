<?php

namespace Terminalbd\CrmBundle\Repository\NewFarmerIntroduce;

use Doctrine\ORM\EntityRepository;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Repository\BaseRepository;

class FarmerIntroduceDetailsRepository extends BaseRepository
{
    public function insertCrmFarmerIntroduceDetails(CrmCustomer $customer, $user, $feed, $data)
    {
        if ($data['farmer_type']){
            $em = $this->_em;
            $entity = new FarmerIntroduceDetails();
            $entity->setCustomer($customer);
            $entity->setAgent($customer->getAgent());
            $entity->setOtherAgent($customer->getOtherAgent());
            $entity->setFeed($feed?$feed:null);
            $entity->setOtherFeed($feed?$feed:null);
            $entity->setCultureSpeciesItemAndQty(json_encode($data['species_type']));
            /*$entity->setPreviousAgentName($data['previous_agent_name']);
            $entity->setPreviousAgentAddress($data['previous_agent_address']);
            $entity->setPreviousFeedName($data['previous_feed_name']);*/

            $entity->setEmployee($user);

            if($data['farmer_type']){
                $farmerType = $em->getRepository(Setting::class)->find($data['farmer_type']);
                $entity->setFarmerType($farmerType);
            }
            $em->persist($entity);
            $em->flush();
        }

    }

    public function getFarmerIntroduceReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.previousAgentName', 'e.previousAgentAddress', 'e.previousFeedName','e.cultureSpeciesItemAndQty', 'e.remarks', 'e.createdAt');
        $qb->addSelect('farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('agent.name AS agentName','agent.address AS agentAddress');
        $qb->where('e.createdAt >= :bOfYear')->setParameter('bOfYear', $filterBy['bOfYear']);
        $qb->andWhere('e.createdAt <= :eOfYear')->setParameter('eOfYear', $filterBy['eOfYear']);
        $qb->andWhere('farmerType.slug = :breedType')->setParameter('breedType', $filterBy['breedType']);
        $qb->leftJoin('e.customer', 'farmer');
        $qb->leftJoin('farmer.agent', 'agent');
        $qb->leftJoin('e.farmerType', 'farmerType');
        $this->handleSearchFilterBetween($qb, $filterBy);
        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result){
            $month = $result['createdAt']->format('F-Y');

            $data[$month][] = $result;
        }

        return $data;

    }

    public function getMonthlyNewFarmerIntroduceTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');

        $qb->join('e.employee', 'employee');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.createdAt >= :monthStart')->setParameter('monthStart', $filterBy['monthStart'] . ' 00:00:00');
        $qb->andWhere('e.createdAt <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd'] . ' 23:59:59');

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }

}