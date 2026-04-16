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
use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\DBAL\Types\Types;

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
        $qb->leftJoin('agent.agentGroup','agentGroup');

        $qb->leftJoin('agent.upozila','thana');
        $qb->leftJoin('thana.parent','district');

        $qb->join('e.farmerIntroduce','farmerIntroduce');
        $qb->leftJoin('farmerIntroduce.feed', 'feed');
        $qb->leftJoin('farmerIntroduce.farmerType','farmerType');

        $qb->select('e.id as id','e.name as name','e.address as address','e.mobile as mobile', 'location.name AS locationName', 'e.status');
        $qb->addSelect('farmerType.name as farmerTypeName');
        $qb->addSelect('farmerIntroduce.cultureSpeciesItemAndQty');
        $qb->addSelect('feed.name as feedName');
        $qb->addSelect( 'agent.name AS agentName', 'agent.agentId as agentId','agent.address as agentLocation', 'agent.otherAndSubAgentId' );
        $qb->addSelect('agentGroup.name AS agentGroupName', 'agentGroup.slug AS agentGroupSlug' );

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

        if(isset($filterBy['customerId']) && $filterBy['customerId'] != ""){
            //	F-000082 how to trim F- and left 0 from the string
            $customerIf = str_replace('F-', '', strtoupper($filterBy['customerId']));
            $customerIf = ltrim($customerIf, '0');

            $qb->andWhere('e.id=:customerId')
               ->setParameter('customerId', (int)$customerIf);
        }

        if (isset($filterBy['status']) && $filterBy['status'] != ""){
            $qb->andWhere('e.status = :status')->setParameter('status', $filterBy['status']);
        }

        $results = $qb->getQuery()->getArrayResult();
        //

        return $results;

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

    public function getCustomerFarmVisitInfoByEmployeeIds( $employeeIds, $filterBy )
    {

        $startDate = !empty($filterBy['startDate'])
            ? \DateTime::createFromFormat('!d-m-Y', $filterBy['startDate'])
            : new \DateTime(date('Y-m-01'));

        $endDate = !empty($filterBy['endDate'])
            ? \DateTime::createFromFormat('!d-m-Y', $filterBy['endDate'])
            : new \DateTime(date('Y-m-t'));
        $typeId = isset($filterBy['type']) ? $filterBy['type']->getId() : null;


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup','s');
        $qb->join('e.farmerIntroduce','farmerIntroduce');
        $qb->join('e.crmVisitDetails','visit_details');
        $qb->join('visit_details.crmVisit','visit');
        $qb->join('farmerIntroduce.employee','employee');
        $qb->join('farmerIntroduce.farmerType','farmerType');

        $qb->select('e.id as id','e.name as name','e.address as address','e.mobile as mobile','e.status as status' ,'e.created as month' );

        $qb->addSelect('farmerIntroduce.cultureSpeciesItemAndQty');
        $qb->addSelect('employee.id as employeeId', 'employee.name as employeeName', 'employee.userId as employeeUserId');
        $qb->addSelect('farmerType.name as farmerTypeName', 'farmerType.slug as farmerTypeSlug');
        $qb->addSelect('visit_details.farmCapacity as farmCapacity', 'visit_details.comments as comments', 'visit.visitDate as visit_date');

        $qb->where('s.slug = :slug')->setParameter('slug','farmer');
        if (isset($filterBy['status'])){
            $qb->join('e.statusLog','slog');
            $qb->addSelect('slog.reason as reason');
            $qb->andWhere('e.status IN (:statuses)')
                ->setParameter('statuses', ['closed', 'close']);
        }

        if (isset($typeId)){
            $qb->andWhere('farmerIntroduce.cultureSpeciesItemAndQty LIKE :type')->setParameter('type', '%' . $typeId . '%');
        }

        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);

//        dd($startDate, $endDate);
        $qb->andWhere('visit.visitDate >= :startDate')
            ->andWhere('visit.visitDate <= :endDate')
            ->setParameter('startDate', $startDate, Types::DATE_MUTABLE)
            ->setParameter('endDate', $endDate, Types::DATE_MUTABLE);
        $results = $qb->getQuery()->getArrayResult();
