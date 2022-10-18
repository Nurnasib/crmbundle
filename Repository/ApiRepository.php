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

//use Doctrine\ORM\EntityRepository;
use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use App\Entity\Version;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\HttpFoundation\JsonResponse;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\CattleFarmVisit;
use Terminalbd\CrmBundle\Entity\CattleFarmVisitDetails;
use Terminalbd\CrmBundle\Entity\CattleLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\ChickLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\ComplainParameter;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\FarmerTrainingReport;
use Terminalbd\CrmBundle\Entity\FcrDetails;
use Terminalbd\CrmBundle\Entity\LayerLifeCycle;
use Terminalbd\CrmBundle\Entity\LayerLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Entity\LayerStandard;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerTouch\FarmerTouchReport;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Entity\SonaliStandard;
use Terminalbd\CrmBundle\Repository\BaseRepository;
use function Doctrine\ORM\QueryBuilder;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ApiRepository extends BaseRepository
{

    protected function handleLocationSearchBetween($qb, $data)
    {

        if ($data) {
            $locations = explode(',', $data);
            $qb->where('e.upozila IN (:upozila)')->setParameter('upozila', $locations);
        }

    }

    /**
     * @param $terminal
     * @param $locations
     * @return array
     */
    public function apiAgent($terminal, $locations): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Agent::class, 'e');
        $qb->Join('e.agentGroup', 'ag');
        $qb->Join('e.upozila', 'up');
        $qb->leftJoin('up.parent', 'dis');
        $qb->select('e.id as id', 'e.name as name', 'e.mobile as mobile', 'e.email as email', 'e.name as companyName', 'e.agentId as agentId', 'e.address as address');
        $qb->addSelect('ag.name as agentGroup');
        $qb->addSelect('up.name as upozila', 'up.id as upozilaId');
        $qb->addSelect('dis.name as district', 'dis.id as districtId');

//        $qb->where('e.terminal = :terminal')->setParameter('terminal',$terminal);
//        $qb->where('ag.slug IN (:slug)')->setParameter('slug', ['feed', 'chick']);
        $qb->andWhere('e.status =:status')->setParameter('status', 1);
        if ($locations) {
            $locations = explode(',', $locations);
            $qb->andWhere('e.upozila IN (:upozila)')->setParameter('upozila', $locations);
        }
        $qb->orderBy('e.name', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['agentId'] = (int)$row['agentId'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['mobile'] = (string)$row['mobile'];
            $data[$key]['email'] = (string)$row['email'];
            $data[$key]['address'] = (string)$row['address'];
            $data[$key]['agentGroup'] = (string)$row['agentGroup'];
            $data[$key]['upozila'] = (string)$row['upozila'];
            $data[$key]['upozilaId'] = (string)$row['upozilaId'];
            $data[$key]['district'] = (string)$row['district'];
            $data[$key]['districtId'] = (string)$row['districtId'];
        }
        return $data;
    }

    /**
     * @param $terminal
     * @param $locations
     * @return array
     */
    public function apiOnlyNourishAgent($terminal, $locations): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Agent::class, 'e');
        $qb->Join('e.agentGroup', 'ag');
        $qb->Join('e.upozila', 'up');
        $qb->leftJoin('up.parent', 'dis');
        $qb->select('e.id as id', 'e.name as name', 'e.mobile as mobile', 'e.email as email', 'e.name as companyName', 'e.agentId as agentId', 'e.address as address');
        $qb->addSelect('ag.name as agentGroup');
        $qb->addSelect('up.name as upozila', 'up.id as upozilaId');
        $qb->addSelect('dis.name as district', 'dis.id as districtId');

//        $qb->where('e.terminal = :terminal')->setParameter('terminal',$terminal);
        $qb->where('ag.slug IN (:slug)')->setParameter('slug', ['feed', 'chick']);
        $qb->andWhere('e.status =:status')->setParameter('status', 1);
        if ($locations) {
            $locations = explode(',', $locations);
            $qb->andWhere('e.upozila IN (:upozila)')->setParameter('upozila', $locations);
        }
        $qb->orderBy('e.name', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['agentId'] = (int)$row['agentId'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['mobile'] = (string)$row['mobile'];
            $data[$key]['email'] = (string)$row['email'];
            $data[$key]['address'] = (string)$row['address'];
            $data[$key]['agentGroup'] = (string)$row['agentGroup'];
            $data[$key]['upozila'] = (string)$row['upozila'];
            $data[$key]['upozilaId'] = (string)$row['upozilaId'];
            $data[$key]['district'] = (string)$row['district'];
            $data[$key]['districtId'] = (string)$row['districtId'];
        }
        return $data;
    }

    /**
     *  Customer
     * @param $terminal
     * @param $mode
     * @param $locations
     * @return array
     */
    public function customerApi($terminal, $mode, $locations): array
    {

        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(CrmCustomer::class, 'e');
        $qb->join('e.customerGroup', 'cg');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->leftJoin('fi.feed', 'feed');
        $qb->leftJoin('fi.otherFeed', 'otherFeed');
        $qb->leftJoin('fi.farmerType', 'farmerTypes');
        $qb->leftJoin('fi.otherAgent', 'otherAgent');
        $qb->leftJoin('fi.subAgent', 'subAgent');
        $qb->leftJoin('e.agent', 'ca');
        $qb->Join('e.location', 'l');
        $qb->Join('l.parent', 'dis');
        $qb->select('e.id as id', 'e.name as name', 'e.mobile as mobile', 'e.address as address');
        $qb->addSelect('cg.name as customerGroup');
        $qb->addSelect('ca.id as agentId', 'ca.name as agentName');
        $qb->addSelect('l.name as upozila', 'l.id as upozilaId');
        $qb->addSelect('dis.name as district', 'dis.id as districtId');
        $qb->addSelect('fi.cultureSpeciesItemAndQty', 'otherAgent.name AS otherAgentName', 'otherAgent.address AS otherAgentAddress', 'subAgent.name AS subAgentName', 'subAgent.address AS subAgentAddress', 'feed.name AS feedName', 'feed.id AS feed_id', 'otherFeed.name AS previousFeedName', 'otherFeed.id AS other_feed_id', 'farmerTypes.id AS farmerType', 'farmerTypes.name AS farmerTypeName');
        $qb->where("cg.slug =:slug")->setParameter('slug', $mode);
        $qb->andWhere('e.deletedAt IS NULL');
        $qb->andWhere('e.deletedBy IS NULL');
        if ($locations) {
            $locations = explode(',', $locations);
            $qb->andWhere('e.location IN (:upozila)')->setParameter('upozila', $locations);
        }
        $qb->orderBy('e.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
//        return $result;
        $data = array();
        foreach ($result as $key => $row) {
            $previousAgentName = '';
            $previousAgentAddress = '';
            if ($row['otherAgentName']) {
                $previousAgentName = $row['otherAgentName'];
            } elseif ($row['subAgentName']) {
                $previousAgentName = $row['subAgentName'];
            }

            if ($row['otherAgentAddress']) {
                $previousAgentAddress = $row['otherAgentAddress'];
            } elseif ($row['subAgentAddress']) {
                $previousAgentAddress = $row['subAgentAddress'];
            }
            $customerType = $row['farmerTypeName'] != '' ? ' (' . $row['farmerTypeName'] . ')' : '';

            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'] . '' . $customerType;
            $data[$key]['mobile'] = (string)$row['mobile'];
            $data[$key]['address'] = (string)$row['address'];
            $data[$key]['customerGroup'] = (string)$row['customerGroup'];
            $data[$key]['agent'] = (string)$row['agentName'];
            $data[$key]['agentId'] = (string)$row['agentId'];
            $data[$key]['upozila'] = (string)$row['upozila'];
            $data[$key]['upozilaId'] = (string)$row['upozilaId'];
            $data[$key]['district'] = (string)$row['district'];
            $data[$key]['districtId'] = (string)$row['districtId'];
            $data[$key]['previousAgentName'] = (string)$previousAgentName;
            $data[$key]['previousAgentAddress'] = (string)$previousAgentAddress;
            $data[$key]['feed_id'] = (string)$row['feed_id'];
            $data[$key]['feed_name'] = (string)$row['feedName'];
            $data[$key]['farmerType'] = (string)$row['farmerType'];
            $data[$key]['previousFeedId'] = (string)$row['other_feed_id'];
            $data[$key]['previousFeedName'] = (string)$row['previousFeedName'];
            $data[$key]['culture_species_item_and_qty'] = (string)$row['cultureSpeciesItemAndQty'];


        }
        return $data;
    }


    /**
     * @param $terminal
     * @param $mode
     * @param $locations
     * @return array
     */
    public function agentApi($terminal, $mode, $locations): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Agent::class, 'e');
        $qb->Join('e.agentGroup', 'ag');
        $qb->Join('e.upozila', 'up');
        $qb->leftJoin('up.parent', 'dis');
        $qb->select('e.id as id', 'e.name as name', 'e.mobile as mobile', 'e.email as email', 'e.name as companyName', 'e.agentId as agentId', 'e.address as address');
        $qb->addSelect('ag.name as agentGroup');
        $qb->addSelect('up.name as upozila', 'up.id as upozilaId');
        $qb->addSelect('dis.name as district', 'dis.id as districtId');

