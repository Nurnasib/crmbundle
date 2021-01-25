<?php

namespace Terminalbd\CrmBundle\Repository\NewFarmerIntroduce;

use Doctrine\ORM\EntityRepository;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\Setting;

class FarmerIntroduceDetailsRepository extends EntityRepository
{


    public function insertCrmFarmerIntroduceDetails(CrmCustomer $customer, $user , $data)
    {
        if ($data['farmer_type']){
            $em = $this->_em;
            $entity = new FarmerIntroduceDetails();
            $entity->setCustomer($customer);
            $entity->setAgent($customer->getAgent());
            $entity->setCultureSpeciesItemAndQty(json_encode($data['species_type']));
            $entity->setPreviousAgentName($data['previous_agent_name']);
            $entity->setPreviousAgentAddress($data['previous_agent_address']);
            $entity->setPreviousFeedName($data['previous_feed_name']);

            $entity->setEmployee($user);

            $entity->setRemarks($data['comments']);

            if($data['farmer_type']){
                $farmerType = $em->getRepository(Setting::class)->find($data['farmer_type']);
                $entity->setFarmerType($farmerType);
            }
            $em->persist($entity);
            $em->flush();
        }

    }

}