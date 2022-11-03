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
     * @ORM\ManyToOne(targetEntity="Api" , inversedBy="crmFishLifeCycle")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $appBatch;

    /**
     * @var $appId
     * @ORM\Column(type="integer", nullable=true)
     */
    private $appId;

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
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetails")
     * @ORM\JoinColumn(name="feed_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedType;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetails")
     * @ORM\JoinColumn(name="mainCultureSpecies", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $mainCultureSpecies;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetails")
     * @ORM\JoinColumn(name="otherCultureSpecies", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $otherCultureSpecies;


    /**
     * @var \DateTime
     * @ORM\Column(name="reporting_date", type="date", nullable=true)
     */
    private $reportingDate;

    /**
     * @var string
     * @Orm\Column(name="feed_item_name", type="text", nullable=true)
     */

    private $feedItemName;

    /**
     * @var string
     *noOfFinal
     * @ORM\Column(name="culture_area_decimal", type="string", nullable=true)
     */

    private $cultureAreaDecimal;

    /**
     * @var float
     * @Orm\Column(name="no_of_initial_fish", type="float")
     */
    private $noOfInitialFish=0;

    /**
     * @var float
     * @Orm\Column(type="float")
     */
    private $noOfFinalFish=0;

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
     * @Orm\Column(name="total_day_of_culture", type="float")
     */
    private $totalDayOfCulture=0;

    /**
     * @var float
     * @Orm\Column(name="average_present_weight", type="float")
     */
    private $averagePresentWeight=0;

    /**
     * @var float
     * @Orm\Column(name="current_sr_percentage", type="float")
     */
    private $currentSrPercentage=0; //Current Survival Rate (C_SR)

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
     * @Orm\Column( type="float")
     */

    private $previousFinalWeightGm=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */

    private $finalAverageWeightGm=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */

    private $finalWeightGm=0;

    /**
     * @var float
     * @Orm\Column(type="float")
     */

    private $finalWeightKg=0;

    /**
     * @var float
     * @Orm\Column(name="current_feed_consumption_kg", type="float")
     */
    private $currentFeedConsumptionKg=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $previousTotalFeedConsumptionKg=0;

    /**
     * @var float
     * @Orm\Column(name="total_feed_consumption_kg", type="float")
     */
    private $totalFeedConsumptionKg=0;

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
     * @var float
     * @Orm\Column(name="final_fcr", type="float")
     */
    private $finalFcr=0;

    /**
     * @var float
     * @Orm\Column(name="final_adg", type="float")
     */
    private $finalAdg=0;

    /**
     * @var float
     * @Orm\Column(type="float")
     */
    private $srPercentage=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $perPcsSeedCost=0;

    /**
     * @var float
     * @Orm\Column(name="total_seed_cost", type="float")
     */
    private $totalSeedCost=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $perKgFeedRate=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $totalFeedCost=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $feedCostPerKgFish=0;


    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $totalOtherCost=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $totalCost=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $productionCostPerKgFish=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $salesPricePerKg=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $totalIncome=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $netProfitOrLoss=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $retuneOverInvestment=0;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetails")
     * @ORM\JoinColumn(name="hatchery_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $hatchery;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetails")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var \DateTime
     * @ORM\Column(name="stocking_date", type="date", nullable=true)
     */
    private $stockingDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="harvest_date", type="date", nullable=true)
     */
    private $harvestDate;

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
     * @var string
     * @Orm\Column(name="species_description", type="text", nullable=true)
     */
    private $speciesDescription;

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
     * @return mixed
     */
    public function getAppBatch()
    {
        return $this->appBatch;
    }

    /**
     * @param mixed $appBatch
     */
    public function setAppBatch($appBatch): void
    {
        $this->appBatch = $appBatch;
    }

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     */
    public function setAppId($appId): void
    {
        $this->appId = $appId;
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

    public function getFormattedFeedItemName(){
        $returnData = '';
        if($this->feedItemName){

            $feedItemNameJsonDate = (array)json_decode($this->feedItemName, true);
            $x = 1;
            $length = sizeof($feedItemNameJsonDate);
            if($feedItemNameJsonDate && sizeof($feedItemNameJsonDate)>0){
                foreach ($feedItemNameJsonDate as $item) {
                    $returnData.= $item['name'];
                    if($length!=$x){
                        $returnData.=' + ';
                    }
                    $x++;
                }
            }
        }
        return $returnData;

    }
    public function getFeedItemNameIds(){
        $returnData = [];
        if($this->feedItemName){

            $feedItemNameJsonDate = (array)json_decode($this->feedItemName, true);
            $x = 1;
            $length = sizeof($feedItemNameJsonDate);
            if($feedItemNameJsonDate && sizeof($feedItemNameJsonDate)>0){
                foreach ($feedItemNameJsonDate as $item) {
                    $returnData[]= $item['id'];
                }
            }
        }
        return $returnData;

    }

    /**
     * @return Setting
     */
    public function getOtherCultureSpecies()
    {
        return $this->otherCultureSpecies;
    }

    /**
     * @param Setting $otherCultureSpecies
     */
    public function setOtherCultureSpecies($otherCultureSpecies): void
    {
        $this->otherCultureSpecies = $otherCultureSpecies;
    }

    /**
     * @return string
     */
    public function getCultureAreaDecimal()
    {
        return $this->cultureAreaDecimal;
    }

    /**
     * @param string $cultureAreaDecimal
     */
    public function setCultureAreaDecimal(string $cultureAreaDecimal)
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

    public function calculateTotalInitialWeight(){
        return ($this->noOfInitialFish*$this->averageInitialWeight)/1000;
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
        if($this->getPresentSamplingDate() && $this->getPreviousSamplingDate()){
            $presentDate = strtotime($this->getPresentSamplingDate()->format('Y-m-d'));
            $previousDate = strtotime($this->getPreviousSamplingDate()->format('Y-m-d'));
            $dateDiff = $presentDate - $previousDate;

            return round($dateDiff / (60 * 60 * 24));
        }
        return 0;
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
    public function getCurrentSrPercentage()
    {
        return $this->currentSrPercentage;
    }

    /**
     * @param float $currentSrPercentage
     */
    public function setCurrentSrPercentage($currentSrPercentage): void
    {
        $this->currentSrPercentage = $currentSrPercentage;
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
        $noOfFish=0;
        if($this->getCurrentSrPercentage()&&$this->getCurrentSrPercentage()>0){
            $noOfFish= (($this->getNoOfInitialFish()*$this->getCurrentSrPercentage())/100);
        }else{
            $noOfFish=$this->getNoOfInitialFish();
        }

        return ((($this->getAveragePresentWeight()-$this->getAverageInitialWeight())*$noOfFish)/1000);
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
    public function getPreviousTotalFeedConsumptionKg()
    {
        return $this->previousTotalFeedConsumptionKg;
    }

    /**
     * @param float $previousTotalFeedConsumptionKg
     */
    public function setPreviousTotalFeedConsumptionKg(float $previousTotalFeedConsumptionKg)
    {
        $this->previousTotalFeedConsumptionKg = $previousTotalFeedConsumptionKg;
    }

    /**
     * @return float
     */
    public function getTotalFeedConsumptionKg()
    {
        return $this->totalFeedConsumptionKg;
    }

    /**
     * @param float $totalFeedConsumptionKg
     */
    public function setTotalFeedConsumptionKg(float $totalFeedConsumptionKg)
    {
        $this->totalFeedConsumptionKg = $totalFeedConsumptionKg;
    }
    
    public function calculateTotalFeedConsumptionKg(){
        return $this->getPreviousTotalFeedConsumptionKg()+ $this->getCurrentFeedConsumptionKg();
    }

    /**
     * @return float
     */
    public function getCurrentFcr()
    {
        return number_format($this->currentFcr,2,'.','');
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
        return number_format($this->currentAdg,3,'.','');
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
            $returnResult = $this->getWeightGainGm()/$this->calculateCurrentCultureDays();
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
    public function setMainCultureSpecies($mainCultureSpecies): void
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
    public function setFeedType($feedType): void
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
    public function setStockingDate( $stockingDate)
    {
        $this->stockingDate = $stockingDate;
    }

    /**
     * @return \DateTime
     */
    public function getHarvestDate()
    {
        return $this->harvestDate;
    }

    /**
     * @param \DateTime $harvestDate
     */
    public function setHarvestDate($harvestDate)
    {
        $this->harvestDate = $harvestDate;
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
    public function setPreviousSamplingDate($previousSamplingDate)
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
    public function setPresentSamplingDate($presentSamplingDate)
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

    /**
     * @return float
     */
    public function getNoOfFinalFish()
    {
        return $this->noOfFinalFish;
    }

    /**
     * @param float $noOfFinalFish
     */
    public function setNoOfFinalFish(float $noOfFinalFish)
    {
        $this->noOfFinalFish = $noOfFinalFish;
    }

    public function calculateNoOfFinalFish(){
        $returnResult = 0;
        if($this->getNoOfInitialFish()>0){
            $noOfFish=0;
            if($this->getSrPercentage()&&$this->getSrPercentage()>0){
                $srPer = $this->getSrPercentage();
                $returnResult = ($this->getNoOfInitialFish()*$srPer)/100;
            }else{
                $returnResult=$this->getNoOfInitialFish();
            }
            
        }

        return $returnResult;
    }

    /**
     * @return float
     */
    public function getTotalDayOfCulture()
    {
        return $this->totalDayOfCulture;
    }

    /**
     * @param float $totalDayOfCulture
     */
    public function setTotalDayOfCulture(float $totalDayOfCulture)
    {
        $this->totalDayOfCulture = $totalDayOfCulture;
    }

    public function calculateTotalDayOfCulture(){
        if($this->getPresentSamplingDate() && $this->getStockingDate() ){
            $presentDate = strtotime($this->getPresentSamplingDate()->format('Y-m-d'));
            $stockingDate = strtotime($this->getStockingDate()->format('Y-m-d'));
            $dateDiff = $presentDate - $stockingDate;

            return round($dateDiff / (60 * 60 * 24));
        }
        return 0;
    }

    public function calculateDayOfCultureForAfterSale(){
        if($this->getHarvestDate() && $this->getStockingDate() ){
            $harvestDate = strtotime($this->getHarvestDate()->format('Y-m-d'));
            $stockingDate = strtotime($this->getStockingDate()->format('Y-m-d'));
            $dateDiff = $harvestDate - $stockingDate;

            return round($dateDiff / (60 * 60 * 24));
        }
        return 0;
    }

    /**
     * @return float
     */
    public function getPreviousFinalWeightGm()
    {
        return $this->previousFinalWeightGm;
    }

    /**
     * @param float $previousFinalWeightGm
     */
    public function setPreviousFinalWeightGm(float $previousFinalWeightGm)
    {
        $this->previousFinalWeightGm = $previousFinalWeightGm;
    }

    /**
     * @return float
     */
    public function getFinalAverageWeightGm()
    {
        return $this->finalAverageWeightGm;
    }

    /**
     * @param float $finalAverageWeightGm
     */
    public function setFinalAverageWeightGm(float $finalAverageWeightGm): void
    {
        $this->finalAverageWeightGm = $finalAverageWeightGm;
    }

    /**
     * @return float
     */
    public function getFinalWeightGm()
    {
        return $this->finalWeightGm;
    }

    /**
     * @param float $finalWeightGm
     */
    public function setFinalWeightGm(float $finalWeightGm)
    {
        $this->finalWeightGm = $finalWeightGm;
    }

    public function calculateFinalWeightGm(){
        return $this->getFinalAverageWeightGm()-$this->getAverageInitialWeight();
    }

    /**
     * @return float
     */
    public function getFinalWeightKg()
    {
        return $this->finalWeightKg;
    }

    /**
     * @param float $finalWeightKg
     */
    public function setFinalWeightKg(float $finalWeightKg)
    {
        $this->finalWeightKg = $finalWeightKg;
    }

    public function calculateFinalWeightKg(){
       return ($this->getNoOfFinalFish()*$this->getFinalWeightGm())/1000;
    }

    public function calculateFinalWeightKgForHarvest(){
       return ($this->getNoOfFinalFish()*$this->getFinalAverageWeightGm())/1000;
    }

    /**
     * @return float
     */
    public function getFinalFcr()
    {
        return number_format($this->finalFcr,2,'.','');
    }

    /**
     * @param float $finalFcr
     */
    public function setFinalFcr(float $finalFcr)
    {
        $this->finalFcr = $finalFcr;
    }
    
    public function calculateFinalFcr(){
        $returnResult = 0;
        if($this->calculateFinalWeightKg()>0){
            $returnResult = $this->calculateTotalFeedConsumptionKg()/$this->calculateFinalWeightKg();
        }

        return $returnResult;
    }


    public function calculateFinalFcrForAfterSale(){
        $returnResult = 0;
        $finalWeightKg=$this->getFinalWeightKg()-$this->getTotalInitialWeight();
        if($finalWeightKg>0){
            $returnResult = $this->getTotalFeedConsumptionKg()/$finalWeightKg;
        }

        return $returnResult;
    }

    /**
     * @return float
     */
    public function getFinalAdg()
    {
        return number_format($this->finalAdg,2,'.','');
    }

    /**
     * @param float $finalAdg
     */
    public function setFinalAdg(float $finalAdg)
    {
        $this->finalAdg = $finalAdg;
    }

    public function calculateFinalAdg(){
        $returnResult = 0;
        if($this->calculateTotalDayOfCulture()>0){
            $returnResult = $this->calculateFinalWeightGm()/$this->calculateTotalDayOfCulture();
        }

        return $returnResult;
    }

    public function calculateFinalAdgForAfterSale(){
        $returnResult = 0;
        if($this->calculateDayOfCultureForAfterSale()>0){
            $returnResult = ($this->getFinalAverageWeightGm()-$this->getAverageInitialWeight())/$this->calculateDayOfCultureForAfterSale();
        }

        return $returnResult;
    }

    /**
     * @return float
     */
    public function getSrPercentage()
    {
        return number_format($this->srPercentage,4,'.','');
    }

    /**
     * @param float $srPercentage
     */
    public function setSrPercentage(float $srPercentage)
    {
        $this->srPercentage = $srPercentage;
    }

    public function calculateSrPercentageForHarvest(){
        $returnResult = 0;
        if($this->getNoOfInitialFish()>0){
            $returnResult = ($this->getNoOfFinalFish()*100)/$this->getNoOfInitialFish();
        }
        return $returnResult;
    }

    /**
     * @return float
     */
    public function getPerPcsSeedCost()
    {
        return $this->perPcsSeedCost;
    }

    /**
     * @param float $perPcsSeedCost
     */
    public function setPerPcsSeedCost(float $perPcsSeedCost): void
    {
        $this->perPcsSeedCost = $perPcsSeedCost;
    }

    /**
     * @return float
     */
    public function getTotalSeedCost()
    {
        return $this->totalSeedCost;
    }

    /**
     * @param float $totalSeedCost
     */
    public function setTotalSeedCost(float $totalSeedCost): void
    {
        $this->totalSeedCost = $totalSeedCost;
    }

    public function calculateTotalSeedCost(){
        return $this->getNoOfInitialFish()*$this->getPerPcsSeedCost();
    }

    /**
     * @return float
     */
    public function getPerKgFeedRate()
    {
        return $this->perKgFeedRate;
    }

    /**
     * @param float $perKgFeedRate
     */
    public function setPerKgFeedRate(float $perKgFeedRate): void
    {
        $this->perKgFeedRate = $perKgFeedRate;
    }

    /**
     * @return float
     */
    public function getTotalFeedCost()
    {
        return $this->totalFeedCost;
    }

    /**
     * @param float $totalFeedCost
     */
    public function setTotalFeedCost(float $totalFeedCost): void
    {
        $this->totalFeedCost = $totalFeedCost;
    }

    public function calculateTotalFeedCost()
    {
        return $this->getTotalFeedConsumptionKg()*$this->getPerKgFeedRate();
    }

    /**
     * @return float
     */
    public function getFeedCostPerKgFish()
    {
        return $this->feedCostPerKgFish;
    }

    /**
     * @param float $feedCostPerKgFish
     */
    public function setFeedCostPerKgFish(float $feedCostPerKgFish): void
    {
        $this->feedCostPerKgFish = $feedCostPerKgFish;
    }

    public function calculateFeedCostPerKgFish()
    {
        $result=0;
        $finalWeightKg=$this->getFinalWeightKg()-$this->getTotalInitialWeight();
        if($finalWeightKg>0){
            $result = $this->getTotalFeedCost()/$finalWeightKg;
        }
        return $result;
    }

    /**
     * @return float
     */
    public function getTotalOtherCost()
    {
        return $this->totalOtherCost;
    }

    /**
     * @param float $totalOtherCost
     */
    public function setTotalOtherCost(float $totalOtherCost): void
    {
        $this->totalOtherCost = $totalOtherCost;
    }

    /**
     * @return float
     */
    public function getTotalCost()
    {
        return $this->totalCost;
    }

    /**
     * @param float $totalCost
     */
    public function setTotalCost(float $totalCost): void
    {
        $this->totalCost = $totalCost;
    }

    public function calculateTotalCost(){
        return $this->getTotalFeedCost()+$this->getTotalSeedCost()+$this->getTotalOtherCost();
    }

    /**
     * @return float
     */
    public function getProductionCostPerKgFish()
    {
        return $this->productionCostPerKgFish;
    }

    /**
     * @param float $productionCostPerKgFish
     */
    public function setProductionCostPerKgFish(float $productionCostPerKgFish): void
    {
        $this->productionCostPerKgFish = $productionCostPerKgFish;
    }


    public function calculateProductionCostPerKgFish()
    {
        $result=0;
        if($this->getFinalWeightKg()>0){
            $result = $this->calculateTotalCost()/$this->getFinalWeightKg();
        }
        return $result;
    }

    /**
     * @return float
     */
    public function getSalesPricePerKg()
    {
        return $this->salesPricePerKg;
    }

    /**
     * @param float $salesPricePerKg
     */
    public function setSalesPricePerKg(float $salesPricePerKg): void
    {
        $this->salesPricePerKg = $salesPricePerKg;
    }

    /**
     * @return float
     */
    public function getTotalIncome()
    {
        return $this->totalIncome;
    }

    /**
     * @param float $totalIncome
     */
    public function setTotalIncome(float $totalIncome): void
    {
        $this->totalIncome = $totalIncome;
    }

    public function calculateTotalIncome()
    {
        $result = $this->getFinalWeightKg()*$this->getSalesPricePerKg();
        return $result;
    }

    /**
     * @return float
     */
    public function getNetProfitOrLoss()
    {
        return $this->netProfitOrLoss;
    }

    /**
     * @param float $netProfitOrLoss
     */
    public function setNetProfitOrLoss(float $netProfitOrLoss): void
    {
        $this->netProfitOrLoss = $netProfitOrLoss;
    }

    public function calculateNetProfitOrLoss()
    {
        return $this->calculateTotalIncome()-$this->calculateTotalCost();
    }

    /**
     * @return float
     */
    public function getRetuneOverInvestment()
    {
        return $this->retuneOverInvestment;
    }

    /**
     * @param float $retuneOverInvestment
     */
    public function setRetuneOverInvestment(float $retuneOverInvestment): void
    {
        $this->retuneOverInvestment = $retuneOverInvestment;
    }
    
    public function calculateRetuneOverInvestment(){
        $result=0;
        if($this->calculateTotalCost()>0){
            $result = ($this->calculateNetProfitOrLoss()*100)/$this->calculateTotalCost();
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getSpeciesDescription()
    {
        return $this->speciesDescription;
    }

    /**
     * @param string $speciesDescription
     */
    public function setSpeciesDescription($speciesDescription): void
    {
        $this->speciesDescription = $speciesDescription;
    }   
    

}