//        dd($results);

        //group by employee
        $returnArray = [];
//        foreach ($results as $result) {
//            $empId = (int)$result['employeeId'];
//            $cultureSpeciesItemAndQty = [];
//            if (isset($result['cultureSpeciesItemAndQty']) && $result['cultureSpeciesItemAndQty'] && $result['cultureSpeciesItemAndQty'] != null) {
//                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);
//                if (is_array($cultureSpeciesItemAndQty)) {
//                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
//                        $cultureSpeciesItemAndQty = [$typeId => $cultureSpeciesItemAndQty[$typeId]];
//                    }
//                    $cultureSpeciesItemAndQty = array_filter($cultureSpeciesItemAndQty, function ($value) {
//                        return $value !== null && $value !== '';
//                    });
//                } else {
//                    $cultureSpeciesItemAndQty = [];
//                }
//                $arrayValues = array_values($cultureSpeciesItemAndQty);
//                $numericValues = array_map('intval', $arrayValues);
//                $result['cultureSpeciesItemAndQtySum'] = sizeof($numericValues) > 0 ? array_sum($numericValues) : 0;
//            } else {
//                $result['cultureSpeciesItemAndQtySum'] = 0;
//            }
//
//            $result['decodedCultureSpeciesItemAndQty'] = $cultureSpeciesItemAndQty;
//            $returnArray[$empId][] = $result;
//        }
        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $farmId = (int)$result['id'];
            $farmCap = $result['farmCapacity'];
            $farmName= $result['name'];
            $address= $result['address'];
            $employeeName= $result['employeeName'];
            $employeeUserId= $result['employeeUserId'];
            $day = $result['visit_date']->format('d');
            $day = (int)$day;

            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
                        $returnArray[$empId][$farmId]['decodedCultureSpeciesItemAndQty'] = $cultureSpeciesItemAndQty;
                        $returnArray[$empId][$farmId]['employeeName'] = $employeeName;
                        $returnArray[$empId][$farmId]['employeeUserId'] = $employeeUserId;
                        $returnArray[$empId][$farmId]['area'] = $address;
                        $returnArray[$empId][$farmId]['name'] = $farmName;
                        $returnArray[$empId][$farmId]['capacity'] = $farmCap;
                        $returnArray[$empId][$farmId][$day] = 'yes';
                    }else{
                        $returnArray[$empId][$farmId]['decodedCultureSpeciesItemAndQty'] = $cultureSpeciesItemAndQty;
                        $returnArray[$empId][$farmId]['employeeName'] = $employeeName;
                        $returnArray[$empId][$farmId]['employeeUserId'] = $employeeUserId;
                        $returnArray[$empId][$farmId]['area'] = $address;
                        $returnArray[$empId][$farmId]['name'] = $farmName;
                        $returnArray[$empId][$farmId]['capacity'] = $farmCap;
                        $returnArray[$empId][$farmId][$day] = 'yes';
                    }
                }
            }
        }
//        dd($returnArray[41]);
        return    $returnArray;

    }

    public function getCustomerByEmployeeIds( $employeeIds, $filterBy )
    {

        $startDate = isset($filterBy['startDate']) ? date('Y-m-d', strtotime($filterBy['startDate'])) : date('Y-m-01');
        $endDate = isset($filterBy['endDate']) ? date('Y-m-d', strtotime($filterBy['endDate'])) : date('Y-m-t');
        $typeId = isset($filterBy['type']) ? $filterBy['type']->getId() : null;


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup','s');
        $qb->join('e.farmerIntroduce','farmerIntroduce');
        $qb->join('farmerIntroduce.employee','employee');
        $qb->join('farmerIntroduce.farmerType','farmerType');

        $qb->select('e.id as id','e.name as name','e.address as address','e.mobile as mobile','e.status as status' ,'e.created as month' );

        $qb->addSelect('farmerIntroduce.cultureSpeciesItemAndQty');
        $qb->addSelect('employee.id as employeeId', 'employee.name as employeeName', 'employee.userId as employeeUserId');
        $qb->addSelect('farmerType.name as farmerTypeName', 'farmerType.slug as farmerTypeSlug');

        $qb->where('s.slug = :slug')->setParameter('slug','farmer');
        if (isset($filterBy['status'])){
            $qb->join('e.statusLog','slog');
            $qb->addSelect('slog.reason as reason');
            $qb->andWhere('e.status IN (:statuses)')
                ->setParameter('statuses', ['closed', 'close']);
        }

        if (isset($typeId)){
            $qb->andWhere('farmerIntroduce.cultureSpeciesItemAndQty LIKE :type')->setParameter('type', '%' . $typeId . '%');
        }
        
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);

        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate .' 00:00:00')
            ->setParameter('endDate', $endDate .' 23:59:59');
        
        $results = $qb->getQuery()->getArrayResult();
