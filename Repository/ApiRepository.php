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
use Doctrine\ORM\Query\Expr\Join;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\CattleFarmVisit;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\LayerLifeCycle;
use Terminalbd\CrmBundle\Entity\LayerStandard;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Entity\SonaliStandard;
use Terminalbd\CrmBundle\Repository\BaseRepository;

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

    protected function handleLocationSearchBetween($qb,$data)
    {

        if($data){
            $locations = explode(',',$data);
            $qb->where('e.upozila IN (:upozila)')->setParameter('upozila',$locations);
        }

    }

    public function apiAgent( $terminal, $locations): array
    {

        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Agent::class,'e');
        $qb->Join('e.agentGroup','ag');
        $qb->Join('e.upozila','up');
        $qb->leftJoin('up.parent','dis');
        $qb->select('e.id as id','e.name as name','e.mobile as mobile','e.email as email','e.name as companyName','e.agentId as agentId');
        $qb->addSelect('ag.name as agentGroup');
        $qb->addSelect('up.name as upozila','up.id as upozilaId');
        $qb->addSelect('dis.name as district','dis.id as districtId');
        //   $qb->where('e.terminal = :terminal')->setParameter('terminal',$terminal);
        if($locations){
            $locations = explode(',',$locations);
            $qb->where('e.upozila IN (:upozila)')->setParameter('upozila',$locations);
        }
        $qb->orderBy('e.name', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['agentId'] = (int)$row['agentId'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['mobile'] = (string)$row['mobile'];
            $data[$key]['email'] = (string)$row['email'];
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
     */
    public function customerApi( $terminal,$locations): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(CrmCustomer::class,'e');
        $qb->Join('e.customerGroup','cg');
        $qb->leftJoin('e.agent','ca');
        $qb->Join('e.location','l');
        $qb->Join('l.parent','dis');
        $qb->select('e.id as id','e.name as name','e.mobile as mobile','e.address as address');
        $qb->addSelect('cg.name as customerGroup');
        $qb->addSelect('ca.id as agentId','ca.name as agentName');
        $qb->addSelect('l.name as upozila','l.id as upozilaId');
        $qb->addSelect('dis.name as district','dis.id as districtId');
        if($locations){
            $locations = explode(',',$locations);
            $qb->where('e.location IN (:upozila)')->setParameter('upozila',$locations);
        }
        $qb->orderBy('e.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['mobile'] = (int)$row['mobile'];
            $data[$key]['address'] = (string)$row['address'];
            $data[$key]['customerGroup'] = (string)$row['customerGroup'];
            $data[$key]['agent'] = (string)$row['agentName'];
            $data[$key]['agentId'] = (string)$row['agentId'];
            $data[$key]['upozila'] = (string)$row['upozila'];
            $data[$key]['upozilaId'] = (string)$row['upozilaId'];
            $data[$key]['district'] = (string)$row['district'];
            $data[$key]['districtId'] = (string)$row['districtId'];

        }
        return $data;
    }


    /**
     * BROILER STANDARD
     */
    public function apiBroiler( $terminal, $data = array() ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(BroilerStandard::class,'b');
        $qb->select('b.id as id','b.age as age','b.targetBodyWeight as targetBodyWeight','b.targetFeedConsumption as targetFeedConsumption');
        //    $qb->where('e.terminal = :terminal')->setParameter('terminal',$terminal);
        $qb->orderBy('b.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['age'] = (int)$row['age'];
            $data[$key]['targetBodyWeight'] = (string)$row['targetBodyWeight'];
            $data[$key]['targetFeedConsumption'] = (string)$row['targetFeedConsumption'];
        }
        return $data;
    }

    /**
     * SONALI STANDARD
     */
    public function apiSonali($terminal, $data = array() ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(SonaliStandard::class,'s');
        $qb->select('s.id as id','s.age as age','s.feedIntakePerDay as feedIntakePerDay','s.targetBodyWeight as targetBodyWeight','s.cumulativeFeedIntake as cumulativeFeedIntake');
        //    $qb->where('e.terminal = :terminal')->setParameter('terminal',$terminal);
        $qb->orderBy('s.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $cumulative = (int)$row['cumulativeFeedIntake'];
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['age'] = (int)$row['age'];
            $data[$key]['feedIntakePerDay'] = (int)$row['feedIntakePerDay'];
            $data[$key]['cumulativeFeedIntake'] = (int)$row['cumulativeFeedIntake'];
            $data[$key]['targetBodyWeight'] = (int)$row['targetBodyWeight'];
            $data[$key]['fcr'] = $cumulative/(int)$row['targetBodyWeight'];
        }
        return $data;
    }


    /**
     * Setting Life Cycle
     */
    public function apiLifeCycleSetting($terminal, $data = array() ): array
    {
        $em = $this->_em;
        /*$sql = 'SELECT crm_setting.name, crm_setting_life_cycle.id, crm_setting_life_cycle.number_of_week, crm_setting_life_cycle.status FROM crm_setting INNER JOIN crm_setting_life_cycle ON crm_setting.id = crm_setting_life_cycle.report_id';
        $result = $em->getConnection()->executeQuery($sql)->fetchAll();*/

        //dd($result);
        $qb = $em->createQueryBuilder();
        $qb->from(SettingLifeCycle::class,'slc');
        $qb->Join('slc.report','r');
        $qb->select('slc.id as id, slc.numberOfWeek as numberOfWeek, slc.status as status','r.name as name');
        $qb->orderBy('slc.id', 'ASC');

        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['numberOfWeek'] = (int)$row['numberOfWeek'];
            $data[$key]['status'] = (int)$row['status'];

        }
        return $data;
    }

    /**
     * Setting Life Cycle
     */
    public function apiSetting($terminal, $data = array() ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(Setting::class,'s');
        $qb->leftJoin('s.parent','p');
        $qb->select('s.id as id','s.name as name','s.settingType as settingType','s.slug as slug','s.status as status'/*,'p.settingType as parent'*/,'p.name as parentName'/*,'p.id as parent_id'*/);
        $qb->orderBy('s.settingType', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['settingType'] = (string)$row['settingType'];
            //$data[$key]['parentType'] = (string)$row['parent'];
            $data[$key]['parentName'] = (string)$row['parentName'];
//            $data[$key]['parent_id'] = (int)$row['parent_id'];
            $data[$key]['slug'] = (string)$row['slug'];
            $data[$key]['status'] = (bool)$row['status'];
        }
        return $data;
    }

    /**
     * Layer Standard
     */
    public function apiLayer( $terminal, $data = array() ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(LayerStandard::class,'ls');
        $qb->Join('ls.report','r');
        $qb->select('ls.id as id','ls.age as age', 'r.name as name','ls.targetFeedConsumption as targetFeedConsumption','ls.targetBodyWeight as targetBodyWeight','ls.targetEggProduction as targetEggProduction','ls.targetEggWeight as targetEggWeight');
        //$qb->where('s.name = e.report_id');
        $qb->orderBy('ls.age', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['age'] = (int)$row['age'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['targetFeedConsumption'] = (int)$row['targetFeedConsumption'];
            $data[$key]['targetBodyWeight'] = (int)$row['targetBodyWeight'];
            $data[$key]['targetEggProduction'] = (int)$row['targetEggProduction'];
            $data[$key]['targetEggWeight'] = (int)$row['targetEggWeight'];

        }
        return $data;
    }


    /**
     *  EMPLOYEE
     */
    public function employeeApi( $terminal, $data = array() ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(User::class,'u');
        $qb->Join('u.userGroup','ug');
        $qb->Join('u.designation','ud');
        $qb->leftJoin('u.department','d');
        $qb->select('u.id as id','ug.name as groupName','u.name as name','u.username as username','ud.name as designation','u.email as email','u.mobile as mobile','u.enabled as enabled');
        $qb->addSelect('d.name as department');
        $qb->where('ug.id = 9');
        $qb->orderBy('u.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
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
     *  Farmer Introduce Report Cattle
     */
    public function farmerIntroduceReport( $terminal, $requestData ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(FarmerIntroduceDetails::class,'fid');
        $qb->Join('fid.customer','fn');
        $qb->Join('fid.agent','fa');
        $qb->Join('fid.farmerType','ft');
        $qb->select('fid.createdAt as createdAt','fid.id as id','fid.previousAgentName as previousAgentName','fn.name as farmerName','fa.name as agent','fn.address as address','fn.mobile as mobile','fid.previousFeedName as previousFeedName','fa.address as agentAddress','fid.previousAgentAddress as previousAgentAddress','fid.remarks as remarks','fid.cultureSpeciesItemAndQty as cultureSpeciesItemAndQty');

        $qb->where('ft.slug = :farmerType');
        $qb->setParameter('farmerType', $requestData);
        $qb->orderBy('fid.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        //$sum = 0;
        foreach($result as $key => $row) {
            $data[$key]['createdAt'] = $row['createdAt'];
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
     *  Sonali Life Cycle Report Poultry
     */
    public function sonaliLifeCycleReportPoultry( $terminal, $data = array() ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(ChickLifeCycle::class,'clc');
        $qb->leftJoin('clc.customer','cc');
        $qb->leftJoin('clc.agent','ca');
        $qb->leftJoin('clc.hatchery','ch');
        $qb->leftJoin('clc.breed','cd');
        $qb->leftJoin('clc.feed','cf');
        $qb->leftJoin('clc.crmChickLifeCycleDetails','cclcd');
        $qb->leftJoin('cclcd.feedType','ft');
        $qb->leftJoin('clc.employee','ce');

        $qb->select('clc.id as id','clc.hatchingDate as hatchingDate','clc.totalBirds as totalBirds');

        $qb->addSelect('ca.name as agentName','ca.address as agentAddress');
        $qb->addSelect('cclcd.visitingWeek as visitingWeek','cclcd.ageDays as ageDays','cclcd.mortalityPes as mortalityPes','cclcd.mortalityPercent as mortalityPercent','cclcd.weightStandard as weightStandard','cclcd.weightAchieved as weightAchieved','cclcd.feedTotalKg as feedTotalKg','cclcd.perBird as perBird','cclcd.feedStandard as feedStandard','cclcd.withoutMortality as withoutMortality','cclcd.withMortality as withMortality','cclcd.proDate as proDate','cclcd.batchNo as batchNo','cclcd.remarks as remarks');
        $qb->addSelect('cc.name as name','cc.address as address','cc.mobile as mobile');
        $qb->addSelect('ch.name as hatchery');
        $qb->addSelect('ft.name as feedTypeName');
        $qb->addSelect('cd.name as breed');
        $qb->addSelect('cf.name as feed');

        //$qb->where('cc.id = :employeeId')->setParameter('employeeId', $employeeId);
        //$qb->where('clc.id = 20');
        $qb->where('clc.id = 20');
        $qb->orderBy('clc.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();

        foreach($result as $key => $row) {
            //$data[$key]['id'] = (int)$row['id'];
            $data[$key]['agentName'] = (string)$row['agentName'];
            $data[$key]['agentAddress'] = (string)$row['agentAddress'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['address'] = (string)$row['address'];
            $data[$key]['mobile'] = (string)$row['mobile'];
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
            //$data[$key]['feedTypeName'] = (string)$row['feedTypeName'];
            $data[$key]['proDate'] = $row['proDate']->format('Y-m-d');
            $data[$key]['batchNo'] = (int)$row['batchNo'];
            $data[$key]['remarks'] = (string)$row['remarks'];

        }
        return $data;
    }

    /**
     *  Boiler Life Cycle Report Poultry
     */
    public function boilerLifeCycleReportPoultry( $terminal, $data = array() ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(ChickLifeCycle::class,'clc');
        $qb->leftJoin('clc.customer','cc');
        $qb->leftJoin('clc.agent','ca');
        $qb->leftJoin('clc.hatchery','ch');
        $qb->leftJoin('clc.breed','cd');
        $qb->leftJoin('clc.feed','cf');
        $qb->leftJoin('clc.crmChickLifeCycleDetails','cclcd');
        $qb->leftJoin('cclcd.feedType','ft');
        $qb->leftJoin('clc.employee','ce');

        $qb->select('clc.id as id','clc.hatchingDate as hatchingDate');

        $qb->addSelect('ca.name as agentName','ca.address as agentAddress');
        $qb->addSelect('cclcd.visitingWeek as visitingWeek','cclcd.ageDays as ageDays','cclcd.mortalityPes as mortalityPes','cclcd.mortalityPercent as mortalityPercent','cclcd.weightStandard as weightStandard','cclcd.weightAchieved as weightAchieved','cclcd.feedTotalKg as feedTotalKg','cclcd.perBird as perBird','cclcd.feedStandard as feedStandard','cclcd.withoutMortality as withoutMortality','cclcd.withMortality as withMortality','cclcd.proDate as proDate','cclcd.batchNo as batchNo','cclcd.remarks as remarks');
        $qb->addSelect('cc.name as name','cc.address as address');
        $qb->addSelect('ch.name as hatchery');
        $qb->addSelect('ft.name as feedTypeName');
        $qb->addSelect('cd.name as breed');
        $qb->addSelect('cf.name as feed');

        $qb->where('clc.id = 15');
        $qb->orderBy('clc.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['agentName'] = (string)$row['agentName'];
            $data[$key]['agentAddress'] = (string)$row['agentAddress'];
            $data[$key]['name'] = (string)$row['name'];
            $data[$key]['address'] = (string)$row['address'];
            $data[$key]['hatchingDate'] = $row['hatchingDate'];
            $data[$key]['visitingWeek'] = (int)$row['visitingWeek'];
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
            $data[$key]['feedTypeName'] = (string)$row['feedTypeName'];
            $data[$key]['proDate'] = $row['proDate'];
            $data[$key]['batchNo'] = (int)$row['batchNo'];
            $data[$key]['remarks'] = (string)$row['remarks'];

        }
        return $data;
    }

    /**
     *  Layer Life Cycle Report Poultry
     */
    public function layerLifeCycleReportPoultry( $terminal, $data = array() ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(LayerLifeCycle::class,'llc');
        $qb->leftJoin('llc.crmLayerLifeCycleDetails','lcd');
        $qb->leftJoin('llc.customer','lc');
//        $qb->innerJoin('llc.feedMill','lfm');
//        $qb->innerJoin('llc.feedType','lft');


        $qb->select('llc.id as id','llc.totalBirds as totalBirds');
//        $qb->addselect('lfm.name as feedMill');
//        $qb->addselect('lft.name as feedType');
        $qb->addSelect('lcd.visitingDate as visitingDate','lcd.ageWeek as ageWeek','lcd.deadBird as deadBird','lcd.avgWeight as avgWeight','lcd.targetWeight as targetWeight','lcd.uniformity as uniformity','lcd.feedPerBird as feedPerBird','lcd.targetFeedPerBird as targetFeedPerBird','lcd.totalEggs as totalEggs','lcd.eggProduction as eggProduction','lcd.targetEggProduction as targetEggProduction','lcd.eggWeightActual as eggWeightActual','lcd.eggWeightStandard as eggWeightStandard','lcd.productionDate as productionDate','lcd.batch_no as batch_no');



        $qb->where('llc.id = 8');
        $qb->orderBy('llc.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['visitingDate'] = $row['visitingDate'];
            $data[$key]['ageWeek'] = (int)$row['ageWeek'];
            $data[$key]['totalBirds'] = (int)$row['totalBirds'];
            $data[$key]['deadBird'] = (int)$row['deadBird'];
            $data[$key]['avgWeight'] = (int)$row['avgWeight'];
            $data[$key]['targetWeight'] = (int)$row['targetWeight'];
            $data[$key]['uniformity'] = (int)$row['uniformity'];
            $data[$key]['feedPerBird'] = (int)$row['feedPerBird'];
            $data[$key]['targetFeedPerBird'] = (int)$row['targetFeedPerBird'];
            $data[$key]['totalEggs'] = (int)$row['totalEggs'];
            $data[$key]['eggProduction'] = (int)$row['eggProduction'];
            $data[$key]['targetEggProduction'] = (int)$row['targetEggProduction'];
            $data[$key]['eggWeightActual'] = (int)$row['eggWeightActual'];
            $data[$key]['eggWeightStandard'] = (int)$row['eggWeightStandard'];
            $data[$key]['productionDate'] = $row['productionDate'];
            $data[$key]['batch_no'] = (int)$row['batch_no'];
//            $data[$key]['feedType'] = (int)$row['feedType'];
//            $data[$key]['feedType'] = (int)$row['feedMill'];

        }
        return $data;
    }

    /**
     *  Cattle Farm Visit Life Cycle Report Poultry
     */
    public function farmCattleVisit( $terminal, $data = array() ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(CattleFarmVisit::class,'cfv');
        $qb->leftJoin('cfv.crmCattleFarmVisitDetails','cfvd');
        $qb->leftJoin('cfvd.customer','cfvdc');
        $qb->leftJoin('cfvd.agent','cfvda');
        $qb->leftJoin('cfvda.upozila','cfvdaz');
        $qb->leftJoin('cfvda.district','cfvdad');

        $qb->addselect('cfvd.visitingDate as visitingDate','cfvd.cattlePopulationOx as cattlePopulationOX','cfvd.cattlePopulationCow as cattlePopulationCow','cfvd.cattlePopulationCalf as cattlePopulationCalf','cfvd.avgMilkYieldPerDay as avgMilkYieldPerDay','cfvd.conceptionRate as conceptionRate','cfvd.fodderGreenGrassKg as fodderGreenGrassKg','cfvd.fodderStrawKg as fodderStrawKg','cfvd.typeOfConcentrateFeed as typeOfConcentrateFeed','cfvd.marketPriceMilkPerLiter as marketPriceMilkPerLiter','cfvd.marketPriceMeatPerKg as marketPriceMeatPerKg','cfvd.remarks as comment');
        $qb->addselect('cfvdc.name as customerName','cfvdc.address as customerAddress');
        $qb->addselect('cfvda.phone as agentPhone');
        $qb->addselect('cfvdaz.name as agentUpozila');
        $qb->addselect('cfvdad.name as agentDistrict');


        $qb->orderBy('cfv.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['visitingDate'] = $row['visitingDate'];
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

    /* Zashim123 */

    /**
     * CRM Visit Report
     */

    public function crmVisit( $terminal, $data = array() ): array
    {
        $em = $this->_em;
        $qb = $em->createQueryBuilder();
        $qb->from(CrmVisit::class,'cv');
        $qb->leftJoin('cv.employee','cve');
        $qb->leftJoin('cv.location','cvl');

        $qb->select('cv.id as id','cv.workingDuration as workingDuration');
        $qb->addSelect('cve.name as employeeName');
        $qb->addSelect('cvl.name as areaName');

        $qb->where('cve.id = 21');
        $qb->orderBy('cv.id', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        $data = array();
        foreach($result as $key => $row) {
            $data[$key]['id'] = (int)$row['id'];
            $data[$key]['employeeName'] = (string)$row['employeeName'];
            $data[$key]['workingDuration'] = (string)$row['workingDuration'];
            $data[$key]['areaName'] = (string)$row['areaName'];

        }
        return $data;
    }


}
