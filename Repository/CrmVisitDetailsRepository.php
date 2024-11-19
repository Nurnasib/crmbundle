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

    public function getVisitDetails($begin,$end,$employeeId,$serviceModeId, $process)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmVisit', 'crmVisit');
        $qb->join('crmVisit.employee', 'employee');
        $qb->join('employee.serviceMode', 'serviceMode');
        $qb->leftJoin('e.purpose', 'purpose');
        $qb->leftJoin('e.crmCustomer', 'farmer');
        $qb->leftJoin('e.agent', 'agent');
        $qb->leftJoin('agent.parent', 'nourishAgent');
        $qb->leftJoin('crmVisit.area', 'area');
        $qb->leftJoin('crmVisit.location', 'location');
        $qb->select('e.farmCapacity', 'e.process', 'e.comments', 'e.purposeMultiple', 'e.reportDesc');
        $qb->addSelect('farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('agent.name AS agentName','agent.agentId', 'agent.address AS agentAddress', 'agent.mobile AS agentMobile');
        $qb->addSelect('employee.userId','employee.name AS employeeName');
        $qb->addSelect('crmVisit.created AS visitCreatedDate', 'crmVisit.visitDate', 'crmVisit.visitTime');
        $qb->addSelect('purpose.name AS purposeName');
        $qb->addSelect('area.name AS areaName');
        $qb->addSelect('location.name AS vistingAreaName');
        $qb->addSelect('nourishAgent.name AS nourishAgentName');
        $qb->where('crmVisit.created >=:begin')->setParameter('begin', $begin);
        $qb->andWhere('crmVisit.created <=:end')->setParameter('end', $end);
        if($employeeId){
            $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);
        }else{
            if($serviceModeId){
                $qb->andWhere('serviceMode.id =:serviceModeId')->setParameter('serviceModeId', $serviceModeId);
            }
        }

        if($process){
            $qb->andWhere('e.process = :process')->setParameter('process', $process);
        }


        $qb->orderBy('crmVisit.created', 'ASC');

        $results = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($results as $result) {
            if ($result['process'] == 'farmer'){
                $data[$result['userId']]['farmer'][$result['visitCreatedDate']->format('d-m-Y')][] = $result;
            }elseif ($result['process'] == 'agent'){
                $data[$result['userId']]['agent'][$result['visitCreatedDate']->format('d-m-Y')][] = $result;
            }elseif ($result['process'] == 'other-agent'){
                $data[$result['userId']]['other-agent'][$result['visitCreatedDate']->format('d-m-Y')][] = $result;
            }elseif ($result['process'] == 'sub-agent'){
                $data[$result['userId']]['sub-agent'][$result['visitCreatedDate']->format('d-m-Y')][] = $result;
            }
            $data[$result['userId']]['employeeName']= $result['employeeName'];
            $data['visitCreatedDate']= ['begin' => $begin,'end' => $end];

        }
        return $data;
    }
    public function getAgentVisitMonitors($begin,$end,$employeeId)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmVisit', 'crmVisit');
        $qb->join('crmVisit.employee', 'employee');
        $qb->leftJoin('employee.designation', 'employeeDesignation');
        $qb->join('e.agent', 'agent');
        $qb->leftJoin('agent.agentGroup', 'agentGroup');
        $qb->leftJoin('agent.district', 'agentDistrict');
        $qb->leftJoin('agent.parent', 'nourishAgent');
        $qb->select('e.farmCapacity', 'e.process', 'e.comments', 'e.purposeMultiple', 'e.reportDesc');
        $qb->addSelect('agent.id AS agentAutoId','agent.name AS agentName','agent.agentId', 'agent.address AS agentAddress', 'agent.mobile AS agentMobile');
        $qb->addSelect('agentGroup.name AS agentGroupName', 'agentGroup.slug AS agentGroupSlug');
        $qb->addSelect('employee.id as employeeAutoId','employee.userId','employee.name AS employeeName');
        $qb->addSelect('crmVisit.created AS visitCreatedDate', 'crmVisit.visitDate', 'crmVisit.visitTime');
        $qb->addSelect('nourishAgent.name AS nourishAgentName');
        $qb->addSelect('agentDistrict.name AS agentDistrictName');
        $qb->addSelect('employeeDesignation.name AS employeeDesignationName');
        $qb->where('crmVisit.created >=:begin')->setParameter('begin', $begin);
        $qb->andWhere('crmVisit.created <=:end')->setParameter('end', $end);

        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);

        $qb->orderBy('crmVisit.created', 'ASC');

        $results = $qb->getQuery()->getArrayResult();

        $data = [];
        if($results){
            foreach ($results as $result) {
                $data['employeeInfo'][$result['employeeAutoId']]= [
                    'employeeName'=> $result['employeeName'],
                    'userId'=> $result['userId'],
                    'designationName'=> $result['employeeDesignationName']
                ];
                $data['agentInfo'][$result['agentAutoId']]= [
                    'agentName'=> $result['agentName'],
                    'agentId'=> $result['agentGroupSlug']=='other-agent' || $result['agentGroupSlug']=='sub-agent' ? $result['agentGroupName'] : $result['agentId'],
                    'agentAddress'=> $result['agentAddress'],
                    'agentMobile'=> $result['agentMobile'],
                    'nourishAgentName'=> $result['nourishAgentName'],
                    'agentDistrictName'=> $result['agentDistrictName']
                ];
                $data['records'][$result['agentAutoId']][$result['visitCreatedDate']->format('d-m-Y')] = $result['visitCreatedDate']->format('d-m-Y');
                $data['visitCreatedDate']= ['begin' => $begin,'end' => $end];

            }
        }

        return $data;
    }


    public function getVisitDetailsSummery($begin, $end, $employeeIds)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmVisit', 'crmVisit');
        $qb->join('crmVisit.employee', 'employee');
        $qb->leftJoin('e.crmCustomer', 'farmer');
        $qb->leftJoin('e.agent', 'agent');
        $qb->leftJoin('agent.agentGroup', 'agentGroup');
