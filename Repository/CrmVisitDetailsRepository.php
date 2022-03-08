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
        if ($data['purpose']&&$data['farmer_firm_type']&&$data['farmer_report']){
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

    }

    public function insertOtherAgent(Agent $agent , $id , $data)
    {
        $em = $this->_em;
        $visit = $em->getRepository(CrmVisit::class)->find($id);
        $entity = new CrmVisitDetails();
        $entity->setAgent($agent);
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

    public function insertSubAgent(Agent $agent , $id , $data)
    {
        $em = $this->_em;
        $visit = $em->getRepository(CrmVisit::class)->find($id);
        $entity = new CrmVisitDetails();
        $entity->setAgent($agent);
        $entity->setCrmVisit($visit);
        $entity->setProcess('sub-agent');
        $purpose = $em->getRepository(Setting::class)->find($data['purpose']);
        $entity->setPurpose($purpose);
        $entity->setComments($data['comments']);

        $em->persist($entity);
        $em->flush();
    }

    public function insertDataFromApi(array $data, $visitId)
    {
        $created = new \DateTime($data['created']);
        $customer = $this->getEntityManager()->getRepository(CrmCustomer::class)->find($data['customer_id']);
        $agent = $this->getEntityManager()->getRepository(Agent::class)->findOneBy(['agentId' => $data['agent_id'], 'status' => 1]);

        $purpose = $this->getEntityManager()->getRepository(Setting::class)->findOneBy(['id' => $data['purpose_id'], 'settingType' => 'PURPOSE', 'status' => 1]);
        $firmType = $this->getEntityManager()->getRepository(Setting::class)->find($data['firm_type_id']);
        $report = $this->getEntityManager()->getRepository(Setting::class)->findOneBy(['id' => $data['report_id'], 'settingType' => 'FARMER_REPORT', 'status' => 1]);

        if ($customer && $agent){
            $sql = "INSERT INTO `crm_visit_details`(`crm_visit_id`, `farmCapacity`, `updated`, `comments`, `created`, `customer_id`, `process`, `agent_id`, `purpose_id`, `firm_type_id`, `report_id`) VALUES (:visitId, :farmCapacity, :updated, :comments, :created, :customerId, :process, :agentId, :purposeId, :firmTypeId, :reportId)";
            $em = $this->_em;
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue('visitId', $visitId);
            $stmt->bindValue('farmCapacity', $data['farmCapacity']);
            $stmt->bindValue('updated', $data['updated']);
            $stmt->bindValue('comments', $data['comments']);
            $stmt->bindValue('created', $created->format('Y-m-d H:i:s'));
            $stmt->bindValue('customerId', $customer->getId());
            $stmt->bindValue('process', $data['process']);
            $stmt->bindValue('agentId', $agent->getId());
            $stmt->bindValue('purposeId', $purpose ? $purpose->getId() : null);
            $stmt->bindValue('firmTypeId', $firmType ? $firmType->getId() : null);
            $stmt->bindValue('reportId', $report ? $report->getId() : null);
            $stmt->execute();
        }
    }

    public function getVisitDetails($begin,$end)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmVisit', 'crmVisit');
        $qb->join('crmVisit.employee', 'employee');
        $qb->leftJoin('e.purpose', 'purpose');
        $qb->leftJoin('e.crmCustomer', 'farmer');
        $qb->leftJoin('e.agent', 'agent');
        $qb->leftJoin('agent.parent', 'nourishAgent');
        $qb->select('e.farmCapacity', 'e.process', 'e.comments', 'e.purposeMultiple');
        $qb->addSelect('farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('agent.name AS agentName', 'agent.address AS agentAddress', 'agent.mobile AS agentMobile');
        $qb->addSelect('employee.userId','employee.name AS employeeName');
        $qb->addSelect('crmVisit.created AS visitDate');
        $qb->addSelect('purpose.name AS purposeName');
        $qb->addSelect('nourishAgent.name AS nourishAgentName');
        $qb->where('crmVisit.created >=:begin')->setParameter('begin', $begin);
        $qb->andWhere('crmVisit.created <=:end')->setParameter('end', $end);

        $results = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($results as $result) {
            if ($result['process'] == 'farmer'){
                $data['farmer'][] = $result;
            }elseif ($result['process'] == 'agent'){
                $data['agent'][] = $result;
            }elseif ($result['process'] == 'other-agent'){
                $data['other-agent'][] = $result;
            }elseif ($result['process'] == 'sub-agent'){
                $data['sub-agent'][] = $result;
            }
            $data['employee'] = [
                'userId' => $result['userId'],
                'employeeName' => $result['employeeName'],
                'visitDate' => [
                    'begin' => $begin,
                    'end' => $end,
                    ]
            ];

        }
        return $data;
    }
}