//        dd($results);
        
        //group by employee
        $returnArray = [];
        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $cultureSpeciesItemAndQty = [];
            if (isset($result['cultureSpeciesItemAndQty']) && $result['cultureSpeciesItemAndQty'] && $result['cultureSpeciesItemAndQty'] != null) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);
                if (is_array($cultureSpeciesItemAndQty)) {
                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
                        $cultureSpeciesItemAndQty = [$typeId => $cultureSpeciesItemAndQty[$typeId]];
                    }
                    $cultureSpeciesItemAndQty = array_filter($cultureSpeciesItemAndQty, function ($value) {
                        return $value !== null && $value !== '';
                    });
                } else {
                    $cultureSpeciesItemAndQty = [];
                }
                $arrayValues = array_values($cultureSpeciesItemAndQty);
                $numericValues = array_map('intval', $arrayValues);
                $result['cultureSpeciesItemAndQtySum'] = sizeof($numericValues) > 0 ? array_sum($numericValues) : 0;
            } else {
                $result['cultureSpeciesItemAndQtySum'] = 0;
            }

            $result['decodedCultureSpeciesItemAndQty'] = $cultureSpeciesItemAndQty;

            $returnArray[$empId][] = $result;
        }
//        dd($returnArray);
        return    $returnArray;

    }

    public function getCustomerByIntroduceByIds( $employeeIds, $filterBy )
    {
        $startDate = isset($filterBy['startDate']) ? date('Y-m-d', strtotime($filterBy['startDate'])) : date('Y-m-01');
        $endDate = isset($filterBy['endDate']) ? date('Y-m-d', strtotime($filterBy['endDate'])) : date('Y-m-t');

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup','s');
        $qb->join('e.farmerIntroduce','farmerIntroduce');
        $qb->join('farmerIntroduce.introduceBy','employee');
        $qb->join('farmerIntroduce.farmerType','farmerType');

        $qb->select('e.id as id','e.name as name','e.address as address','e.mobile as mobile' );

        $qb->addSelect('employee.id as employeeId', 'employee.name as employeeName', 'employee.userId as employeeUserId');
        $qb->addSelect('farmerType.name as farmerTypeName', 'farmerType.slug as farmerTypeSlug');

        $qb->where('s.slug = :slug')->setParameter('slug','farmer');

        if (isset($filterBy['type'])){
            $qb->andWhere('farmerType.slug = :type')->setParameter('type',$filterBy['type']);
        }

        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');

        $qb->andWhere('farmerIntroduce.introduceDate IS NOT NULL');
        $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIds);

        $qb->andWhere('farmerIntroduce.introduceDate BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate .' 00:00:00')
            ->setParameter('endDate', $endDate .' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();
//        dd($results);
        $returnArray = [];
        foreach ($results as $result) {
            $returnArray[$result['employeeId']][] = $result;
        }
        return    $returnArray;

    }

    public function getFarmCapacityByEmployeeIds($employeeIds, $filterBy)
    {
        $startDate = isset($filterBy['startDate'])
            ? date('Y-m-d', strtotime($filterBy['startDate']))
            : date('Y-m-01');
        $endDate = isset($filterBy['endDate'])
            ? date('Y-m-d', strtotime($filterBy['endDate']))
            : date('Y-m-t');

        $typeId = isset($filterBy['type']) ? $filterBy['type']->getId() : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.employee', 'employee');
        $qb->select('employee.id AS employeeId');
        $qb->addSelect('MONTH(e.created) AS month');
        $qb->addSelect('fi.cultureSpeciesItemAndQty AS cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        $qb->andWhere('e.created IS NOT NULL');
        $qb->andWhere('employee.id IN (:employeeIds)')
            ->setParameter('employeeIds', $employeeIds);
        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate . ' 00:00:00')
            ->setParameter('endDate', $endDate . ' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();

        $returnArray = [];

        $monthlyCapacity = [];

        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $month = (int)$result['month'];

            $capacity = 0;
            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    // Apply type filter if set
                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
                        $cultureSpeciesItemAndQty = [$typeId=> $cultureSpeciesItemAndQty[$typeId]];
                    }
                    $cultureSpeciesItemAndQty = array_filter($cultureSpeciesItemAndQty, function ($value) {
                        return $value !== null && $value !== '';
                    });
                    $numericValues = array_map('intval', array_values($cultureSpeciesItemAndQty));
                    $capacity = array_sum($numericValues);
                }
            }

            $monthlyCapacity[$empId][$month] = ($monthlyCapacity[$empId][$month] ?? 0) + $capacity;
        }
        //dd($monthlyCapacity);
        return $monthlyCapacity;
    }
    public function getFarmByEmployeeIds($employeeIds, $filterBy)
    {
        $startDate = isset($filterBy['startDate'])
            ? date('Y-m-d', strtotime($filterBy['startDate']))
            : date('Y-m-01');
        $endDate = isset($filterBy['endDate'])
            ? date('Y-m-d', strtotime($filterBy['endDate']))
            : date('Y-m-t');

        $typeId = isset($filterBy['type']) ? $filterBy['type']->getId() : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.employee','employee');
        $qb->select('employee.id AS employeeId, e.id as farmerId');
        $qb->addSelect('MONTH(e.created) AS month');
        $qb->addSelect('fi.cultureSpeciesItemAndQty AS cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        if (isset($typeId)){
            $qb->andWhere('fi.cultureSpeciesItemAndQty LIKE :type')->setParameter('type', '%' . $typeId . '%');
        }
        $qb->andWhere('employee.id IN (:employeeIds)')
            ->setParameter('employeeIds', $employeeIds);
        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate . ' 00:00:00')
            ->setParameter('endDate', $endDate . ' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();
        $returnArray = [];
        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $month = (int)$result['month'];

            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
                        $returnArray[$empId][$month] = ($returnArray[$empId][$month] ?? 0) + 1;
                    }else{
                        $returnArray[$empId][$month] = ($returnArray[$empId][$month] ?? 0) + 1;
                    }
                }
            }
        }
        //dd($returnArray);
        return    $returnArray;

    }

    public function getDailyFarmCapacityByEmployeeIds($employeeIds, $filterBy)
    {
        $startDate = isset($filterBy['startDate'])
            ? date('Y-m-d', strtotime($filterBy['startDate']))
            : date('Y-m-01');
        $endDate = isset($filterBy['endDate'])
            ? date('Y-m-d', strtotime($filterBy['endDate']))
            : date('Y-m-t');

        $typeId = isset($filterBy['type']) ? $filterBy['type']->getId() : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.employee', 'employee');
        $qb->select('employee.id AS employeeId');
        $qb->addSelect('e.created AS created');
        $qb->addSelect('fi.cultureSpeciesItemAndQty AS cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        $qb->andWhere('e.created IS NOT NULL');
        $qb->andWhere('employee.id IN (:employeeIds)')
            ->setParameter('employeeIds', $employeeIds);
        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate . ' 00:00:00')
            ->setParameter('endDate', $endDate . ' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();

        $returnArray = [];

        $dailyCapacity = [];

        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $day = $result['created']->format('d');
            $day = (int)$day;

            $capacity = 0;
            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    // Apply type filter if set
                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
                        $cultureSpeciesItemAndQty = [$typeId=> $cultureSpeciesItemAndQty[$typeId]];
                    }
                    $cultureSpeciesItemAndQty = array_filter($cultureSpeciesItemAndQty, function ($value) {
                        return $value !== null && $value !== '';
                    });
                    $numericValues = array_map('intval', array_values($cultureSpeciesItemAndQty));
                    $capacity = array_sum($numericValues);
                }
            }

            $dailyCapacity[$empId][$day] = ($dailyCapacity[$empId][$day] ?? 0) + $capacity;
        }
