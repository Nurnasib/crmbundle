<?php

namespace Terminalbd\CrmBundle\Repository\NewFarmerIntroduce;

use App\Entity\Core\Agent;
use Doctrine\ORM\EntityRepository;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Repository\BaseRepository;

class FarmerIntroduceDetailsRepository extends BaseRepository
{
    public function insertCrmFarmerIntroduceDetails(CrmCustomer $customer, $user, $feed, $data)
    {
        $subAgent=null;


        if ($data['farmer_type']){
            $em = $this->_em;
            $entity = new FarmerIntroduceDetails();
            $entity->setCustomer($customer);
            $entity->setAgent($customer->getAgent());
            if($customer->getOtherAgent()){
                $entity->setOtherAgent($customer->getOtherAgent());
            }
            if(isset($data['sub_agent'])&&$data['sub_agent']!=''){
                $subAgent = $em->getRepository(Agent::class)->find($data['sub_agent']);
            }
            if($subAgent){
                $entity->setSubAgent($subAgent);
            }
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

    public function getFarmerIntroduceReportByEmployeeDate($report, $filterBy)
    {
        $returnArray = [];
        $breed= $report->getParent()->getParent();
        $species = $this->getEntityManager()->getRepository(Setting::class)->findBy(array('status'=>1,'settingType'=>'SPECIES_TYPE','parent'=>$breed));
//dd($species);
        if($report){
            $qb = $this->createQueryBuilder('e');
            $qb->select('e.id as eId', 'e.cultureSpeciesItemAndQty', 'e.remarks', 'e.createdAt', 'e.introduceDate');
            $qb->addSelect('farmer.name AS customerName', 'farmer.address AS customerAddress', 'farmer.mobile AS customerMobile');
            $qb->addSelect('agent.name AS agentName','agent.address AS agentAddress');
            $qb->addSelect('otherAgent.name AS otherAgentName','otherAgent.address AS otherAgentAddress');
            $qb->addSelect('subAgent.name AS subAgentName','subAgent.address AS subAgentAddress');
            $qb->addSelect('farmerType.name AS farmerTypeName');
            $qb->addSelect('feed.name AS feedName');
            $qb->addSelect('otherFeed.name AS otherFeedName');
            $qb->addSelect('employee.id AS employeeId', 'employee.name AS employeeName');
            $qb->addSelect('designation.name AS employeeDesignationName');


            $qb->join('e.customer', 'farmer');
            $qb->join('e.employee', 'employee');
            $qb->join('e.farmerType', 'farmerType');
            $qb->leftJoin('employee.designation', 'designation');
            $qb->leftJoin('e.agent', 'agent');
            $qb->leftJoin('e.otherAgent', 'otherAgent');
            $qb->leftJoin('e.subAgent', 'subAgent');
            $qb->leftJoin('e.feed', 'feed');
            $qb->leftJoin('e.otherFeed', 'otherFeed');

            $qb->where('e.farmerType =:farmerType')->setParameter('farmerType', $breed);

            $startDate = isset($filterBy['startDate'])&&$filterBy['startDate']!=''? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00': '';
            $endDate = isset($filterBy['endDate']) && $filterBy['endDate']!=''? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59': '';

            $employee = isset($filterBy['employeeId'])&&$filterBy['employeeId']!=''? $filterBy['employeeId']: '';
            if (!empty($employee)){
                $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee);
            }

            if (!empty($startDate) && !empty($endDate)){
                $qb->andWhere('e.introduceDate >= :startDate')->setParameter('startDate', $startDate);
                $qb->andWhere('e.introduceDate <= :endDate')->setParameter('endDate', $endDate);
            }
//            $qb->andWhere('e.introduceDate IS NOT NULL');
            $qb->andWhere('e.createdAt IS NOT NULL');
            $qb->orderBy('e.introduceDate','ASC');
            $results = $qb->getQuery()->getArrayResult();

            if($results){
                foreach ($results as $result){
                    $monthYear = $result['introduceDate']->format('F-Y');
                    $returnArray[$result['employeeId']]['species']=$species;
                    $returnArray[$result['employeeId']]['name']=$result['employeeName'];
                    $returnArray[$result['employeeId']]['employeeDesignationName']=$result['employeeDesignationName'];
                    $returnArray[$result['employeeId']]['details'][$monthYear][]=$result;
                }
            }
        }
//        dd($returnArray);

        return $returnArray;

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

    public function getFarmerSurveyReport($filterBy)
    {

        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00' : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59' : null;
        $employee = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.employee', 'employee');
        $qb->join('e.customer', 'farmer');
        $qb->join('e.agent', 'agent');
        $qb->leftJoin('e.feed', 'feed');
        $qb->leftJoin('e.otherFeed', 'other_feed');
        $qb->leftJoin('agent.district', 'agentDistrict');
        $qb->leftJoin('agent.upozila', 'agentUpozila');

        $qb->select('e.cultureSpeciesItemAndQty', 'e.remarks', 'e.createdAt');
        $qb->addSelect('farmer.id AS farmerId', 'farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('agent.agentId', 'agent.name AS agentName', 'agentDistrict.name AS agentDistrictName', 'agentUpozila.name AS agentUpozilaName');
        $qb->addSelect('other_feed.name AS otherFeedName');
        $qb->addSelect('feed.name AS feedName');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $employee);
        $qb->andWhere('e.createdAt >= :start')->setParameter('start', $start);
        $qb->andWhere('e.createdAt <= :end')->setParameter('end', $end);

        $results = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($results as $result) {
            $month = $result['createdAt']->format('Y-m-F');
            $data[$month][$result['agentId']]['agent'] = [
                'agentId' => $result['agentId'],
                'agentName' => $result['agentName'],
                'agentDistrictName' => $result['agentDistrictName'],
                'agentUpozilaName' => $result['agentUpozilaName'],
            ];
            $data[$month][$result['agentId']]['farmers'][$result['farmerId']] = $result;
        }
        ksort($data);
        return $data;
    }

}