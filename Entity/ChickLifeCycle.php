<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(name="crm_chick_life_cycle")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ChickLifeCycleRepository")
 */
class ChickLifeCycle
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="chicklifecycle")
     */
    private $customer;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="chicklifecycle")
     */
    private $agent;

    /**
     * @var string
     * @Orm\Column(name="hatching_date" ,type="string",nullable=true)
     */
    private $hatchingDate;


    /**
     * @var string
     * @Orm\Column(name="reporting_date" ,type="string",nullable=true)
     */
    private $reportingDate;

    /**
     * @var string
     * @ORM\Column(name="visitingweek", type="string",nullable=true)
     */

    private $visitingweek;

    /**
     * @var string
     * @Orm\Column(name="totalbirds" ,type="string",nullable=true)
     */

    private $totalbirds;

    /**
     * @var string
     * @Orm\Column(name="age_days", type="string",nullable=true)
     */
    private $agedays;

    /**
     * @var string
     * @Orm\Column(name="mortality_pes", type="string",nullable=true)
     */

    private $mortalityPes;

    /**
     * @var string
     * @Orm\Column(name="mortality_percent", type="string",nullable=true)
     */

    private $mortalityPercent;

    /**
     * @var string
     * @Orm\Column(name="weightStandard", type="text",nullable=true)
     */

    private $weightStandard;

    /**
     * @var string
     * @Orm\Column(name="weightAchieved", type="text",nullable=true)
     */

    private $weightAchieved;

    /**
     * @var string
     * @Orm\Column(name="feedTotalkg", type="text",nullable=true)
     */

    private $feedTotalkg;

    /**
     * @var string
     * @Orm\Column(name="perBird", type="text",nullable=true)
     */

    private $perBird;

    /**
     * @var string
     * @Orm\Column(name="feedStandard", type="text",nullable=true)
     */

    private $feedStandard;

    /**
     * @var string
     * @Orm\Column(name="withoutMortality", type="text",nullable=true)
     */

    private $withoutMortality;

    /**
     * @var string
     * @Orm\Column(name="withMortality", type="text",nullable=true)
     */

    private $withMortality;

    /**
     * @var string
     * @Orm\Column(name="hatchery", type="text",nullable=true)
     */

    private $hatchery;

    /**
     * @var string
     * @Orm\Column(name="breed", type="text",nullable=true)
     */

    private $breed;


    /**
     * @var string
     * @Orm\Column(name="feed", type="text",nullable=true)
     */

    private $feed;

    /**
     * @var string
     * @Orm\Column(name="feedType", type="text",nullable=true)
     */

    private $feedType;


    /**
     * @var string
     * @Orm\Column(name="proDate", type="text",nullable=true)
     */

    private $proDate;

    /**
     * @var string
     * @Orm\Column(name="batchNo", type="text",nullable=true)
     */

    private $batchNo;

    /**
     * @return SettingType
     */
    public function getBirdMode()
    {
        return $this->birdMode;
    }

    /**
     * @param SettingType $birdMode
     */
    public function setBirdMode($birdMode)
    {
        $this->birdMode = $birdMode;
    }

    /**
     * @var SettingType
     * @ORM\Column(type="string" )
     */
    private $birdMode;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function getHatchingDate()
    {
        return $this->hatchingDate;
    }

    /**
     * @param string $hatchingDate
     */
    public function setHatchingDate($hatchingDate)
    {
        $this->hatchingDate = $hatchingDate;
    }


    /**
     * @return string
     */
    public function getVisitingweek()
    {
        return $this->visitingweek;
    }

    /**
     * @param string $visitingweek
     */
    public function setVisitingweek($visitingweek)
    {
        $this->visitingweek = $visitingweek;
    }

    /**
     * @return string
     */
    public function getTotalbirds()
    {
        return $this->totalbirds;
    }

    /**
     * @param string $totalbirds
     */
    public function setTotalbirds($totalbirds)
    {
        $this->totalbirds = $totalbirds;
    }

    /**
     * @return string
     */
    public function getAgedays()
    {
        return $this->agedays;
    }

    /**
     * @param string $agedays
     */
    public function setAgedays($agedays)
    {
        $this->agedays = $agedays;
    }

    /**
     * @return string
     */
    public function getMortalityPes()
    {
        return $this->mortalityPes;
    }

    /**
     * @param string $mortalityPes
     */
    public function setMortalityPes($mortalityPes)
    {
        $this->mortalityPes = $mortalityPes;
    }

    /**
     * @return string
     */
    public function getWeightStandard()
    {
        return $this->weightStandard;
    }

    /**
     * @param string $weightStandard
     */
    public function setWeightStandard($weightStandard)
    {
        $this->weightStandard = $weightStandard;
    }

    /**
     * @return string
     */
    public function getWeightAchieved()
    {
        return $this->weightAchieved;
    }

    /**
     * @param string $weightAchieved
     */
    public function setWeightAchieved($weightAchieved)
    {
        $this->weightAchieved = $weightAchieved;
    }

    /**
     * @return string
     */
    public function getFeedTotalkg()
    {
        return $this->feedTotalkg;
    }

    /**
     * @param string $feedTotalkg
     */
    public function setFeedTotalkg($feedTotalkg)
    {
        $this->feedTotalkg = $feedTotalkg;
    }

    /**
     * @return string
     */
    public function getReportingDate()
    {
        return $this->reportingDate;
    }

    /**
     * @param string $reportingDate
     */
    public function setReportingDate($reportingDate)
    {
        $this->reportingDate = $reportingDate;
    }



    /**
     * @return string
     */
    public function getFeedStandard()
    {
        return $this->feedStandard;
    }

    /**
     * @param string $feedStandard
     */
    public function setFeedStandard($feedStandard)
    {
        $this->feedStandard = $feedStandard;
    }

    /**
     * @return string
     */
    public function getHatchery()
    {
        return $this->hatchery;
    }

    /**
     * @param string $hatchery
     */
    public function setHatchery($hatchery)
    {
        $this->hatchery = $hatchery;
    }

    /**
     * @return string
     */
    public function getBreed()
    {
        return $this->breed;
    }

    /**
     * @param string $breed
     */
    public function setBreed($breed)
    {
        $this->breed = $breed;
    }

    /**
     * @return string
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param string $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
    }

    /**
     * @return string
     */
    public function getFeedType()
    {
        return $this->feedType;
    }

    /**
     * @param string $feedType
     */
    public function setFeedType($feedType)
    {
        $this->feedType = $feedType;
    }

    /**
     * @return string
     */
    public function getProDate()
    {
        return $this->proDate;
    }

    /**
     * @param string $proDate
     */
    public function setProDate($proDate)
    {
        $this->proDate = $proDate;
    }



    /**
     * @return string
     */
    public function getBatchNo()
    {
        return $this->batchNo;
    }

    /**
     * @param string $batchNo
     */
    public function setBatchNo($batchNo)
    {
        $this->batchNo = $batchNo;
    }

    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param string $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }


    /**
     * @var string
     * @Orm\Column(name="remarks", type="text",nullable=true)
     */

    private $remarks;


    /**
     * @return CrmCustomer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param CrmCustomer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param Agent $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return string
     */
    public function getMortalityPercent()
    {
        return $this->mortalityPercent;
    }

    /**
     * @param string $mortalityPercent
     */
    public function setMortalityPercent($mortalityPercent)
    {
        $this->mortalityPercent = $mortalityPercent;
    }

    /**
     * @return string
     */
    public function getPerBird()
    {
        return $this->perBird;
    }

    /**
     * @param string $perBird
     */
    public function setPerBird($perBird)
    {
        $this->perBird = $perBird;
    }

    /**
     * @return string
     */
    public function getWithoutMortality()
    {
        return $this->withoutMortality;
    }

    /**
     * @return string
     */
    public function getWithMortality()
    {
        return $this->withMortality;
    }

    /**
     * @param string $withoutMortality
     */
    public function setWithoutMortality($withoutMortality)
    {
        $this->withoutMortality = $withoutMortality;
    }

    /**
     * @param string $withMortality
     */
    public function setWithMortality($withMortality)
    {
        $this->withMortality = $withMortality;
    }









}