//        dd($dailyCapacity);
        return $dailyCapacity;
    }
    public function getDailyFarmByEmployeeIds($employeeIds, $filterBy)
    {
        $startDate = isset($filterBy['startDate'])
            ? date('Y-m-d', strtotime($filterBy['startDate']))
            : date('Y-m-01');
        $endDate = isset($filterBy['endDate'])
            ? date('Y-m-d', strtotime($filterBy['endDate']))
            : date('Y-m-t');

        $typeId = isset($filterBy['type']) ? $filterBy['type']->getId() : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.employee','employee');
        $qb->select('employee.id AS employeeId, e.id as farmerId');
        $qb->addSelect('e.created AS created');
        $qb->addSelect('fi.cultureSpeciesItemAndQty AS cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        if (isset($typeId)){
            $qb->andWhere('fi.cultureSpeciesItemAndQty LIKE :type')->setParameter('type', '%' . $typeId . '%');
        }
        $qb->andWhere('employee.id IN (:employeeIds)')
            ->setParameter('employeeIds', $employeeIds);
        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate . ' 00:00:00')
            ->setParameter('endDate', $endDate . ' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();
        $returnArray = [];
        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $day = $result['created']->format('d');
            $day = (int)$day;

            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
                        $returnArray[$empId][$day] = ($returnArray[$empId][$day] ?? 0) + 1;
                    }else{
                        $returnArray[$empId][$day] = ($returnArray[$empId][$day] ?? 0) + 1;
                    }
                }
            }
        }
        //dd($returnArray);
        return    $returnArray;

    }

    public function getSummeryByEmployeeIds($employeeIds, $filterBy, $typeIds)
    {
        $startDate = isset($filterBy['startDate'])
            ? date('Y-m-d', strtotime($filterBy['startDate']))
            : date('Y-m-01');
        $endDate = isset($filterBy['endDate'])
            ? date('Y-m-d', strtotime($filterBy['endDate']))
            : date('Y-m-t');

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.employee','employee');
        $qb->select('employee.id AS employeeId, e.id as farmerId');
        $qb->addSelect('MONTH(e.created) AS month');
        $qb->addSelect('fi.cultureSpeciesItemAndQty AS cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');

        $qb->andWhere('employee.id IN (:employeeIds)')
            ->setParameter('employeeIds', $employeeIds);
        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate . ' 00:00:00')
            ->setParameter('endDate', $endDate . ' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();
        $returnArray = [];
        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $month = (int)$result['month'];

            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    foreach ($typeIds as $typeId) {
                        if (isset($cultureSpeciesItemAndQty[$typeId])) {
                            $returnArray[$empId][$month][$typeId] = ($returnArray[$empId][$month][$typeId] ?? 0) + 1;
                        }
                    }
                }
            }
        }