//        $qb->where('e.terminal = :terminal')->setParameter('terminal',$terminal);
        $qb->where('ag.slug = :slug')->setParameter('slug', $mode);
        $qb->andWhere('e.status =:status')->setParameter('status', 1);
        if ($locations) {
            $locations = explode(',', $locations);
            $qb->andWhere('e.upozila IN (:upozila)')->setParameter('upozila', $locations);
        }
        $qb->orderBy('e.name', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['agentId'] = (int)$row['agentId'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['mobile'] = (string)$row['mobile'];
            $data[$key]['email'] = (string)$row['email'];
            $data[$key]['address'] = (string)$row['address'];
            $data[$key]['agentGroup'] = (string)$row['agentGroup'];
            $data[$key]['upozila'] = (string)$row['upozila'];
            $data[$key]['upozilaId'] = (string)$row['upozilaId'];
            $data[$key]['district'] = (string)$row['district'];
            $data[$key]['districtId'] = (string)$row['districtId'];
        }
        return $data;
    }

    /**
     * CRM Visit Report
     * @param $terminal
     * @param $username
     * @return array
     */

    public function crmVisit($terminal, $username): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(CrmVisit::class, 'cv');
        $qb->leftJoin('cv.employee', 'cve');
        $qb->leftJoin('cve.designation', 'cved');
        $qb->leftJoin('cv.location', 'cvl');

        $qb->select('cv.id as id', 'cv.workingDuration as workingDurationForm', 'cv.workingDurationTo as workingDurationTo', 'cv.created as created');
        $qb->addSelect('cve.name as employeeName');
        $qb->addSelect('cved.name as designationName');
        $qb->addSelect('cvl.name as areaName');

        if ($username) {
            $username = explode(',', $username);
            $qb->where('cve.username IN (:username)')->setParameter('username', $username);
        }

        $qb->orderBy('cv.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['employeeName'] = (string)$row['employeeName'];
            $data[$key]['designationName'] = (string)$row['designationName'];
            $data[$key]['areaName'] = (string)$row['areaName'];
            $data[$key]['workingDurationForm'] = (string)$row['workingDurationForm'];
            $data[$key]['workingDurationTo'] = (string)$row['workingDurationTo'];
            $data[$key]['created'] = $row['created']->format('Y-m-d');

        }
        return $data;
    }

    /**
     *  EMPLOYEE
     * @param $terminal
     * @param $username
     * @return array
     */
    public function employeeApi($terminal, $username): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(User::class, 'u');
        $qb->Join('u.userGroup', 'ug');
        $qb->leftJoin('u.designation', 'ud');
        $qb->leftJoin('u.department', 'd');
        $qb->select('u.id as id', 'u.name as name', 'u.username as username', 'u.email as email', 'u.mobile as mobile', 'u.enabled as enabled');
        $qb->addSelect('ug.name as groupName');
        $qb->addSelect('ud.name as designation');
        $qb->addSelect('d.name as department');

        if ($username) {
            $username = explode(',', $username);
            $qb->where('u.username IN (:username)')->setParameter('username', $username);
        }

        // $qb->where('ug.id = 9');

        $qb->orderBy('u.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['group'] = (string)$row['groupName'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['username'] = (string)$row['username'];
            $data[$key]['designation'] = (string)$row['designation'];
            $data[$key]['email'] = (string)$row['email'];
            $data[$key]['mobile'] = (string)$row['mobile'];
            $data[$key]['department'] = (string)$row['department'];
            $data[$key]['status'] = (string)$row['enabled'];

        }
        return $data;
    }


    /**
     * BROILER STANDARD
     * @param $terminal
     * @param array $data
     * @return array
     */
    public function apiBroiler($terminal, $data = array()): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(BroilerStandard::class, 'b');
        $qb->select('b.id as id', 'b.age as age', 'b.targetBodyWeight as targetBodyWeight', 'b.targetFeedConsumption as targetFeedConsumption');
        //    $qb->where('e.terminal = :terminal')->setParameter('terminal',$terminal);
        $qb->orderBy('b.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['age'] = (int)$row['age'];
            $data[$key]['targetBodyWeight'] = (string)$row['targetBodyWeight'];
            $data[$key]['targetFeedConsumption'] = (string)$row['targetFeedConsumption'];
        }
        return $data;
    }

    /**
     * SONALI STANDARD
     * @param $terminal
     * @param array $data
     * @return array
     */
    public function apiSonali($terminal, $data = array()): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(SonaliStandard::class, 's');
        $qb->select('s.id as id', 's.age as age', 's.feedIntakePerDay as feedIntakePerDay', 's.targetBodyWeight as targetBodyWeight', 's.cumulativeFeedIntake as cumulativeFeedIntake');
        //    $qb->where('e.terminal = :terminal')->setParameter('terminal',$terminal);
        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $cumulative = (int)$row['cumulativeFeedIntake'];
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['age'] = (int)$row['age'];
            $data[$key]['feedIntakePerDay'] = (float)$row['feedIntakePerDay'];
            $data[$key]['cumulativeFeedIntake'] = (float)$row['cumulativeFeedIntake'];
            $data[$key]['targetBodyWeight'] = (int)$row['targetBodyWeight'];
            $data[$key]['fcr'] = $cumulative / (int)$row['targetBodyWeight'];
        }
        return $data;
    }


    /**
     * Setting Life Cycle
     * @param $terminal
     * @param array $data
     * @return array
     */
    public function apiLifeCycleSetting($terminal, $data = array()): array
    {
        $em = $this->_em;
        /*$sql = 'SELECT crm_setting.name, crm_setting_life_cycle.id, crm_setting_life_cycle.number_of_week, crm_setting_life_cycle.status FROM crm_setting INNER JOIN crm_setting_life_cycle ON crm_setting.id = crm_setting_life_cycle.report_id';
        $result = $em->getConnection()->executeQuery($sql)->fetchAll();*/

        //dd($result);
        $qb = $em->createQueryBuilder();
        $qb->from(SettingLifeCycle::class, 'slc');
        $qb->Join('slc.report', 'r');
        $qb->select('slc.id as id, slc.numberOfWeek as numberOfWeek, slc.status as status', 'r.name as name');
        $qb->orderBy('slc.id', 'ASC');

        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['numberOfWeek'] = (int)$row['numberOfWeek'];
            $data[$key]['status'] = (int)$row['status'];

        }
        return $data;
    }

    /**
     * Setting Life Cycle
     * @param $terminal
     * @param array $data
     * @return array
     */
    public function apiSetting($terminal, $data = array()): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->select('s.id as id', 's.name as name', 's.settingType as settingType', 's.slug as slug', 's.status as status'/*,'p.settingType as parent'*/, 'p.name as parentName'/*,'p.id as parent_id'*/);
        $qb->where('s.status = 1');
        $qb->orderBy('s.settingType', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$row['settingType']][] = array(
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'settingType' => $row['settingType'],
                'parentName' => $row['parentName'],
                'slug' => $row['slug'],
            );

        }
        return $data;
    }

    /**
     * Layer Standard
     * @param $terminal
     * @param array $data
     * @return array
     */
    public function apiLayer($terminal, $data = array()): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(LayerStandard::class, 'ls');
        $qb->Join('ls.report', 'r');
        $qb->select('ls.id as id', 'ls.age as age', 'r.name as name', 'ls.targetFeedConsumption as targetFeedConsumption', 'ls.targetBodyWeight as targetBodyWeight', 'ls.targetEggProduction as targetEggProduction', 'ls.targetEggWeight as targetEggWeight');
        //$qb->where('s.name = e.report_id');
        $qb->orderBy('ls.age', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['age'] = (int)$row['age'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['targetFeedConsumption'] = (int)$row['targetFeedConsumption'];
            $data[$key]['targetBodyWeight'] = (int)$row['targetBodyWeight'];
            $data[$key]['targetEggProduction'] = (float)$row['targetEggProduction'];
            $data[$key]['targetEggWeight'] = (float)$row['targetEggWeight'];

        }
        return $data;
    }


    /**
     *  Farmer Introduce Report
     * @param $terminal
     * @param $farmerType
     * @param $employee
     * @return array
     */
    public function farmerIntroduceReport($terminal, $farmerType, $employee): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(FarmerIntroduceDetails::class, 'fid');
        $qb->Join('fid.customer', 'fn');
        $qb->Join('fid.agent', 'fa');
        $qb->Join('fid.farmerType', 'ft');
        $qb->select('fid.createdAt as createdAt', 'fid.id as id', 'fid.previousAgentName as previousAgentName', 'fn.name as farmerName', 'fa.name as agent', 'fn.address as address', 'fn.mobile as mobile', 'fid.previousFeedName as previousFeedName', 'fa.address as agentAddress', 'fid.previousAgentAddress as previousAgentAddress', 'fid.remarks as remarks', 'fid.cultureSpeciesItemAndQty as cultureSpeciesItemAndQty');

        $qb->where('ft.slug = :farmerType')
            ->andWhere('fid.employee = :employee')
            ->setParameters(array('farmerType' => $farmerType, 'employee' => $employee));
        // $qb->where('ft.slug = :farmerType');
        // $qb->setParameter('farmerType', $requestData);
        $qb->orderBy('fid.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        //$sum = 0;
        foreach ($result as $key => $row) {
            $data[$key]['createdAt'] = $row['createdAt']->format('Y-m-d');
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['farmerName'] = (string)$row['farmerName'];
            $data[$key]['mobile'] = (string)$row['mobile'];
            $data[$key]['nameOfAgent'] = (string)$row['agent'];
            $data[$key]['cultureSpeciesItemAndQty'] = json_decode((string)$row['cultureSpeciesItemAndQty']);

//            $sum = $sum + json_decode((int)$row['cultureSpeciesItemAndQty']);
//            $data[$key]['total'] = $sum;
            $data[$key]['agentAddress'] = (string)$row['agentAddress'];
            $data[$key]['address'] = (string)$row['address'];
            $data[$key]['previousFeedName'] = (string)$row['previousFeedName'];
            $data[$key]['previousAgentName'] = (string)$row['previousAgentName'];
            $data[$key]['previousAgentAddress'] = (string)$row['previousAgentAddress'];
            $data[$key]['remarks'] = (string)$row['remarks'];


        }
        return $data;
    }

    /**
     *  Farmer Training Report
     * @param $terminal
     * @param $breedName
     * @param $employee
     * @return array
     */
    public function farmerTrainingReport($terminal, $breedName, $employee): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(FarmerTrainingReport::class, 'f');
        $qb->leftJoin('f.agent', 'agent');
        $qb->leftJoin('f.breedName', 'breedName');
        $qb->leftJoin('f.farmerTrainingReportDetails', 'farmerTrainingReportDetails');
        $qb->leftJoin('farmerTrainingReportDetails.customer', 'farmer');

        $qb->select('f.id as id', 'f.trainingDate as trainingDate', 'f.trainingMaterial as trainingMaterial', 'f.trainingTopics as trainingTopics', 'f.remarks as remarks');
        $qb->addSelect('agent.name AS agentName', 'agent.address AS agentAddress', 'agent.mobile AS agentMobile');
        $qb->addSelect('farmer.id as farmerId', 'farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('farmerTrainingReportDetails.farmerCapacity as farmerCapacity', 'farmerTrainingReportDetails.trainingMaterialQty as trainingMaterialQty');

        $qb->where('breedName.slug = :breedName')
            ->andWhere('f.employee = :employee')
            ->setParameters(array('breedName' => $breedName, 'employee' => $employee));

        $qb->orderBy('f.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['trainingDate'] = $row['trainingDate']->format('Y-m-d');
            $data[$key]['agentName'] = (string)$row['agentName'];
            $data[$key]['agentAddress'] = (string)$row['agentAddress'];
            $data[$key]['agentMobile'] = (string)$row['agentMobile'];
            $data[$key]['farmerId'] = (string)$row['farmerId'];
            $data[$key]['farmerName'] = (string)$row['farmerName'];
            $data[$key]['farmerAddress'] = (string)$row['farmerAddress'];
            $data[$key]['farmerMobile'] = (string)$row['farmerMobile'];
            $data[$key]['farmerCapacity'] = json_decode((string)$row['farmerCapacity']);
            $data[$key]['trainingMaterialQty'] = json_decode((string)$row['trainingMaterialQty']);
            $data[$key]['trainingMaterial'] = json_decode((string)$row['trainingMaterial']);
            $data[$key]['trainingTopics'] = (string)$row['trainingTopics'];
            $data[$key]['remarks'] = (string)$row['remarks'];

        }
        return $data;
    }


    /**
     * Farmer Touch Report
     * @param $terminal
     * @param $start
     * @param $end
     * @param $report
     * @param $employee
     * @return array
     */
    public function farmerTouchReport($terminal, $start, $end, $report, $employee): array
    {
        $em = $this->_em;
        $startDate = date('Y-m-01', strtotime($start));
        $endDate = date('Y-m-t', strtotime($end));

        if ($report && $employee) {
            $qb = $em->createQueryBuilder();
            $qb->from(FarmerTouchReport::class, 'f');
            $qb->join('f.agent', 'agent');
            $qb->join('f.customer', 'farmer');
            $qb->join('f.report', 'reportName');
            $qb->leftJoin('agent.district', 'agentdistrict');
            $qb->leftJoin('agent.upozila', 'agentupozila');

            $qb->select('f.cultureSpeciesItemAndQty as cultureSpeciesItemAndQty', 'f.nourishItemName as nourishItemName', 'f.otherCultureSpecies as otherCultureSpecies', 'f.remarks as remarks');
            $qb->addSelect('agent.mobile as mobile', 'agent.name as name');
            $qb->addSelect('agentdistrict.name as districtName');
            $qb->addSelect('agentupozila.name as upozilaName');
            $qb->addSelect('farmer.name as farmerName', 'farmer.address as farmerAddress', 'farmer.mobile as farmerMoblie');

            $qb->where('f.createdAt >= :startDate')
                ->andWhere('f.createdAt <= :endDate')
                ->andWhere('f.reportParentParent = :report')
                ->andWhere('f.employee = :employee')
                ->setParameters(array('startDate' => $startDate . ' 00:00:00', 'endDate' => $endDate . ' 23:59:59', 'report' => $report, 'employee' => $employee));
            $qb->orderBy('f.id', 'ASC');
            $result = $qb->getQuery()->getArrayResult();
            $data = array();
            foreach ($result as $key => $row) {
                $data[$key]['mobile'] = (string)$row['mobile'];
                $data[$key]['name'] = (string)$row['name'];
                $data[$key]['districtName'] = (string)$row['districtName'];
                $data[$key]['upozilaName'] = (string)$row['upozilaName'];
                $data[$key]['farmerName'] = (string)$row['farmerName'];
                $data[$key]['farmerAddress'] = (string)$row['farmerAddress'];
                $data[$key]['farmerMoblie'] = (string)$row['farmerMoblie'];
                $data[$key]['cultureSpeciesItemAndQty'] = json_decode((string)$row['cultureSpeciesItemAndQty']);
                $data[$key]['nourishItemName'] = (string)$row['nourishItemName'];
                $data[$key]['otherCultureSpecies'] = (string)$row['otherCultureSpecies'];
                $data[$key]['remarks'] = (string)$row['remarks'];

            }
            return $data;
        }
        return array();
    }

    /**
     *  Report Poultry
     * @param $terminal
     * @param $start
     * @param $end
     * @param $report
     * @param $customer
     * @return array
     */
    public function poultryLifeCylceReport($terminal, $start, $end, $report, $customer): array
    {
        $em = $this->_em;
        $startDate = date('Y-m-01', strtotime($start));
        $endDate = date('Y-m-t', strtotime($end));
        if ($report && $customer) {
            $qb = $em->createQueryBuilder();
            $qb->from(ChickLifeCycle::class, 'c');
            $qb->Join('c.agent', 'agent');
            $qb->Join('c.customer', 'farmer');
            $qb->Join('c.report', 'cr');
            $qb->Join('cr.parent', 'crp');
            $qb->Join('c.crmChickLifeCycleDetails', 'cc');
            $qb->leftJoin('c.hatchery', 'ch');
            $qb->leftJoin('c.breed', 'cb');
            $qb->leftJoin('c.feed', 'cf');
            $qb->leftJoin('cf.parent', 'cfp');

            $qb->select('c.id as id', 'c.hatchingDate as hatchingDate', 'c.totalBirds as totalBirds');
            $qb->addSelect('agent.name as agentName', 'agent.address as agentAddress');
            $qb->addSelect('farmer.name as farmerName', 'farmer.address as farmerAddress', 'farmer.mobile as farmerMobile');
            $qb->addSelect('cc.visitingWeek as visitingWeek', 'cc.ageDays as ageDays', 'cc.mortalityPes as mortalityPes', 'cc.mortalityPercent as mortalityPercent', 'cc.weightStandard as weightStandard', 'cc.weightAchieved as weightAchieved', 'cc.feedTotalKg as feedTotalKg', 'cc.perBird as perBird', 'cc.feedStandard as feedStandard', 'cc.withoutMortality as withoutMortality', 'cc.withMortality as withMortality', 'cc.proDate as proDate', 'cc.batchNo as batchNo', 'cc.remarks as remarks');
            $qb->addSelect('ch.name as hatchery');
            $qb->addSelect('cb.name as breed');
            $qb->addSelect('cf.name as feed');
            $qb->addSelect('cfp.name as feedtype');

            $qb->where('c.createdAt >= :startDate')
                ->andWhere('c.createdAt <= :endDate')
                ->andWhere('cr.slug = :report')
                ->andWhere('farmer.name = :farmer')
                ->setParameters(array(
                    'startDate' => $startDate . ' 00:00:00',
                    'endDate' => $endDate . ' 23:59:59',
                    'report' => $report,
                    'farmer' => $customer,
                ));
            //$qb->where('c.id = 20');
            $qb->orderBy('c.id', 'ASC');
            $result = $qb->getQuery()->getArrayResult();
            //dd($result);
            $data = array();

            foreach ($result as $key => $row) {
                $data[$key]['agentName'] = (string)$row['agentName'];
                $data[$key]['agentAddress'] = (string)$row['agentAddress'];
                $data[$key]['farmerName'] = (string)$row['farmerName'];
                $data[$key]['farmerAddress'] = (string)$row['farmerAddress'];
                $data[$key]['farmerMobile'] = (string)$row['farmerMobile'];
                $data[$key]['hatchingDate'] = $row['hatchingDate']->format('Y-m-d');
                $data[$key]['visitingWeek'] = (int)$row['visitingWeek'];
                $data[$key]['totalBirds'] = (int)$row['totalBirds'];
                $data[$key]['ageDays'] = (int)$row['ageDays'];
                $data[$key]['mortalityPes'] = (int)$row['mortalityPes'];
                $data[$key]['mortalityPercent'] = (float)$row['mortalityPercent'];
                $data[$key]['weightStandard'] = (int)$row['weightStandard'];
                $data[$key]['weightAchieved'] = (int)$row['weightAchieved'];
                $data[$key]['feedTotalKg'] = (int)$row['feedTotalKg'];
                $data[$key]['perBird'] = (float)$row['perBird'];
                $data[$key]['feedStandard'] = (float)$row['feedStandard'];
                $data[$key]['withoutMortality'] = (float)$row['withoutMortality'];
                $data[$key]['withMortality'] = (float)$row['withMortality'];
                $data[$key]['hatchery'] = (string)$row['hatchery'];
                $data[$key]['breed'] = (string)$row['breed'];
                $data[$key]['feed'] = (string)$row['feed'];
                $data[$key]['feedtype'] = (string)$row['feedtype'];
                $data[$key]['proDate'] = $row['proDate'];
                $data[$key]['batchNo'] = (int)$row['batchNo'];
                $data[$key]['remarks'] = (string)$row['remarks'];

            }
            return $data;
        }
    }

    /**
     *  Cattle Farm Visit
     * @param $terminal
     * @param $start
     * @param $end
     * @param $employee
     * @return array
     */
    public function farmVisitCattle($terminal, $start, $end, $employee): array
    {
        $em = $this->_em;
        $startDate = date('Y-m-01', strtotime($start));
        $endDate = date('Y-m-t', strtotime($end));

        $qb = $em->createQueryBuilder();
        $qb->from(CattleFarmVisit::class, 'cfv');
        $qb->leftJoin('cfv.crmCattleFarmVisitDetails', 'cfvd');
        $qb->leftJoin('cfvd.customer', 'cfvdc');
        $qb->leftJoin('cfvd.agent', 'cfvda');
        $qb->leftJoin('cfvda.upozila', 'cfvdaz');
        $qb->leftJoin('cfvda.district', 'cfvdad');

        $qb->addselect('cfvd.visitingDate as visitingDate', 'cfvd.cattlePopulationOx as cattlePopulationOX', 'cfvd.cattlePopulationCow as cattlePopulationCow', 'cfvd.cattlePopulationCalf as cattlePopulationCalf', 'cfvd.avgMilkYieldPerDay as avgMilkYieldPerDay', 'cfvd.conceptionRate as conceptionRate', 'cfvd.fodderGreenGrassKg as fodderGreenGrassKg', 'cfvd.fodderStrawKg as fodderStrawKg', 'cfvd.typeOfConcentrateFeed as typeOfConcentrateFeed', 'cfvd.marketPriceMilkPerLiter as marketPriceMilkPerLiter', 'cfvd.marketPriceMeatPerKg as marketPriceMeatPerKg', 'cfvd.remarks as comment');
        $qb->addselect('cfvdc.name as customerName', 'cfvdc.address as customerAddress');
        $qb->addselect('cfvda.phone as agentPhone');
        $qb->addselect('cfvdaz.name as agentUpozila');
        $qb->addselect('cfvdad.name as agentDistrict');

        $qb->where('cfv.reportingMonth >= :startDate')
            ->andWhere('cfv.reportingMonth <= :endDate')
            ->andWhere('cfv.employee = :employee')
            ->setParameters(array('startDate' => $startDate . ' 00:00:00', 'endDate' => $endDate . ' 23:59:59', 'employee' => $employee));

        $qb->orderBy('cfv.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['visitingDate'] = $row['visitingDate']->format('Y-m-d');
            $data[$key]['customerName'] = (string)$row['customerName'];
            $data[$key]['customerAddress'] = (string)$row['customerAddress'];
            $data[$key]['agentPhone'] = (string)$row['agentPhone'];
            $data[$key]['agentUpozila'] = (string)$row['agentUpozila'];
            $data[$key]['agentDistrict'] = (string)$row['agentDistrict'];
            $data[$key]['cattlePopulationOX'] = (int)$row['cattlePopulationOX'];
            $data[$key]['cattlePopulationCow'] = (int)$row['cattlePopulationCow'];
            $data[$key]['cattlePopulationCalf'] = (int)$row['cattlePopulationCalf'];
            $data[$key]['avgMilkYieldPerDay'] = (int)$row['avgMilkYieldPerDay'];
            $data[$key]['conceptionRate'] = (int)$row['conceptionRate'];
            $data[$key]['fodderGreenGrassKg'] = (int)$row['fodderGreenGrassKg'];
            $data[$key]['fodderStrawKg'] = (int)$row['fodderStrawKg'];
            $data[$key]['typeOfConcentrateFeed'] = (string)$row['typeOfConcentrateFeed'];
            $data[$key]['marketPriceMilkPerLiter'] = (int)$row['marketPriceMilkPerLiter'];
            $data[$key]['marketPriceMeatPerKg'] = (int)$row['marketPriceMeatPerKg'];
            $data[$key]['comment'] = (string)$row['comment'];

        }
        return $data;
    }

    /**
     * Report Poultry FRC
     */
    public function frcReportPoulty($terminal, $start, $end, $report, $employee): array
    {
        $em = $this->_em;

        $startDate = date('Y-m-01', strtotime($start));
        $endDate = date('Y-m-t', strtotime($end));

        if ($report && $employee) {
            $qb = $em->createQueryBuilder();
            $qb->from(FcrDetails::class, 'f');
            $qb->join('f.agent', 'agent');
            $qb->Join('f.report', 'fr');
            $qb->Join('f.employee', 'employee');
            $qb->join('f.hatchery', 'hatchery');
            $qb->join('f.breed', 'breed');
            $qb->join('f.feed', 'feed');
            $qb->leftjoin('f.feedMill', 'feedMill');
            $qb->leftJoin('agent.district', 'agentdistrict');

            $qb->select('f.id as id', 'f.hatchingDate as hatchingDate', 'f.totalBirds as totalBirds', 'f.ageDay as ageDay', 'f.mortalityPes as mortalityPes', 'f.mortalityPercent as mortalityPercent', 'f.weightStandard as weightStandard', 'f.feedConsumptionTotalKg as feedConsumptionTotalKg', 'f.feedConsumptionPerBird as feedConsumptionPerBird', 'f.fcrWithoutMortality as fcrWithoutMortality', 'f.fcrWithMortality as fcrWithMortality', 'f.proDate as proDate', 'f.batchNo as batchNo', 'f.remarks as remarks');
            $qb->addSelect('agent.name as agentName', 'agent.address as agentAddress');
            $qb->addSelect('agentdistrict.name as districtName');
            $qb->addSelect('hatchery.name as hatcheryName');
            $qb->addSelect('breed.name as breedName');
            $qb->addSelect('feed.name as feedName');
            $qb->addSelect('feedMill.name as feedMillName');

            $qb->where('f.reportingMonth >= :startDate')
                ->andWhere('f.reportingMonth <= :endDate')
                ->andWhere('fr.slug = :report')
                ->andWhere('employee.name = :employee')
                ->setParameters(array(
                    'startDate' => $startDate . ' 00:00:00',
                    'endDate' => $endDate . ' 23:59:59',
                    'report' => $report,
                    'employee' => $employee
                ));

            $qb->orderBy('f.id', 'ASC');
            $result = $qb->getQuery()->getArrayResult();

            $data = array();
            foreach ($result as $key => $row) {
                $data[$key]['agentName'] = (string)$row['agentName'];
                $data[$key]['districtName'] = (string)$row['districtName'];
                $data[$key]['agentAddress'] = (string)$row['agentAddress'];
                $data[$key]['hatchingDate'] = $row['hatchingDate']->format('Y-m-d');
                $data[$key]['totalBirds'] = (int)$row['totalBirds'];
                $data[$key]['ageDay'] = (int)$row['ageDay'];
                $data[$key]['mortalityPes'] = (int)$row['mortalityPes'];
                $data[$key]['weightStandard'] = (int)$row['weightStandard'];
                $data[$key]['feedConsumptionTotalKg'] = (int)$row['feedConsumptionTotalKg'];
                $data[$key]['feedConsumptionPerBird'] = (int)$row['feedConsumptionPerBird'];
                $data[$key]['fcrWithoutMortality'] = (float)$row['fcrWithoutMortality'];
                $data[$key]['fcrWithMortality'] = (float)$row['fcrWithMortality'];
                $data[$key]['hatcheryName'] = (string)$row['hatcheryName'];
                $data[$key]['breedName'] = (string)$row['breedName'];
                $data[$key]['feedName'] = (string)$row['feedName'];
                $data[$key]['feedMillName'] = (string)$row['feedMillName'];
                $data[$key]['proDate'] = $row['proDate'];
                $data[$key]['batchNo'] = (int)$row['batchNo'];
                $data[$key]['remarks'] = (string)$row['remarks'];
            }
            return $data;
        }

    }

    /**
     * Daily Activies Purpose
     */
    public function dailyActiviesPurpose()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->select('s.id as id', 's.name as name', 's.settingType as settingType');

        $qb->where("s.settingType = 'PURPOSE'");
        $qb->andWhere('s.slug IS NULL or s.slug NOT IN (:slug)')->setParameter('slug', ['broiler-shed-included', 'layer-shed-included', 'cattle-farm-included', 'pond-included']);
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (string)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            //$data[$key]['breedName'] = (string)$row['breedName'];

            /*$data[$row['settingType']][]= array(
                'id'=>(int)$row['id'],
                'name'=>$row['name']
            );*/

        }
        return $data;

    }

    /**
     * Daily Activies Purpose
     */
    public function dailyActivitiesPurposeForSalesAndMarketing()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->select('s.id as id', 's.name as name', 's.settingType as settingType');

        $qb->where("s.settingType = 'PURPOSE'");
        $qb->andWhere('s.slug IN (:slug)')->setParameter('slug', ['broiler-shed-included', 'layer-shed-included', 'cattle-farm-included', 'pond-included', 'market-promotion', 'area-problems', 'customer-service', 'problem-farm-visit', 'survey-purpose', 'others-purpose']);
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (string)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
        }
        return $data;

    }

    /**
     * Vehicle
     */
    public function vehicle()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->select('s.id as id', 's.name as name', 's.settingType as settingType');

        $qb->where("s.settingType = 'VEHICLE'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (string)$row['id'];
            $data[$key]['name'] = (string)$row['name'];


        }
        return $data;

    }

    /**
     * Farmer Select Purpose
     */
    public function farmerSelectPurpose($terminal, $employee)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(FarmerIntroduceDetails::class, 'fid');
        $qb->Join('fid.customer', 'farmer');
        $qb->Join('fid.employee', 'employee');

        $qb->select('farmer.id as id', 'farmer.name as name', 'farmer.mobile as mobile');

        $qb->where('employee.id = :employeeId')->setParameters(array('employeeId' => $employee));

        $qb->orderBy('fid.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        //dd($result);
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (string)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['mobile'] = (string)$row['mobile'];

        }
        return $data;
    }

    /**
     * Select Farm Type
     */
    public function selectFarmType()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as name','s.slug', 's.settingType as settingType');
        $qb->addselect('p.name as breedName');

        $qb->where("s.settingType = 'FARM_TYPE'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (string)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['slug'] = (string)$row['slug'];
            $data[$key]['breedName'] = (string)$row['breedName'];

            /*$data[$row['breedName']][]= array(
                'id'=>(int)$row['id'],
                'name'=>$row['name'],
                'breedName'=>$row['breedName'],
            );*/

        }
        return $data;
    }

    /**
     * Select Farm Type For Marketing Employee
     */
    public function selectFarmTypeForMarketing()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as name', 's.slug as slug', 's.settingType as settingType');
        $qb->addselect('p.name as breedName');

        $qb->where("s.settingType = 'FARM_TYPE'");
        $qb->andWhere('s.status = 1');
        $qb->andWhere('s.slug IN (:slug)')->setParameter('slug', ['others-poultry', 'others-cattle', 'others-fish']);

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (string)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['slug'] = (string)$row['slug'];
            $data[$key]['breedName'] = (string)$row['breedName'];

        }
        return $data;
    }

    /**
     * Farm Select Report
     */
    public function farmSelectReport()
    {
        $exceptSlug = array('fcr-after-sale-boiler', 'fcr-after-sale-sonali');

        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as name', 's.slug as slug');
        $qb->addSelect('p.name as farmType', 'p.slug as farmTypeSlug');

        $qb->where("s.settingType = 'FARMER_REPORT'");
        $qb->andWhere('s.status = 1');
        $qb->andWhere($qb->expr()->notIn('s.slug', $exceptSlug));
        //    $qb->andWhere('s.slug NOT IN (:slug)')->setParameter('slug', $exceptSlug);


        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();

        $data = array();
        foreach ($result as $key => $row) {
//            if(!in_array($row['slug'],$exceptSlug)){
            $data[$key]['id'] = (string)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['slug'] = (string)$row['slug'];
            $data[$key]['farmType'] = (string)$row['farmType'];
            $data[$key]['farmTypeSlug'] = (string)$row['farmTypeSlug'];
//            }

        }
        return $data;
    }

    /**
     * Farm Select Report For Marketing
     */
    public function farmSelectReportForMarketing()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as name', 's.slug as slug');
        $qb->addSelect('p.name as farmType', 'p.slug as farmTypeSlug');

        $qb->where("s.settingType = 'FARMER_REPORT'");
        $qb->andWhere('s.status = 1');
        $qb->andWhere('s.slug IN (:slug)')->setParameter('slug', ['farmer-introduce-report-poultry', 'farmer-introduce-report-cattle', 'farmer-introduce-report-fish']);

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();

        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (string)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['slug'] = (string)$row['slug'];
            $data[$key]['farmType'] = (string)$row['farmType'];
            $data[$key]['farmTypeSlug'] = (string)$row['farmTypeSlug'];
        }
        return $data;
    }

    /**
     * Search Farmer
     */
    public function searchfarmer($user)
    {
        $arrs = array();
        foreach ($user->getUpozila() as $location) {
            $arrs[] = $location->getId();
        }
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(CrmCustomer::class, 'e');
        $qb->join('e.location', 'location');
        $qb->join('e.customerGroup', 's');
        $qb->join('e.agent', 'a');
        $qb->join('e.farmerIntroduce', 'fi');
        $qb->join('fi.farmerType', 'farmerType');

        $qb->select('e.id as id', 'e.name as name', 'e.address as address', 'e.mobile as mobile');
        $qb->addSelect('a.id as agentId', 'a.name as agentName', 'farmerType.name AS farmerTypeName');

        $qb->where('s.slug = :slug')->setParameter('slug', 'farmer');
        $qb->andWhere('location.id IN (:upozils)')->setParameter('upozils', $arrs);
        $result = $qb->getQuery()->getArrayResult();

        $data = array();
        foreach ($result as $key => $row) {
            $customerType = $row['farmerTypeName'] != '' ? ' (' . $row['farmerTypeName'] . ')' : '';

            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'] . '' . $customerType;;
            $data[$key]['address'] = (string)$row['address'];
            $data[$key]['mobile'] = (string)$row['mobile'];
            $data[$key]['agentId'] = (string)$row['agentId'];
            $data[$key]['agentName'] = (string)$row['agentName'];
        }
        return $data;
    }

    /**
     * New Farmer Type
     */
    public function newfarmertype()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as name', 's.settingType as settingType');
        $qb->addselect('p.name as breedName');

        $qb->where("s.settingType = 'BREED_NAME'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];

        }

        return $data;
    }

    /**
     * ageweek
     */
    public function ageweek()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(LayerStandard::class, 'l');
        $qb->leftJoin('l.report', 'r');

        $qb->select('l.id as id', 'l.age as ageWeek');

        $qb->where("r.slug = 'layer-life-cycle-brown'");

        $qb->orderBy('l.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['ageWeek'] = (float)$row['ageWeek'];

        }

        return $data;
    }

    /**
     * Feed Type
     */
    public function feedType()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->leftJoin('p.parent', 'pp');


        $qb->select('s.id as id', 's.name as feedTypeName');
        $qb->addSelect('p.name as feedName');
        $qb->addSelect('pp.name as parentName');

        $qb->where("s.settingType = 'FEED_TYPE'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['feedTypeName'] = (string)$row['feedTypeName'];
            $data[$key]['feedName'] = (string)$row['feedName'];
            $data[$key]['parentName'] = (string)$row['parentName'];

        }

        return $data;
    }

    /**
     * Feed Type
     */
    public function fishFeedType()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->leftJoin('p.parent', 'pp');


        $qb->select('s.id as id', 's.name as feedTypeName');
        $qb->addSelect('p.name as feedName');
        $qb->addSelect('pp.name as parentName');

        $qb->where("s.settingType = 'FEED_TYPE'");
        $qb->andWhere('s.status = 1');
        $qb->andWhere('pp.name =:parentParentName');
        $qb->setParameter('parentParentName', 'Fish');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['feedTypeName'] = (string)$row['feedTypeName'];
        }

        return $data;
    }

    /**
     * Fish Species Name
     */
    public function fishSpeciesName()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->leftJoin('p.parent', 'pp');


        $qb->select('s.id as id', 's.name as speciesName');
        $qb->addSelect('p.name as feedTypeName');

        $qb->where("s.settingType = 'SPECIES_NAME'");
        $qb->andWhere('s.status = 1');
        $qb->andWhere('pp.name =:parentParentName');
        $qb->setParameter('parentParentName', 'Fish');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['speciesName'] = (string)$row['speciesName'];
            $data[$key]['feedTypeName'] = (string)$row['feedTypeName'];
        }

        return $data;
    }

    /**
     * Hatchery
     */
    public function hatchery()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');


        $qb->select('s.id as id', 's.name as hatchery');

        $qb->where("s.settingType = 'HATCHERY'");
        $qb->andWhere('s.status = 1');
        $qb->orderBy('s.sortOrder', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['hatchery'] = (string)$row['hatchery'];

        }

        return $data;
    }

    /**
     * Hatchery
     */
    public function fcrHatcheryCompany()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');


        $qb->select('s.id', 's.name');

        $qb->where("s.settingType = 'HATCHERY'");
        $qb->andWhere('s.status = 1');
        $qb->andWhere('s.slug IN (:slug)')->setParameter('slug', ['nourish', 'cp', 'ag', 'new-hope', 'kazi', 'aftab', 'aci-godrej', 'paragon', 'provita', 'quality', 'aman', 'rrp']);
        $qb->orderBy('s.sortOrder', 'ASC');
        return $qb->getQuery()->getArrayResult();

    }

    /**
     * FEED MILL
     */
    public function feedMill()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');


        $qb->select('s.id as id', 's.name as feedMill');

        $qb->where("s.settingType = 'FEED_MILL'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.name', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['feedMill'] = (string)$row['feedMill'];

        }

        return $data;
    }

    /**
     * BREED TYPE
     */
    public function breedType()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->leftJoin('p.parent', 'pp');

        $qb->select('s.id as id', 's.name as breedType');
        $qb->addSelect('p.name as breedName');
        $qb->addSelect('pp.name as parentName');

        $qb->where("s.settingType = 'BREED_TYPE'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['breedType'] = (string)$row['breedType'];
            $data[$key]['breedName'] = (string)$row['breedName'];
            $data[$key]['parentName'] = (string)$row['parentName'];

        }

        return $data;
    }

    /**
     * Color
     */
    public function color()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');

        $qb->select('s.id as id', 's.name as color');

        $qb->where("s.settingType = 'COLOR'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.name', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['color'] = (string)$row['color'];
        }
        return $data;
    }

    /**
     * Feed
     */
    public function feed()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->select('s.id as id', 's.name as feedName');

        $qb->where("s.settingType = 'FEED_NAME'");
        $qb->andWhere('s.status = 1');
        $qb->orderBy('s.sortOrder', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['feedName'] = (string)$row['feedName'];
        }
        return $data;
    }

    /**
     * Disease
     */
    public function disease()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as feedName');
        $qb->addSelect('p.name as breedName');

        $qb->where("s.settingType = 'DISEASE_NAME'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['feedName'] = (string)$row['feedName'];
            $data[$key]['breedName'] = (string)$row['breedName'];
        }
        return $data;
    }

    /**
     * Product Name
     */
    public function product()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->leftJoin('p.parent', 'pp');

        $qb->select('s.id as id', 's.name as feedName');
        $qb->addSelect('pp.name as name');

        $qb->where("s.settingType = 'PRODUCT_TYPE'");
        $qb->andWhere('s.status = 1');
        $qb->andWhere('s.slug NOT IN (:slug)')->setParameter('slug', ['broiler-chicks', 'layer-chicks', 'sonali-chicks']);

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['feedName'] = (string)$row['feedName'];
            $data[$key]['name'] = (string)$row['name'];

        }

        return $data;
    }


    /**
     * Product Name For Chick Boiler
     */
    public function productForBoilerChick()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->leftJoin('p.parent', 'pp');

        $qb->select('s.id as id', 's.name as feedName');
        $qb->addSelect('pp.name as name');

        $qb->where("s.settingType = 'PRODUCT_TYPE'");
        $qb->andWhere('s.status = 1');
        $qb->andWhere('s.slug IN (:slug)')->setParameter('slug', ['broiler-chicks']);

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['feedName'] = (string)$row['feedName'];
            $data[$key]['name'] = (string)$row['name'];

        }

        return $data;
    }

    /**
     * Product Name For Chick Layer
     */
    public function productForLayerChick()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->leftJoin('p.parent', 'pp');

        $qb->select('s.id as id', 's.name as feedName');
        $qb->addSelect('pp.name as name');

        $qb->where("s.settingType = 'PRODUCT_TYPE'");
        $qb->andWhere('s.status = 1');
        $qb->andWhere('s.slug IN (:slug)')->setParameter('slug', ['broiler-chicks', 'layer-chicks', 'sonali-chicks']);

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['feedName'] = (string)$row['feedName'];
            $data[$key]['name'] = (string)$row['name'];
        }

        return $data;
    }

    /**
     * Main Culture Species
     */
    public function mainculturespecies()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as feedName');
        $qb->addSelect('p.name as name');

        $qb->where("s.settingType = 'SPECIES_TYPE'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['feedName'] = (string)$row['feedName'];
            $data[$key]['name'] = (string)$row['name'];

        }

        return $data;
    }

    /**
     * User Visiting Area
     */
    public function usercrmvisitingarea($employee)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(User::class, 'u');
        $qb->Join('u.upozila', 'uz');

        $qb->select('u.id as id', 'u.name as name');
        $qb->addSelect('uz.id as upozilaId', 'uz.name as upozilaName');

        $qb->where('u.id = :employeeId')->setParameters(array('employeeId' => $employee));
        $qb->orderBy('u.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            /*$data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];*/
            $data[$key]['upozilaId'] = (string)$row['upozilaId'];
            $data[$key]['upozilaName'] = (string)$row['upozilaName'];

        }

        return $data;
    }

    /**
     * Dairy Breed Type
     */
    public function dairyBreedType()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as breedType');
        $qb->addSelect('p.name as breedName');

        $qb->where("s.settingType = 'BREED_TYPE'");
        $qb->andWhere('s.status = 1');

        $qb->andWhere("p.name = 'Dairy'");
        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['breedType'] = (string)$row['breedType'];


        }

        return $data;
    }

    /**
     * Fattening Breed Type
     */
    public function fatteningBreedType()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as breedType');
        $qb->addSelect('p.name as breedName');

        $qb->where("s.settingType = 'BREED_TYPE'");
        $qb->andWhere('s.status = 1');

        $qb->andWhere("p.name = 'Fattening'");
        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['breedType'] = (string)$row['breedType'];
        }

        return $data;
    }


    public function getData()
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.employee', 'employee');
        $qb->select('e.id', 'e.batchNo', 'e.status', 'e.createdAt');
        $qb->addSelect('employee.userId', 'employee.name AS employeeName');
        return $qb->getQuery()->getArrayResult();
    }


    public function companySpeciesWiseAvarageFCRBefore()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as fishName');
        $qb->addSelect('p.name as fishType');

        $qb->where("s.settingType = 'SPECIES_NAME'");
        $qb->andWhere('s.status = 1');

        $qb->andWhere("p.settingType = 'FEED_TYPE'");
        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['fishName'] = (string)$row['fishName'];
            $data[$key]['fishType'] = (string)$row['fishType'];
        }
        return $data;
    }


    /**
     * Average Fish Sale Price (Farmer Price)
     */
    public function fishSalesPrice()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as gmPcs');
        $qb->addSelect('p.name as fishName', 'p.id as fishId');

        $qb->where("s.settingType = 'FISH_SIZE'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['fishId'] = (int)$row['fishId'];
            $data[$key]['fishName'] = (string)$row['fishName'];
            $data[$key]['gmPcs'] = (string)$row['gmPcs'];

        }
        return $data;

    }

    /**
     * Company Wise Feed Sale Report fish
     */
    public function companyWiseFeedSaleFish()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');

        $qb->select('s.id as id', 's.name as name');
        $qb->addSelect('p.name as feedName');

        $qb->where("s.settingType = 'PRODUCT_TYPE'");
        $qb->andWhere('s.status = 1');

        $qb->andWhere("p.name = 'Fish'");
        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['feedName'] = (string)$row['feedName'];
        }

        return $data;
    }

    /**
     * Company Or Feed Name
     */
    public function company()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');

        $qb->select('s.id as id', 's.name as name');
        $qb->where("s.settingType = 'FEED_NAME'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.sortOrder', 'ASC');
        $result = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
        }
        return $data;
    }

    /**
     * Competitors Company Tilapia Fry Sales
     */
    public function competitorsCompany()
    {
        $exceptName = ['Nourish'];

        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->select('s.id as id', 's.name as name');
        $qb->where("s.settingType = 'FEED_NAME'");
        $qb->andWhere('s.status = 1');

        $qb->andWhere('s.name NOT IN (:name)')->setParameter('name', $exceptName);
        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
        }
        return $data;
    }

    /**
     * Farm Select Report Fcr After
     */
    public function farmSelectReportFcrAfter()
    {
        $exceptSlug = ['fcr-after-sale-boiler', 'fcr-after-sale-sonali', 'company-species-wise-average-fcr-after'];

        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');

        $qb->select("s.id as id", "s.name as name");
        $qb->where("s.settingType = 'FARMER_REPORT'");
        $qb->andWhere('s.status = 1');

        $qb->andWhere('s.slug IN (:slug)')->setParameter('slug', $exceptSlug);
        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
        }
        return $data;

    }

    public function getLifeCycleData($parameters)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(ChickLifeCycleDetails::class, 'clcd');
        $qb->join('clcd.crmChickLifeCycle', 'clc');
        $qb->join('clc.customer', 'farmer');
        $qb->join('clc.employee', 'employee');
        $qb->join('clc.report', 'report');
        $qb->select('clcd');
        $qb->where("clc.lifeCycleState = 'IN_PROGRESS'");
        $qb->andWhere('farmer.id =:farmer_id')->setParameter('farmer_id', $parameters['farmer_id']);
        $qb->andWhere('employee.id =:employee_id')->setParameter('employee_id', $parameters['employee_id']);
        $qb->andWhere('report.id =:report_id')->setParameter('report_id', $parameters['report_id']);

        return $qb->getQuery()->getArrayResult();
    }

    public function getLayerLifeCycleData($parameters)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(LayerLifeCycleDetails::class, 'lfcd');
        $qb->join('lfcd.crmLayerLifeCycle', 'lfc');
        $qb->join('lfc.customer', 'farmer');
        $qb->join('lfc.employee', 'employee');
        $qb->join('lfc.report', 'report');
        $qb->select('lfcd');
        $qb->where("lfc.lifeCycleState = 'IN_PROGRESS'");
        $qb->andWhere('farmer.id =:farmer_id')->setParameter('farmer_id', $parameters['farmer_id']);
        $qb->andWhere('employee.id =:employee_id')->setParameter('employee_id', $parameters['employee_id']);
        $qb->andWhere('report.id =:report_id')->setParameter('report_id', $parameters['report_id']);

        return $qb->getQuery()->getArrayResult();
    }

    public function getCattleLifeCycleData($parameters)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(CattleLifeCycleDetails::class, 'clcd');
        $qb->join('clcd.crmCattleLifeCycle', 'clc');
        $qb->join('clc.customer', 'farmer');
        $qb->join('clc.employee', 'employee');
        $qb->join('clc.report', 'report');
        $qb->select('clcd');
        $qb->where("clc.lifeCycleState = 'IN_PROGRESS'");
        $qb->andWhere('farmer.id =:farmer_id')->setParameter('farmer_id', $parameters['farmer_id']);
        $qb->andWhere('employee.id =:employee_id')->setParameter('employee_id', $parameters['employee_id']);
        $qb->andWhere('report.id =:report_id')->setParameter('report_id', $parameters['report_id']);

        return $qb->getQuery()->getArrayResult();
    }

    public function getCurrentVersion()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Version::class, 'v');
        $qb->select('v.version', 'v.status');
        $qb->where('v.status = 1');

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getComplainType($type)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(ComplainParameter::class, 'c');
        $qb->select('c.id', 'c.type', 'c.item', 'c.slug', 'c.status', 'c.days', 'c.quantity', 'c.order');
        $qb->where('c.type =:type')->setParameter('type', $type);
        $qb->andWhere('c.status = 1');
        $qb->orderBy('c.order', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    public function getTransport($type)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->select('s.id', 's.settingType', 's.name', 's.slug', 's.status');
        $qb->where('s.settingType =:type')->setParameter('type', $type);
        $qb->andWhere('s.status = 1');

        return $qb->getQuery()->getArrayResult();
    }

    public function getNourishHatchery($type)
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->select('s.id', 's.settingType', 's.name', 's.slug', 's.status');
        $qb->where('s.settingType =:type')->setParameter('type', $type);
        $qb->andWhere('s.status = 1');

        return $qb->getQuery()->getArrayResult();
    }


    /**
     * Lab Name
     */
    public function labName()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->select('s.id as id', 's.name as labName');
        $qb->where("s.settingType = 'LAB_NAME'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['labName'] = (string)$row['labName'];
        }
        return $data;
    }

    /**
     * Lab Service Name
     */
    public function labServiceName()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->select('s.id as id', 's.name as labServiceName');
        $qb->addSelect('p.name as breedName');
        $qb->where("s.settingType = 'LAB_SERVICE_NAME'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['labServiceName'] = (string)$row['labServiceName'];
            $data[$key]['breedName'] = (string)$row['breedName'];
        }
        return $data;
    }


    public function chickLifeCycleInProgress($parameters)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->from(ChickLifeCycleDetails::class, 'chickLifeCycleDetails');
        $qb->join('chickLifeCycleDetails.crmChickLifeCycle', 'chickLifeCycle');
        $qb->join('chickLifeCycle.customer', 'customer');
        $qb->join('chickLifeCycle.employee', 'employee');
        $qb->join('chickLifeCycle.report', 'report');
        $qb->select('chickLifeCycleDetails');

        $qb->where("chickLifeCycle.lifeCycleState = 'IN_PROGRESS'");
        $qb->andWhere('customer.id = :customerId')->setParameter('customerId', $parameters['customer_id']);
        $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $parameters['employee_id']);
        $qb->andWhere('report.id = :reportId')->setParameter('reportId', $parameters['report_id']);

        return $qb->getQuery()->getArrayResult();

    }

    public function layerLifeCycleInProgress($parameters)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->from(LayerLifeCycleDetails::class, 'layerLifeCycleDetails');
        $qb->join('layerLifeCycleDetails.crmLayerLifeCycle', 'layerLifeCycle');
        $qb->join('layerLifeCycle.customer', 'customer');
        $qb->join('layerLifeCycle.employee', 'employee');
        $qb->join('layerLifeCycle.report', 'report');
        $qb->select('layerLifeCycleDetails');

        $qb->where("layerLifeCycle.lifeCycleState = 'IN_PROGRESS'");
        $qb->andWhere('customer.id = :customerId')->setParameter('customerId', $parameters['customer_id']);
        $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $parameters['employee_id']);
        $qb->andWhere('report.id = :reportId')->setParameter('reportId', $parameters['report_id']);

        return $qb->getQuery()->getArrayResult();

    }

    public function getEmployeeLocation(User $lineManager, $ids)
    {
        $rolesString = implode('_', $lineManager->getRoles());

        $qb = $this->_em->createQueryBuilder();
        $qb->from(User::class, 'u');
        $qb->join('u.userGroup', 'userGroup');
        $qb->select('u.id', 'u.name', 'u.mobile', 'u.userId', 'u.latitude', 'u.longitude');
        $qb->where('u.enabled = 1');
        $qb->andWhere('u.latitude IS NOT NULL');
        $qb->andWhere('u.longitude IS NOT NULL');
        $qb->andWhere('userGroup.slug = :groupSlug')->setParameter('groupSlug', 'employee');

        if ($ids) {
            $qb->andWhere('u.id IN (:ids)')->setParameter('ids', $ids);
        } else {
            $userRole = [];
            if (str_contains($rolesString, 'ROLE_CRM_POULTRY_ADMIN')) {
                array_push($userRole, 'ROLE_CRM_POULTRY_USER');
            }
            if (str_contains($rolesString, 'ROLE_CRM_CATTLE_ADMIN')) {
                array_push($userRole, 'ROLE_CRM_CATTLE_USER');
            }
            if (str_contains($rolesString, 'ROLE_CRM_AQUA_ADMIN')) {
                array_push($userRole, 'ROLE_CRM_AQUA_USER');
            }
            if (str_contains($rolesString, 'ROLE_CRM_SALES_MARKETING_ADMIN')) {
                array_push($userRole, 'ROLE_CRM_SALES_MARKETING_USER');
            }
            $query = '';
            foreach ($userRole as $key => $role) {
                if ($key != 0) {
                    $query .= " OR ";
                }

                $query .= "u.roles LIKE '%" . $role . "%'";
            }

            $qb->andWhere($query);
        }

        return $qb->getQuery()->getArrayResult();

    }


    /**
     * Product Name
     */
    public function productName()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->leftJoin('s.parent', 'p');
        $qb->leftJoin('p.parent', 'pp');


        $qb->select('s.id as id', 's.name as productName');
        $qb->addSelect('p.name as feedName');
        $qb->addSelect('pp.name as parentName');

        $qb->where("s.settingType = 'PRODUCT_NAME'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {

            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['feedTypeName'] = (string)$row['productName'];
            $data[$key]['feedName'] = (string)$row['feedName'];
            $data[$key]['parentName'] = (string)$row['parentName'];

        }

        return $data;
    }


    public function getUnprocessData(User $loggedUser)
    {
        $employeeRoles = [];
        if (in_array('ROLE_CRM_POULTRY_ADMIN', $loggedUser->getRoles())) {
            array_push($employeeRoles, 'ROLE_CRM_POULTRY_USER');
        }
        if (in_array('ROLE_CRM_CATTLE_ADMIN', $loggedUser->getRoles())) {
            array_push($employeeRoles, 'ROLE_CRM_CATTLE_USER');
        }
        if (in_array('ROLE_CRM_AQUA_ADMIN', $loggedUser->getRoles())) {
            array_push($employeeRoles, 'ROLE_CRM_AQUA_USER');
        }
        if (in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $loggedUser->getRoles())) {
            array_push($employeeRoles, 'ROLE_CRM_SALES_MARKETING_USER');
        }


        $qb = $this->createQueryBuilder('e');

        $qb->join('e.employee', 'employee');
        
        $qb->select('e');

        $qb->where('e.status = false');
        $qb->orderBy('e.createdAt', 'DESC');

        if ($employeeRoles) {
            $query = '';
            foreach ($employeeRoles as $key => $role) {
                if ($key !== 0) {
                    $query .= " OR ";
                }
                $query .= "employee.roles LIKE '%" . $role . "%'";

            }
            $qb->andWhere($query);

            return $qb->getQuery()->getResult();

        }
    }


    /**
     * Challenge Name
     */
    public function challengeName()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->select('s.id as id', 's.name as challengeName');
        $qb->where("s.settingType = 'CHALLENGE_NAME'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['challengeName'];
        }
        return $data;
    }

    /**
     * AREA
     */
    public function area()
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class, 's');
        $qb->select('s.id as id', 's.name');
        $qb->where("s.settingType = 'AREA'");
        $qb->andWhere('s.status = 1');

        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach ($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
        }
        return $data;
    }
}