<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * CrmCustomer
 *
 * @ORM\Table(name="crm_chick_life_cycle")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\BroilerLifeCycleRepository")
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
     * @var string
     * @ORM\Column(name="officer_name", type="string",nullable=true)
     */

    private $officerName;


    /**
     * @var string
     * @ORM\Column(name="region", type="string",nullable=true)
     */

    private $region;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="hatching_date", type="datetime")
     */
    private $hatchingDate;

    /**
     * @return \DateTime
     */
    public function getReportingDate()
    {
        return $this->reportingDate;
    }

    /**
     * @param \DateTime $reportingDate
     */
    public function setReportingDate($reportingDate)
    {
        $this->reportingDate = $reportingDate;
    }


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="reportingDate", type="datetime")
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
     * @Orm\Column(name="feedBird", type="text",nullable=true)
     */

    private $feedBird;


    /**
     * @var string
     * @Orm\Column(name="feedStandard", type="text",nullable=true)
     */

    private $feedStandard;



    /**
     * @var string
     * @Orm\Column(name="fcr_with_mortality", type="text",nullable=true)
     */

    private $fcrWithMortality;



    /**
     * @var string
     * @Orm\Column(name="fcr_without_mortality", type="text",nullable=true)
     */

    private $fcrWithOutMortality;


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
    public function getOfficerName()
    {
        return $this->officerName;
    }

    /**
     * @param string $officerName
     */
    public function setOfficerName($officerName)
    {
        $this->officerName = $officerName;
    }

    /**
     * @return \DateTime
     */
    public function getHatchingDate()
    {
        return $this->hatchingDate;
    }

    /**
     * @param \DateTime $hatchingDate
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
    public function getFeedBird()
    {
        return $this->feedBird;
    }

    /**
     * @param string $feedBird
     */
    public function setFeedBird($feedBird)
    {
        $this->feedBird = $feedBird;
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
    public function getFcrWithMortality()
    {
        return $this->fcrWithMortality;
    }

    /**
     * @param string $fcrWithMortality
     */
    public function setFcrWithMortality($fcrWithMortality)
    {
        $this->fcrWithMortality = $fcrWithMortality;
    }

    /**
     * @return string
     */
    public function getFcrWithOutMortality()
    {
        return $this->fcrWithOutMortality;
    }

    /**
     * @param string $fcrWithOutMortality
     */
    public function setFcrWithOutMortality($fcrWithOutMortality)
    {
        $this->fcrWithOutMortality = $fcrWithOutMortality;
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
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
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




}
