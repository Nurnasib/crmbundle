<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fish_life_cycle_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FishLifeCycleDetailsRepository")
 */
class FishLifeCycleDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var FishLifeCycle
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\FishLifeCycle", inversedBy="fishLifeCycleDetails")
     * @ORM\JoinColumn(name="fish_life_cycle_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $fishLifeCycle;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="fishLifeCycleDetails")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer", inversedBy="fishLifeCycleDetails")
     */
    private $customer;

    /**
     * @var \DateTime
     * @ORM\Column(name="reporting_date", type="date", nullable=true)
     */
    private $reportingDate;

    /**
     * @var string
     * @Orm\Column(name="feed_item_name", type="string", nullable=true)
     */

    private $feedItemName;

    /**
     * @var string
     * @Orm\Column(name="other_culture_species", type="string", nullable=true)
     */
    private $otherCultureSpecies;

    /**
     * @var float
     *
     * @ORM\Column(name="culture_area_decimal", type="float")
     */

    private $cultureAreaDecimal=0;

    /**
     * @var float
     * @Orm\Column(name="no_of_initial_fish", type="float")
     */
    private $noOfInitialFish=0;

    /**
     * @var float
     * @Orm\Column(name="stocking_density", type="float")
     */

    private $stockingDensity=0;

    /**
     * @var float
     * @Orm\Column(name="average_initial_weight", type="float")
     */
    private $averageInitialWeight=0;

    /**
     * @var float
     * @Orm\Column(name="total_initial_weight", type="float")
     */
    private $totalInitialWeight=0;

    /**
     * @var float
     * @Orm\Column(name="current_culture_days", type="float")
     */
    private $currentCultureDays=0;

    /**
     * @var float
     * @Orm\Column(name="average_present_weight", type="float")
     */
    private $averagePresentWeight=0;

    /**
     * @var float
     * @Orm\Column(name="weightGainGm", type="float")
     */

    private $weightGainGm=0;

    /**
     * @var float
     * @Orm\Column(name="weightGainKg", type="float")
     */

    private $weightGainKg=0;

    /**
     * @var float
     * @Orm\Column(name="current_feed_consumption_kg", type="float")
     */
    private $currentFeedConsumptionKg=0;

    /**
     * @var float
     * @Orm\Column(name="current_fcr", type="float")
     */
    private $currentFcr=0;

    /**
     * @var float
     * @Orm\Column(name="current_adg", type="float")
     */
    private $currentAdg=0;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetails")
     * @ORM\JoinColumn(name="hatchery_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $hatchery;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetails")
     * @ORM\JoinColumn(name="mainCultureSpecies", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $mainCultureSpecies;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetails")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetails")
     * @ORM\JoinColumn(name="feed_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedType;

    /**
     * @var \DateTime
     * @ORM\Column(name="stocking_date", type="date", nullable=true)
     */

    private $stockingDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="previous_sampling_date", type="date", nullable=true)
     */
    private $previousSamplingDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="present_sampling_date", type="date", nullable=true)
     */
    private $presentSamplingDate;

    /**
     * @var string
     * @Orm\Column(name="farmer_remarks", type="text", nullable=true)
     */
    private $farmerRemarks;

    /**
     * @var string
     * @Orm\Column(name="employee_remarks", type="text", nullable=true)
     */
    private $employeeRemarks;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

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
     * @return FishLifeCycle
     */
    public function getFishLifeCycle()
    {
        return $this->fishLifeCycle;
    }

    /**
     * @param FishLifeCycle $fishLifeCycle
     */
    public function setFishLifeCycle($fishLifeCycle)
    {
        $this->fishLifeCycle = $fishLifeCycle;
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
    public function getReportingDate()
    {
        return $this->reportingDate;
    }

    /**
     * @param \DateTime $reportingDate
     */
    public function setReportingDate(\DateTime $reportingDate)
    {
        $this->reportingDate = $reportingDate;
    }

    /**
     * @return string
     */
    public function getFeedItemName()
    {
        return $this->feedItemName;
    }

    /**
     * @param string $feedItemName
     */
    public function setFeedItemName($feedItemName)
    {
        $this->feedItemName = $feedItemName;
    }

    /**
     * @return string
     */
    public function getOtherCultureSpecies()
    {
        return $this->otherCultureSpecies;
    }

    /**
     * @param string $otherCultureSpecies
     */
    public function setOtherCultureSpecies($otherCultureSpecies)
    {
        $this->otherCultureSpecies = $otherCultureSpecies;
    }

    /**
     * @return float
     */
    public function getCultureAreaDecimal()
    {
        return $this->cultureAreaDecimal;
    }

    /**
     * @param float $cultureAreaDecimal
     */
    public function setCultureAreaDecimal(float $cultureAreaDecimal)
    {
        $this->cultureAreaDecimal = $cultureAreaDecimal;
    }

    /**
     * @return float
     */
    public function getNoOfInitialFish()
    {
        return $this->noOfInitialFish;
    }

    /**
     * @param float $noOfInitialFish
     */
    public function setNoOfInitialFish(float $noOfInitialFish)
    {
        $this->noOfInitialFish = $noOfInitialFish;
    }

    /**
     * @return float
     */
    public function getStockingDensity()
    {
        return number_format($this->stockingDensity,2,'.','');
    }

    /**
     * @param float $stockingDensity
     */
    public function setStockingDensity(float $stockingDensity)
    {
        $this->stockingDensity = $stockingDensity;
    }

    public function getCalculateStockingDensity(){
        $returnResult = 0;
        if($this->getCultureAreaDecimal()>0){
            $returnResult = $this->getNoOfInitialFish()/$this->getCultureAreaDecimal();
        }
        return $returnResult;
    }
    /**
     * @return float
     */
    public function getAverageInitialWeight()
    {
        return $this->averageInitialWeight;
    }

    /**
     * @param float $averageInitialWeight
     */
    public function setAverageInitialWeight(float $averageInitialWeight)
    {
        $this->averageInitialWeight = $averageInitialWeight;
    }

    /**
     * @return float
     */
    public function getTotalInitialWeight()
    {
        return $this->totalInitialWeight;
    }

    /**
     * @param float $totalInitialWeight
     */
    public function setTotalInitialWeight(float $totalInitialWeight)
    {
        $this->totalInitialWeight = $totalInitialWeight;
    }

    /**
     * @return float
     */
    public function getCurrentCultureDays()
    {
        return $this->currentCultureDays;
    }

    /**
     * @param float $currentCultureDays
     */
    public function setCurrentCultureDays(float $currentCultureDays)
    {
        $this->currentCultureDays = $currentCultureDays;
    }
    
    public function calculateCurrentCultureDays(){
        $presentDate = strtotime($this->getPresentSamplingDate()->format('Y-m-d'));
        $previousDate = strtotime($this->getPreviousSamplingDate()->format('Y-m-d'));
        $dateDiff = $presentDate - $previousDate;

        return round($dateDiff / (60 * 60 * 24));
    }

    /**
     * @return float
     */
    public function getAveragePresentWeight()
    {
        return $this->averagePresentWeight;
    }

    /**
     * @param float $averagePresentWeight
     */
    public function setAveragePresentWeight(float $averagePresentWeight)
    {
        $this->averagePresentWeight = $averagePresentWeight;
    }

    /**
     * @return float
     */
    public function getWeightGainGm()
    {
        return $this->weightGainGm;
    }

    /**
     * @param float $weightGainGm
     */
    public function setWeightGainGm(float $weightGainGm)
    {
        $this->weightGainGm = $weightGainGm;
    }

    public function calculateWeightGainGm(){
        return ($this->getAveragePresentWeight()-$this->getAverageInitialWeight());
    }

    /**
     * @return float
     */
    public function getWeightGainKg()
    {
        return $this->weightGainKg;
    }

    /**
     * @param float $weightGainKg
     */
    public function setWeightGainKg(float $weightGainKg)
    {
        $this->weightGainKg = $weightGainKg;
    }

    public function calculateWeightGainKg(){
        return ($this->getWeightGainGm()*$this->getNoOfInitialFish())/1000;
    }

    /**
     * @return float
     */
    public function getCurrentFeedConsumptionKg()
    {
        return $this->currentFeedConsumptionKg;
    }

    /**
     * @param float $currentFeedConsumptionKg
     */
    public function setCurrentFeedConsumptionKg(float $currentFeedConsumptionKg)
    {
        $this->currentFeedConsumptionKg = $currentFeedConsumptionKg;
    }

    /**
     * @return float
     */
    public function getCurrentFcr()
    {
        return $this->currentFcr;
    }

    /**
     * @param float $currentFcr
     */
    public function setCurrentFcr(float $currentFcr)
    {
        $this->currentFcr = $currentFcr;
    }

    public function calculateCurrentFcr(){
        $returnResult = 0;
        if($this->calculateWeightGainKg()>0){
            $returnResult = $this->getCurrentFeedConsumptionKg()/$this->calculateWeightGainKg();
        }

        return $returnResult;

    }

    /**
     * @return float
     */
    public function getCurrentAdg()
    {
        return $this->currentAdg;
    }

    /**
     * @param float $currentAdg
     */
    public function setCurrentAdg(float $currentAdg)
    {
        $this->currentAdg = $currentAdg;
    }

    public function calculateCurrentAdg(){

        $returnResult = 0;

        if($this->calculateCurrentCultureDays()>0){
            $returnResult = ($this->getAveragePresentWeight()-$this->getAverageInitialWeight())/$this->calculateCurrentCultureDays();
        }
        return $returnResult;
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
    public function getMainCultureSpecies()
    {
        return $this->mainCultureSpecies;
    }

    /**
     * @param Setting $mainCultureSpecies
     */
    public function setMainCultureSpecies($mainCultureSpecies)
    {
        $this->mainCultureSpecies = $mainCultureSpecies;
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
    public function getStockingDate()
    {
        return $this->stockingDate;
    }

    /**
     * @param \DateTime $stockingDate
     */
    public function setStockingDate(\DateTime $stockingDate)
    {
        $this->stockingDate = $stockingDate;
    }

    /**
     * @return \DateTime
     */
    public function getPreviousSamplingDate()
    {
        return $this->previousSamplingDate;
    }

    /**
     * @param \DateTime $previousSamplingDate
     */
    public function setPreviousSamplingDate(\DateTime $previousSamplingDate)
    {
        $this->previousSamplingDate = $previousSamplingDate;
    }

    /**
     * @return \DateTime
     */
    public function getPresentSamplingDate()
    {
        return $this->presentSamplingDate;
    }

    /**
     * @param \DateTime $presentSamplingDate
     */
    public function setPresentSamplingDate(\DateTime $presentSamplingDate)
    {
        $this->presentSamplingDate = $presentSamplingDate;
    }

    /**
     * @return string
     */
    public function getFarmerRemarks()
    {
        return $this->farmerRemarks;
    }

    /**
     * @param string $farmerRemarks
     */
    public function setFarmerRemarks($farmerRemarks)
    {
        $this->farmerRemarks = $farmerRemarks;
    }

    /**
     * @return string
     */
    public function getEmployeeRemarks()
    {
        return $this->employeeRemarks;
    }

    /**
     * @param string $employeeRemarks
     */
    public function setEmployeeRemarks($employeeRemarks)
    {
        $this->employeeRemarks = $employeeRemarks;
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
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

}