//        $qb->leftJoin('agent.parent', 'nourishAgent');
        $qb->select('e.id', 'e.process');
        $qb->addSelect('farmer.id AS farmerId','farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('agent.id AS agentAutoId','agent.name AS agentName','agent.agentId', 'agent.address AS agentAddress', 'agent.mobile AS agentMobile');
        $qb->addSelect('agentGroup.name AS agentGroupName', 'agentGroup.slug AS agentGroupSlug');
        $qb->addSelect('employee.id AS employeeAutoId','employee.userId','employee.name AS employeeName');
        $qb->addSelect('crmVisit.created AS visitCreatedDate', 'crmVisit.visitDate', 'crmVisit.visitTime');
//        $qb->addSelect('nourishAgent.name AS nourishAgentName');
        $qb->where('crmVisit.created >=:begin')->setParameter('begin', $begin);
        $qb->andWhere('crmVisit.created <=:end')->setParameter('end', $end);
        if($employeeIds){
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);
        }
        $results = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($results as $result) {
            if ($result['farmerId']){
                $data[$result['employeeAutoId']]['farmer'][$result['visitCreatedDate']->format('F')][$result['farmerId']] = $result;
            }
            if ($result['agentAutoId'] && ( $result['agentGroupSlug'] == 'feed' || $result['agentGroupSlug'] == 'chick')){
                $data[$result['employeeAutoId']]['agent'][$result['visitCreatedDate']->format('F')][$result['agentAutoId']] = $result;
            }elseif ($result['agentAutoId'] && $result['agentGroupSlug'] == 'other-agent'){
                $data[$result['employeeAutoId']]['other-agent'][$result['visitCreatedDate']->format('F')][$result['agentAutoId']] = $result;
            }elseif ($result['agentAutoId'] && $result['agentGroupSlug'] == 'sub-agent'){
                $data[$result['employeeAutoId']]['sub-agent'][$result['visitCreatedDate']->format('F')][$result['agentAutoId']] = $result;
            }
        }
        return $data;
    }
}