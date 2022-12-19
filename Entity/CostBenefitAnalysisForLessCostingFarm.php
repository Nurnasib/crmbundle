<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 *
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CostBenefitAnalysisForLessCostingFarmRepository")
 * @ORM\Table(name="crm_cost_benefit_analysis_for_less_costing_farm",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"employee_id", "customer_id", "report_id", "reporting_month"})}
 *     )
 */
class CostBenefitAnalysisForLessCostingFarm
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="costBenefitAnalysisForLessCostingFarm")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="costBenefitAnalysisForLessCostingFarm")
     * @ORM\JoinColumn(name="report_parent_parent_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $reportParentParent;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="costBenefitAnalysisForLessCostingFarm")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer", inversedBy="costBenefitAnalysisForLessCostingFarm")
     */
    private $customer;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="costBenefitAnalysisForLessCostingFarm")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="costBenefitAnalysisForLessCostingFarm")
     * @ORM\JoinColumn(name="hatchery_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $hatchery;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="costBenefitAnalysisForLessCostingFarm")
     * @ORM\JoinColumn(name="breed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $breed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="antibioticFreeFarm")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="antibioticFreeFarm")
     * @ORM\JoinColumn(name="species_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $species;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $hatchingDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $reportingMonth;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $pondSize;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $fingerlingSize=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $harvestingSize=0;

//    particulars item section start 1-9:
    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $totalStockedChicksPcs=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $totalFeedUsedKg=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $totalBroilerWeightKg=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $mortality=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $ageDays=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $fcr=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $itemPricePerPcs=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $feedPricePerKg=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $broilerOrFishPricePerKg=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $totalMedicineCost=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $totalVaccineCost=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $totalPondPreparationCost=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $usedBagPricePerPcs=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $litterOrPondRentCost=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $electricityAndFuelCost=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $labourCost=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $transportCost=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $otherCost=0;

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
     * @return Setting
     */
    public function getReportParentParent()
    {
        return $this->reportParentParent;
    }

    /**
     * @param Setting $reportParentParent
     */
    public function setReportParentParent($reportParentParent): void
    {
        $this->reportParentParent = $reportParentParent;
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
    public function setAgent(Agent $agent): void
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
    public function setCustomer(CrmCustomer $customer): void
    {
        $this->customer = $customer;
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
     * @return Setting
     */
    public function getHatchery()
    {
        return $this->hatchery;
    }

    /**
     * @param Setting $hatchery
     */
    public function setHatchery(Setting $hatchery): void
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
    public function setBreed(Setting $breed): void
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
    public function setFeed(Setting $feed): void
    {
        $this->feed = $feed;
    }

    /**
     * @return Setting
     */
    public function getSpecies()
    {
        return $this->species;
    }

    /**
     * @param Setting $species
     */
    public function setSpecies(Setting $species): void
    {
        $this->species = $species;
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
     * @return float
     */
    public function getTotalStockedChicksPcs()
    {
        return $this->totalStockedChicksPcs;
    }

    /**
     * @param float $totalStockedChicksPcs
     */
    public function setTotalStockedChicksPcs($totalStockedChicksPcs): void
    {
        $this->totalStockedChicksPcs = $totalStockedChicksPcs;
    }

    /**
     * @return float
     */
    public function getTotalFeedUsedKg()
    {
        return $this->totalFeedUsedKg;
    }

    /**
     * @param float $totalFeedUsedKg
     */
    public function setTotalFeedUsedKg($totalFeedUsedKg): void
    {
        $this->totalFeedUsedKg = $totalFeedUsedKg;
    }

    /**
     * @return float
     */
    public function getTotalBroilerWeightKg()
    {
        return $this->totalBroilerWeightKg;
    }

    /**
     * @param float $totalBroilerWeightKg
     */
    public function setTotalBroilerWeightKg($totalBroilerWeightKg): void
    {
        $this->totalBroilerWeightKg = $totalBroilerWeightKg;
    }

    /**
     * @return float
     */
    public function getMortality()
    {
        return $this->mortality;
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
    public function getAgeDays()
    {
        return $this->ageDays;
    }

    /**
     * @param float $ageDays
     */
    public function setAgeDays($ageDays): void
    {
        $this->ageDays = $ageDays;
    }

    /**
     * @return float
     */
    public function getFcr()
    {
        return $this->fcr;
    }

    /**
     * @param float $fcr
     */
    public function setFcr($fcr): void
    {
        $this->fcr = $fcr;
    }

    public function calculateFcr()
    {
        $returnResult = 0;

        if($this->totalBroilerWeightKg>0){
            $returnResult = $this->totalFeedUsedKg/$this->totalBroilerWeightKg;
        }
        return $returnResult;
    }

    /**
     * @return float
     */
    public function getItemPricePerPcs()
    {
        return $this->itemPricePerPcs;
    }

    /**
     * @param float $itemPricePerPcs
     */
    public function setItemPricePerPcs($itemPricePerPcs): void
    {
        $this->itemPricePerPcs = $itemPricePerPcs;
    }

    /**
     * @return float
     */
    public function getFeedPricePerKg()
    {
        return $this->feedPricePerKg;
    }

    /**
     * @param float $feedPricePerKg
     */
    public function setFeedPricePerKg($feedPricePerKg): void
    {
        $this->feedPricePerKg = $feedPricePerKg;
    }

    /**
     * @return float
     */
    public function getBroilerOrFishPricePerKg()
    {
        return $this->broilerOrFishPricePerKg;
    }

    /**
     * @param float $broilerOrFishPricePerKg
     */
    public function setBroilerOrFishPricePerKg($broilerOrFishPricePerKg): void
    {
        $this->broilerOrFishPricePerKg = $broilerOrFishPricePerKg;
    }

    /**
     * @return float
     */
    public function getTotalMedicineCost()
    {
        return $this->totalMedicineCost;
    }

    /**
     * @param float $totalMedicineCost
     */
    public function setTotalMedicineCost($totalMedicineCost): void
    {
        $this->totalMedicineCost = $totalMedicineCost;
    }

    /**
     * @return float
     */
    public function getTotalVaccineCost()
    {
        return $this->totalVaccineCost;
    }

    /**
     * @param float $totalVaccineCost
     */
    public function setTotalVaccineCost($totalVaccineCost): void
    {
        $this->totalVaccineCost = $totalVaccineCost;
    }

    /**
     * @return float
     */
    public function getTotalPondPreparationCost()
    {
        return $this->totalPondPreparationCost;
    }

    /**
     * @param float $totalPondPreparationCost
     */
    public function setTotalPondPreparationCost($totalPondPreparationCost): void
    {
        $this->totalPondPreparationCost = $totalPondPreparationCost;
    }

    /**
     * @return float
     */
    public function getUsedBagPricePerPcs()
    {
        return $this->usedBagPricePerPcs;
    }

    /**
     * @param float $usedBagPricePerPcs
     */
    public function setUsedBagPricePerPcs($usedBagPricePerPcs): void
    {
        $this->usedBagPricePerPcs = $usedBagPricePerPcs;
    }

    /**
     * @return float
     */
    public function getLitterOrPondRentCost()
    {
        return $this->litterOrPondRentCost;
    }

    /**
     * @param float $litterOrPondRentCost
     */
    public function setLitterOrPondRentCost($litterOrPondRentCost): void
    {
        $this->litterOrPondRentCost = $litterOrPondRentCost;
    }

    /**
     * @return float
     */
    public function getElectricityAndFuelCost()
    {
        return $this->electricityAndFuelCost;
    }

    /**
     * @param float $electricityAndFuelCost
     */
    public function setElectricityAndFuelCost($electricityAndFuelCost): void
    {
        $this->electricityAndFuelCost = $electricityAndFuelCost;
    }

    /**
     * @return float
     */
    public function getLabourCost()
    {
        return $this->labourCost;
    }

    /**
     * @param float $labourCost
     */
    public function setLabourCost($labourCost): void
    {
        $this->labourCost = $labourCost;
    }

    /**
     * @return float
     */
    public function getTransportCost()
    {
        return $this->transportCost;
    }

    /**
     * @param float $transportCost
     */
    public function setTransportCost($transportCost): void
    {
        $this->transportCost = $transportCost;
    }

    /**
     * @return float
     */
    public function getOtherCost()
    {
        return $this->otherCost;
    }

    /**
     * @param float $otherCost
     */
    public function setOtherCost($otherCost): void
    {
        $this->otherCost = $otherCost;
    }

    /**
     * @param float $mortality
     */
    public function setMortality($mortality): void
    {
        $this->mortality = $mortality;
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
    public function setRemarks($remarks): void
    {
        $this->remarks = $remarks;
    }

    /**
     * @return string
     */
    public function getPondSize()
    {
        return $this->pondSize;
    }

    /**
     * @param string $pondSize
     */
    public function setPondSize($pondSize): void
    {
        $this->pondSize = $pondSize;
    }

    /**
     * @return float
     */
    public function getFingerlingSize()
    {
        return $this->fingerlingSize;
    }

    /**
     * @param float $fingerlingSize
     */
    public function setFingerlingSize($fingerlingSize): void
    {
        $this->fingerlingSize = $fingerlingSize;
    }

    /**
     * @return float
     */
    public function getHarvestingSize()
    {
        return $this->harvestingSize;
    }

    /**
     * @param float $harvestingSize
     */
    public function setHarvestingSize($harvestingSize): void
    {
        $this->harvestingSize = $harvestingSize;
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
    
//    calculate section

    public function calculateChickTotalPrice()
    {
        return $this->itemPricePerPcs* $this->totalStockedChicksPcs;
    }

    public function calculateFeedCostUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = ($this->totalFeedUsedKg/$this->totalStockedChicksPcs)*$this->feedPricePerKg;
        }
        return $price;
    }

    public function calculateFeedCostTotalPrice()
    {
        $price = $this->calculateFeedCostUnitPrice()*$this->totalStockedChicksPcs;
        return $price;
    }

    public function calculateMedicineCostUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = $this->totalMedicineCost/$this->totalStockedChicksPcs;
        }
        return $price;
    }

    public function calculateMedicineCostTotalPrice()
    {
        $price = $this->calculateMedicineCostUnitPrice()*$this->totalStockedChicksPcs;
        return $price;
    }

    public function calculateVaccineCostUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = $this->totalVaccineCost/$this->totalStockedChicksPcs;
        }
        return $price;
    }

    public function calculateVaccineCostTotalPrice()
    {
        $price = $this->calculateVaccineCostUnitPrice()*$this->totalStockedChicksPcs;
        return $price;
    }

    public function calculateLabourCostUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = $this->labourCost/$this->totalStockedChicksPcs;
        }
        return $price;
    }

    public function calculateLabourCostTotalPrice()
    {
        $price = $this->calculateLabourCostUnitPrice()*$this->totalStockedChicksPcs;
        return $price;
    }

    public function calculateElectricityCostUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = $this->electricityAndFuelCost/$this->totalStockedChicksPcs;
        }
        return $price;
    }

    public function calculateElectricityCostTotalPrice()
    {
        $price = $this->calculateElectricityCostUnitPrice()*$this->totalStockedChicksPcs;
        return $price;
    }

    public function calculateLitterOrPondRentCostUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = $this->litterOrPondRentCost/$this->totalStockedChicksPcs;
        }
        return $price;
    }

    public function calculateLitterOrPondRentCostTotalPrice()
    {
        $price = $this->calculateLitterOrPondRentCostUnitPrice()*$this->totalStockedChicksPcs;
        return $price;
    }

    public function calculatePondPreparationCostUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = $this->totalPondPreparationCost/$this->totalStockedChicksPcs;
        }
        return $price;
    }

    public function calculatePondPreparationCostTotalPrice()
    {
        $price = $this->calculatePondPreparationCostUnitPrice()*$this->totalStockedChicksPcs;
        return $price;
    }

    public function calculateTransportCostUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = $this->transportCost/$this->totalStockedChicksPcs;
        }
        return $price;
    }

    public function calculateTransportCostTotalPrice()
    {
        $price = $this->calculateTransportCostUnitPrice()*$this->totalStockedChicksPcs;
        return $price;
    }

    public function calculateOtherCostUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = $this->otherCost/$this->totalStockedChicksPcs;
        }
        return $price;
    }

    public function calculateOtherCostTotalPrice()
    {
        $price = $this->calculateOtherCostUnitPrice()*$this->totalStockedChicksPcs;
        return $price;
    }

    public function calculateTotalProductionItemUnitPrice()
    {
        return $this->itemPricePerPcs + $this->calculateElectricityCostUnitPrice()+$this->calculateFeedCostUnitPrice()+$this->calculateLabourCostUnitPrice()+$this->calculateLitterOrPondRentCostUnitPrice()+
            $this->calculateVaccineCostUnitPrice()+$this->calculateMedicineCostUnitPrice()+$this->calculateOtherCostUnitPrice()+$this->calculateTransportCostUnitPrice()+$this->calculatePondPreparationCostUnitPrice();
    }

    public function calculateTotalProductionItemTotalPrice()
    {
        return $this->calculateChickTotalPrice() + $this->calculateElectricityCostTotalPrice()+$this->calculateFeedCostTotalPrice()+$this->calculateLabourCostTotalPrice()+$this->calculateLitterOrPondRentCostTotalPrice()+
            $this->calculateVaccineCostTotalPrice()+$this->calculateMedicineCostTotalPrice()+$this->calculateOtherCostTotalPrice()+$this->calculateTransportCostTotalPrice()+$this->calculatePondPreparationCostTotalPrice();
    }

    public function calculateProductionCostPerKg()
    {
        $price=0;
        if ($this->totalBroilerWeightKg>0){
            $price = $this->calculateTotalProductionItemTotalPrice()/$this->totalBroilerWeightKg;
        }
        return $price;
    }

    public function calculateBroilerSaleUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = ($this->totalBroilerWeightKg/$this->totalStockedChicksPcs)*$this->broilerOrFishPricePerKg;
        }

        return $price;
    }

    public function calculateBroilerSaleTotalPrice()
    {
        return $this->calculateBroilerSaleUnitPrice()*$this->totalStockedChicksPcs;
    }

    public function calculateFeedBagSaleUnitPrice()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price = (($this->totalFeedUsedKg/50)/$this->totalStockedChicksPcs)*$this->usedBagPricePerPcs;
        }

        return $price;
    }

    public function calculateFeedBagSaleTotalPrice()
    {
        return $this->calculateFeedBagSaleUnitPrice()*$this->totalStockedChicksPcs;
    }

    public function calculateTotalSellingUnitPrice()
    {
        return $this->calculateBroilerSaleUnitPrice()+$this->calculateFeedBagSaleUnitPrice();
    }

    public function calculateTotalSellingTotalPrice()
    {
        return $this->calculateBroilerSaleTotalPrice()+$this->calculateFeedBagSaleTotalPrice();
    }

    public function calculateSellingPricePerKg()
    {
        $price = 0;
        if($this->totalBroilerWeightKg>0){
            $price = $this->calculateTotalSellingTotalPrice()/$this->totalBroilerWeightKg;
        }
        return $price;
    }

    public function calculateTotalNetProfit()
    {
        return $this->calculateTotalSellingTotalPrice()-$this->calculateTotalProductionItemTotalPrice();
    }

    public function calculateNetProfitPerKg()
    {
        return $this->calculateSellingPricePerKg()-$this->calculateProductionCostPerKg();
    }

}
