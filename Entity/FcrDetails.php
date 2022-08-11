<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fcr_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FcrDetailsRepository")
 */
class FcrDetails
{
    const FCR_FEED_BEFORE = 'BEFORE';
    const FCR_FEED_AFTER = 'AFTER';
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fcr")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="fcr")
     */
    private $employee;

    /**
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmVisit" , inversedBy="fcr")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $visit;

    /**
     * @var string
     * @ORM\Column(name="fcr_of_feed", type="string",nullable=true)
     */

    private $fcrOfFeed; //BEFORE OR AFTER

    /**
     * @var \DateTime
     * @ORM\Column(name="reporting_month", type="date", nullable=true)
     */
    private $reportingMonth;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="fcrDetails")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="fcrDetails")
     */
    private $customer;

    /**
     * @var \DateTime
     * @ORM\Column(name="hatching_date", type="date", nullable=true)
     */
    private $hatchingDate;

    /**
     * @var float
     *
     * @ORM\Column(name="total_birds", type="float")
     */

    private $totalBirds=0;

    /**
     * @var float
     * @Orm\Column(name="age_day", type="float")
     */
    private $ageDay=0;

    /**
     * @var float
     * @Orm\Column(name="mortality_pes", type="float")
     */

    private $mortalityPes=0;

    /**
     * @var float
     * @Orm\Column(name="mortality_percent", type="float")
     */

    private $mortalityPercent=0;

    /**
     * @var float
     * @Orm\Column(name="weight_standard", type="float")
     */

    private $weightStandard=0;

    /**
     * @var float
     * @Orm\Column(name="weight", type="float")
     */
    private $weight=0;

    /**
     * @var float
     * @Orm\Column(name="feed_consumption_total_kg", type="float")
     */
    private $feedConsumptionTotalKg=0;

    /**
     * @var float
     * @Orm\Column(name="feed_consumption_per_bird", type="float")
     */
    private $feedConsumptionPerBird=0;

    /**
     * @var float
     * @Orm\Column(type="float")
     */

    private $feedConsumptionStandard=0;

    /**
     * @var float
     * @Orm\Column(name="fcr_without_mortality", type="float")
     */

    private $fcrWithoutMortality=0;

    /**
     * @var float
     * @Orm\Column(name="fcr_with_mortality", type="float")
     */

    private $fcrWithMortality=0;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fcrDetails")
     * @ORM\JoinColumn(name="hatchery_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $hatchery;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fcrDetails")
     * @ORM\JoinColumn(name="breed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $breed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fcrDetails")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fcrDetails")
     * @ORM\JoinColumn(name="feed_mill_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedMill;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fcrDetails")
     * @ORM\JoinColumn(name="feed_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedType;

    /**
     * @var \DateTime
     * @ORM\Column(name="pro_date", type="date", nullable=true)
     */

    private $proDate;

    /**
     * @var string
     * @Orm\Column(name="batch_no", type="string", nullable=true)
     */

    private $batchNo;

    /**
     * @var string
     * @Orm\Column(name="remarks", type="text", nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var Api
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Api", inversedBy="appBatch")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $appBatch;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */

    private $appId;

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
     * @return \DateTime
     */
    public function getHatchingDate()
    {
        return $this->hatchingDate;
    }

    /**
     * @param \DateTime $hatchingDate
     */
    public function setHatchingDate(\DateTime $hatchingDate): void
    {
        $this->hatchingDate = $hatchingDate;
    }

    /**
     * @return Setting
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param Setting $report
     */
    public function setReport(Setting $report): void
    {
        $this->report = $report;
    }

    /**
     * @return User
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param User $employee
     */
    public function setEmployee(User $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return mixed
     */
    public function getVisit()
    {
        return $this->visit;
    }

    /**
     * @param mixed $visit
     */
    public function setVisit($visit): void
    {
        $this->visit = $visit;
    }

    /**
     * @return string
     */
    public function getFcrOfFeed()
    {
        return $this->fcrOfFeed;
    }

    /**
     * @param string $fcrOfFeed
     */
    public function setFcrOfFeed(string $fcrOfFeed): void
    {
        $this->fcrOfFeed = $fcrOfFeed;
    }

    /**
     * @return \DateTime
     */
    public function getReportingMonth()
    {
        return $this->reportingMonth;
    }

    /**
     * @param \DateTime $reportingMonth
     */
    public function setReportingMonth(\DateTime $reportingMonth): void
    {
        $this->reportingMonth = $reportingMonth;
    }

    /**
     * @return float
     */
    public function getTotalBirds()
    {
        return $this->totalBirds;
    }

    /**
     * @param float $totalBirds
     */
    public function setTotalBirds(float $totalBirds): void
    {
        $this->totalBirds = $totalBirds;
    }

    /**
     * @return float
     */
    public function getAgeDay(): float
    {
        return $this->ageDay;
    }

    /**
     * @param float $ageDay
     */
    public function setAgeDay(float $ageDay): void
    {
        $this->ageDay = $ageDay;
    }

    /**
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param string $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return float
     */
    public function getFeedConsumptionTotalKg(): float
    {
        return $this->feedConsumptionTotalKg;
    }

    /**
     * @param float $feedConsumptionTotalKg
     */
    public function setFeedConsumptionTotalKg(float $feedConsumptionTotalKg): void
    {
        $this->feedConsumptionTotalKg = $feedConsumptionTotalKg;
    }

    /**
     * @return float
     */
    public function getFeedConsumptionPerBird(): float
    {
        return $this->feedConsumptionPerBird;
    }

    /**
     * @param float $feedConsumptionPerBird
     */
    public function setFeedConsumptionPerBird(float $feedConsumptionPerBird): void
    {
        $this->feedConsumptionPerBird = $feedConsumptionPerBird;
    }

    public function calculatePerBird(){
        $result = 0;
        if($this->getTotalBirds()>0) {
            $result =  (($this->getFeedConsumptionTotalKg()/$this->getTotalBirds())*1000);
        }
//        return number_format($result,2,'.','');
        return $result;
    }

    /**
     * @return float
     */
    public function getFcrWithoutMortality()
    {
        return $this->fcrWithoutMortality;
    }

    /**
     * @param float $fcrWithoutMortality
     */
    public function setFcrWithoutMortality(float $fcrWithoutMortality): void
    {
        $this->fcrWithoutMortality = $fcrWithoutMortality;
    }

    public function calculateWithoutMortality(){
        $result = 0;
        if($this->getTotalBirds()>0 && $this->getWeight()>0) {

            $result = (($this->getFeedConsumptionTotalKg() / $this->getTotalBirds()) / $this->getWeight()) * 1000;
        }
//        return number_format($result,2,'.','');
        return $result;

    }
    /**
     * @return float
     */
    public function getFcrWithMortality(): float
    {
        return $this->fcrWithMortality;
    }

    /**
     * @param float $fcrWithMortality
     */
    public function setFcrWithMortality(float $fcrWithMortality): void
    {
        $this->fcrWithMortality = $fcrWithMortality;
    }

    public function calculateWithMortality(){
        $result = 0;
        if($this->getTotalBirds()>0 && $this->getWeight()>0){

            $result = (($this->getFeedConsumptionTotalKg()/($this->getTotalBirds()-$this->getMortalityPes()))/$this->getWeight())*1000;
        }

//        return number_format($result,2,'.','');
        return $result;

    }

    /**
     * @return Setting
     */
    public function getFeedMill()
    {
        return $this->feedMill;
    }

    /**
     * @param Setting $feedMill
     */
    public function setFeedMill($feedMill)
    {
        $this->feedMill = $feedMill;
    }

    /**
     * @return Setting
     */
    public function getFeedType()
    {
        return $this->feedType;
    }

    /**
     * @param Setting $feedType
     */
    public function setFeedType($feedType)
    {
        $this->feedType = $feedType;
    }


    /**
     * @return \DateTime
     */
    public function getProDate()
    {
        return $this->proDate;
    }

    /**
     * @param \DateTime $proDate
     */
    public function setProDate(\DateTime $proDate): void
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
    public function setBatchNo(string $batchNo): void
    {
        $this->batchNo = $batchNo;
    }

    /**
     * @return Setting
     */
    public function getHatchery()
    {
        return $this->hatchery;
    }

    /**
     * @param Setting $hatchery
     */
    public function setHatchery($hatchery)
    {
        $this->hatchery = $hatchery;
    }

    /**
     * @return Setting
     */
    public function getBreed()
    {
        return $this->breed;
    }

    /**
     * @param Setting $breed
     */
    public function setBreed($breed)
    {
        $this->breed = $breed;
    }

    /**
     * @return Setting
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param Setting $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
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
     * @return float
     */
    public function getMortalityPes(): float
    {
        return $this->mortalityPes;
    }

    /**
     * @param float $mortalityPes
     */
    public function setMortalityPes(float $mortalityPes): void
    {
        $this->mortalityPes = $mortalityPes;
    }

    /**
     * @return float
     */
    public function getMortalityPercent(): float
    {
        return $this->mortalityPercent;
    }

    /**
     * @param float $mortalityPercent
     */
    public function setMortalityPercent(float $mortalityPercent): void
    {
        $this->mortalityPercent = $mortalityPercent;
    }

    public function calculateMortalityPercent(){
        $return = 0;
        if($this->getTotalBirds()>0){
            $return = ($this->getMortalityPes()*100)/$this->getTotalBirds();
        }
        return $return;
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return float
     */
    public function getWeightStandard()
    {
        return $this->weightStandard;
    }

    /**
     * @param float $weightStandard
     */
    public function setWeightStandard(float $weightStandard): void
    {
        $this->weightStandard = $weightStandard;
    }

    /**
     * @return float
     */
    public function getFeedConsumptionStandard()
    {
        return $this->feedConsumptionStandard;
    }

    /**
     * @param float $feedConsumptionStandard
     */
    public function setFeedConsumptionStandard(float $feedConsumptionStandard): void
    {
        $this->feedConsumptionStandard = $feedConsumptionStandard;
    }

    /**
     * @return Api
     */
    public function getAppBatch()
    {
        return $this->appBatch;
    }

    /**
     * @param Api $appBatch
     */
    public function setAppBatch(Api $appBatch): void
    {
        $this->appBatch = $appBatch;
    }

    /**
     * @return int
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param int $appId
     */
    public function setAppId(int $appId): void
    {
        $this->appId = $appId;
    }

}
