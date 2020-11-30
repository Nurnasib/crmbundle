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
use Doctrine\ORM\EntityRepository;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
use Terminalbd\CrmBundle\Entity\Setting;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class CrmVisitDetailsRepository extends EntityRepository
{

    public function insertDailyActivity(CrmVisit $crmVisit , $data)
    {
        $em = $this->_em;

        foreach ($data['agent'] as $key => $value):

            if(!empty($value) and !empty($data['agentPurpose'][$key])) {
                $purpose = $em->getRepository(Setting::class)->find($data['agentPurpose'][$key]);
                $visit = $this->findOneBy(
                    array('crmVisit' => $crmVisit, 'agent' => $value, 'purpose' => $purpose)
                );
                if (empty($visit)) {
                    $entity = new CrmVisitDetails();
                    $entity->setCrmVisit($crmVisit);
                    $entity->setProcess('agent');
                    $agent = $em->getRepository(Agent::class)->find($data['agent'][$key]);
                    $entity->setAgent($agent);
                    $entity->setPurpose($purpose);
                    $entity->setComments($data['agentComments'][$key]);
                    $em->persist($entity);
                    $em->flush();
                } else {
                    $visit->setComments($data['agentComments'][$key]);
                    $em->persist($visit);
                    $em->flush();
                }
            }

        endforeach;

       foreach ($data['otherAgent'] as $key => $value):
           if(!empty($value) and !empty($data['otherPurpose'][$key])) {
                $purpose = $em->getRepository(Setting::class)->find($data['otherPurpose'][$key]);
                $visit = $this->findOneBy(
                    array('crmVisit' => $crmVisit, 'crmCustomer' => $value , 'purpose' => $purpose)
                );
                if(empty($visit)){
                    $entity = new CrmVisitDetails();
                    $entity->setCrmVisit($crmVisit);
                    $entity->setProcess('other-agent');
                    $customer = $em->getRepository(CrmCustomer::class)->find($value);
                    $entity->setCrmCustomer($customer);
                    $entity->setPurpose($purpose);
                    $entity->setComments($data['otherComments'][$key]);
                    $em->persist($entity);
                    $em->flush();
                }else{
                    $visit->setComments($data['otherComments'][$key]);
                    $em->persist($visit);
                    $em->flush();
                }
            }
       endforeach;

        foreach ($data['subAgent'] as $key => $value):

            if(!empty($value) and !empty($data['subPurpose'][$key])) {
                $purpose = $em->getRepository(Setting::class)->find($data['subPurpose'][$key]);
                $visit = $this->findOneBy(
                    array('crmVisit' => $crmVisit, 'crmCustomer' => $value, 'purpose' => $purpose)
                );
                if (empty($visit)) {
                    $entity = new CrmVisitDetails();
                    $entity->setCrmVisit($crmVisit);
                    $entity->setProcess('sub-agent');
                    $customer = $em->getRepository(CrmCustomer::class)->find($value);
                    $entity->setCrmCustomer($customer);
                    $entity->setPurpose($purpose);
                    $entity->setComments($data['subComments'][$key]);
                    $em->persist($entity);
                    $em->flush();
                } else {
                    $visit->setComments($data['subComments'][$key]);
                    $em->persist($visit);
                    $em->flush();
                }
            }

        endforeach;
    }

    public function insertCrmVisitDetailForFarmer(CrmCustomer $customer , $id , $data)
    {
        $em = $this->_em;
        $visit = $em->getRepository(CrmVisit::class)->find($id);
        $entity = new CrmVisitDetails();
        $entity->setCrmCustomer($customer);
        $entity->setAgent($customer->getAgent());
        $entity->setCrmVisit($visit);
        $entity->setFarmCapacity($data['capacity']);
        $entity->setComments($data['comments']);
        $entity->setProcess('farmer');
        if($data['purpose']){
            $purpose = $em->getRepository(Setting::class)->find($data['purpose']);
            $entity->setPurpose($purpose);
        }
        if($data['farmer_firm_type']){
            $farmType = $em->getRepository(Setting::class)->find($data['farmer_firm_type']);
            $entity->setFirmType($farmType);
        }
        if($data['farmer_report']){
            $farmerReport = $em->getRepository(Setting::class)->find($data['farmer_report']);
            $entity->setReport($farmerReport);
        }
        $em->persist($entity);
        $em->flush();
    }

    public function insertOtherAgent(CrmCustomer $customer , $id , $data)
    {
        $em = $this->_em;
        $visit = $em->getRepository(CrmVisit::class)->find($id);
        $entity = new CrmVisitDetails();
        $entity->setCrmCustomer($customer);
        $entity->setCrmVisit($visit);
        if($data['purpose']){
            $purpose = $em->getRepository(Setting::class)->find($data['purpose']);
            $entity->setPurpose($purpose);
        }
        $entity->setComments($data['comments']);
        $entity->setProcess('other-agent');
        $em->persist($entity);
        $em->flush();
    }

    public function insertSubAgent(CrmCustomer $customer , $id , $data)
    {
        $em = $this->_em;
        $visit = $em->getRepository(CrmVisit::class)->find($id);
        $entity = new CrmVisitDetails();
        $entity->setCrmCustomer($customer);
        $entity->setCrmVisit($visit);
        $entity->setProcess('sub-agent');
        if($data['purpose']){
            $purpose = $em->getRepository(Setting::class)->find($data['purpose']);
            $entity->setPurpose($purpose);
        }
        $entity->setComments($data['comments']);
        $em->persist($entity);
        $em->flush();
    }

}