//        dd($returnArray);
        return    $returnArray;

    }

    public function getClosedFarmCapacityByEmployeeIds($employeeIds, $filterBy)
    {
        $startDate = isset($filterBy['startDate'])
            ? date('Y-m-d', strtotime($filterBy['startDate']))
            : date('Y-m-01');
        $endDate = isset($filterBy['endDate'])
            ? date('Y-m-d', strtotime($filterBy['endDate']))
            : date('Y-m-t');

        $typeId = isset($filterBy['type']) ? $filterBy['type']->getId() : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.employee', 'employee');
        $qb->select('employee.id AS employeeId');
        $qb->addSelect('MONTH(e.created) AS month');
        $qb->addSelect('fi.cultureSpeciesItemAndQty AS cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        $qb->andWhere('e.created IS NOT NULL');
        $qb->andWhere('e.status IN (:statuses)')
            ->setParameter('statuses', ['closed', 'close']);
        $qb->andWhere('employee.id IN (:employeeIds)')
            ->setParameter('employeeIds', $employeeIds);
        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate . ' 00:00:00')
            ->setParameter('endDate', $endDate . ' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();

        $returnArray = [];

        $monthlyCapacity = [];

        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $month = (int)$result['month'];

            $capacity = 0;
            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    // Apply type filter if set
                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
                        $cultureSpeciesItemAndQty = [$typeId=> $cultureSpeciesItemAndQty[$typeId]];
                    }
                    $cultureSpeciesItemAndQty = array_filter($cultureSpeciesItemAndQty, function ($value) {
                        return $value !== null && $value !== '';
                    });
                    $numericValues = array_map('intval', array_values($cultureSpeciesItemAndQty));
                    $capacity = array_sum($numericValues);
                }
            }

            $monthlyCapacity[$empId][$month] = ($monthlyCapacity[$empId][$month] ?? 0) + $capacity;
        }
        //dd($monthlyCapacity);
        return $monthlyCapacity;
    }
    public function getClosedFarmByEmployeeIds($employeeIds, $filterBy)
    {
        $startDate = isset($filterBy['startDate'])
            ? date('Y-m-d', strtotime($filterBy['startDate']))
            : date('Y-m-01');
        $endDate = isset($filterBy['endDate'])
            ? date('Y-m-d', strtotime($filterBy['endDate']))
            : date('Y-m-t');

        $typeId = isset($filterBy['type']) ? $filterBy['type']->getId() : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.employee','employee');
        $qb->select('employee.id AS employeeId, e.id as farmerId');
        $qb->addSelect('MONTH(e.created) AS month');
        $qb->addSelect('fi.cultureSpeciesItemAndQty AS cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        $qb->andWhere('e.status IN (:statuses)')
            ->setParameter('statuses', ['closed', 'close']);
        if (isset($typeId)){
            $qb->andWhere('fi.cultureSpeciesItemAndQty LIKE :type')->setParameter('type', '%' . $typeId . '%');
        }
        $qb->andWhere('employee.id IN (:employeeIds)')
            ->setParameter('employeeIds', $employeeIds);
        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate . ' 00:00:00')
            ->setParameter('endDate', $endDate . ' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();
        $returnArray = [];
        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $month = (int)$result['month'];

            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
                        $returnArray[$empId][$month] = ($returnArray[$empId][$month] ?? 0) + 1;
                    }else{
                        $returnArray[$empId][$month] = ($returnArray[$empId][$month] ?? 0) + 1;
                    }
                }
            }
        }
        //dd($returnArray);
        return    $returnArray;

    }

    public function getDailyClosedFarmCapacityByEmployeeIds($employeeIds, $filterBy)
    {
        $startDate = isset($filterBy['startDate'])
            ? date('Y-m-d', strtotime($filterBy['startDate']))
            : date('Y-m-01');
        $endDate = isset($filterBy['endDate'])
            ? date('Y-m-d', strtotime($filterBy['endDate']))
            : date('Y-m-t');

        $typeId = isset($filterBy['type']) ? $filterBy['type']->getId() : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.employee', 'employee');
        $qb->select('employee.id AS employeeId');
        $qb->addSelect('e.created AS created');
        $qb->addSelect('fi.cultureSpeciesItemAndQty AS cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        $qb->andWhere('e.created IS NOT NULL');
        $qb->andWhere('e.status IN (:statuses)')
            ->setParameter('statuses', ['closed', 'close']);
        $qb->andWhere('employee.id IN (:employeeIds)')
            ->setParameter('employeeIds', $employeeIds);
        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate . ' 00:00:00')
            ->setParameter('endDate', $endDate . ' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();

        $returnArray = [];

        $dailyCapacity = [];

        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $day = $result['created']->format('d');
            $day = (int)$day;

            $capacity = 0;
            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    // Apply type filter if set
                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
                        $cultureSpeciesItemAndQty = [$typeId=> $cultureSpeciesItemAndQty[$typeId]];
                    }
                    $cultureSpeciesItemAndQty = array_filter($cultureSpeciesItemAndQty, function ($value) {
                        return $value !== null && $value !== '';
                    });
                    $numericValues = array_map('intval', array_values($cultureSpeciesItemAndQty));
                    $capacity = array_sum($numericValues);
                }
            }

            $dailyCapacity[$empId][$day] = ($dailyCapacity[$empId][$day] ?? 0) + $capacity;
        }
//        dd($dailyCapacity);
        return $dailyCapacity;
    }
    public function getDailyClosedFarmByEmployeeIds($employeeIds, $filterBy)
    {
        $startDate = isset($filterBy['startDate'])
            ? date('Y-m-d', strtotime($filterBy['startDate']))
            : date('Y-m-01');
        $endDate = isset($filterBy['endDate'])
            ? date('Y-m-d', strtotime($filterBy['endDate']))
            : date('Y-m-t');

        $typeId = isset($filterBy['type']) ? $filterBy['type']->getId() : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.employee','employee');
        $qb->select('employee.id AS employeeId, e.id as farmerId');
        $qb->addSelect('e.created AS created');
        $qb->addSelect('fi.cultureSpeciesItemAndQty AS cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        $qb->andWhere('e.status IN (:statuses)')
            ->setParameter('statuses', ['closed', 'close']);
        if (isset($typeId)){
            $qb->andWhere('fi.cultureSpeciesItemAndQty LIKE :type')->setParameter('type', '%' . $typeId . '%');
        }
        $qb->andWhere('employee.id IN (:employeeIds)')
            ->setParameter('employeeIds', $employeeIds);
        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate . ' 00:00:00')
            ->setParameter('endDate', $endDate . ' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();
        $returnArray = [];
        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $day = $result['created']->format('d');
            $day = (int)$day;

            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    if (isset($typeId) && isset($cultureSpeciesItemAndQty[$typeId])) {
                        $returnArray[$empId][$day] = ($returnArray[$empId][$day] ?? 0) + 1;
                    }else{
                        $returnArray[$empId][$day] = ($returnArray[$empId][$day] ?? 0) + 1;
                    }
                }
            }
        }
        //dd($returnArray);
        return    $returnArray;

    }

    public function getClosedSummeryByEmployeeIds($employeeIds, $filterBy, $typeIds)
    {
        $startDate = isset($filterBy['startDate'])
            ? date('Y-m-d', strtotime($filterBy['startDate']))
            : date('Y-m-01');
        $endDate = isset($filterBy['endDate'])
            ? date('Y-m-d', strtotime($filterBy['endDate']))
            : date('Y-m-t');

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.employee','employee');
        $qb->select('employee.id AS employeeId, e.id as farmerId');
        $qb->addSelect('MONTH(e.created) AS month');
        $qb->addSelect('fi.cultureSpeciesItemAndQty AS cultureSpeciesItemAndQty');
        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        $qb->andWhere('e.status IN (:statuses)')
            ->setParameter('statuses', ['closed', 'close']);

        $qb->andWhere('employee.id IN (:employeeIds)')
            ->setParameter('employeeIds', $employeeIds);
        $qb->andWhere('e.created BETWEEN :startDate AND :endDate')
            ->setParameter('startDate', $startDate . ' 00:00:00')
            ->setParameter('endDate', $endDate . ' 23:59:59');

        $results = $qb->getQuery()->getArrayResult();
        $returnArray = [];
        foreach ($results as $result) {
            $empId = (int)$result['employeeId'];
            $month = (int)$result['month'];

            if (!empty($result['cultureSpeciesItemAndQty'])) {
                $cultureSpeciesItemAndQty = json_decode($result['cultureSpeciesItemAndQty'], true);

                if (is_array($cultureSpeciesItemAndQty)) {
                    foreach ($typeIds as $typeId) {
                        if (isset($cultureSpeciesItemAndQty[$typeId])) {
                            $returnArray[$empId][$month][$typeId] = ($returnArray[$empId][$month][$typeId] ?? 0) + 1;
                        }
                    }
                }
            }
        }
//        dd($returnArray);
        return    $returnArray;

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