<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fish_life_cycle_culture")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FishLifeCycleCultureRepository")
 */
class FishLifeCycleCulture
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Api" , inversedBy="fishLifeCycleCulture")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $appBatch;

    /**
     * @var $appBatch
     * @ORM\Column(type="integer",nullable=true)
     */
    private $appId;

    /**
     * @var FishLifeCycleCultureDetails
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\FishLifeCycleCultureDetails", mappedBy="fishLifeCycleCulture")
     */
    private $fishLifeCycleCultureDetails;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleCulture")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="fishLifeCycleCulture")
     */
    private $employee;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="fishLifeCycleCulture")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer", inversedBy="fishLifeCycleCulture")
     */
    private $customer;

    /**
     * @var integer
     * @Orm\Column(name="pond_number", type="integer", options={"default"="1"})
     */
    private $pondNumber=1;

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
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleCulture")
     * @ORM\JoinColumn(name="feed_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedType;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleCulture")
     * @ORM\JoinColumn(name="mainCultureSpecies", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $mainCultureSpecies;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleCulture")
     * @ORM\JoinColumn(name="otherCultureSpecies", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $otherCultureSpecies;

    /**
     * @var string
     * @Orm\Column(name="feed_item_name", type="text", nullable=true)
     */
    private $feedItemName;

    /**
     * @var \DateTime
     * @ORM\Column(name="reporting_date", type="date", nullable=true)
     */
    private $reportingDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="stocking_date", type="date", nullable=true)
     */
    private $stockingDate;

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
     * @Orm\Column(name="stocking_density", type="float")
     */

    private $stockingDensity=0;

    /**
     * @var float
     * @Orm\Column(name="average_initial_weight_gm", type="float")
     */
    private $averageInitialWeightGm=0;

    /**
     * @var float
     * @Orm\Column(name="total_initial_weight_kg", type="float")
     */
    private $totalInitialWeightKg=0;

    /**
     * @var string
     * @Orm\Column(name="species_description", type="text", nullable=true)
     */
    private $speciesDescription;

    /**
     * @var float
     * @Orm\Column(name="final_fcr", type="float", nullable=true)
     */
    private $finalFcr=0;

    /**
     * @var float
     * @Orm\Column(name="final_adg", type="float", nullable=true)
     */
    private $finalAdg=0;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20, nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
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
     * @return FishLifeCycleCultureDetails
     */
    public function getFishLifeCycleCultureDetails(): FishLifeCycleCultureDetails
    {
        return $this->fishLifeCycleCultureDetails;
    }

    /**
     * @param FishLifeCycleCultureDetails $fishLifeCycleCultureDetails
     */
    public function setFishLifeCycleCultureDetails(FishLifeCycleCultureDetails $fishLifeCycleCultureDetails): void
    {
        $this->fishLifeCycleCultureDetails = $fishLifeCycleCultureDetails;
    }

    /**
     * @return Setting
     */
    public function getReport(): Setting
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
    public function getEmployee(): User
    {
        return $this->employee;
    }

    /**
     * @param User $employee
     */
    public function setEmployee($employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return Agent
     */
    public function getAgent(): Agent
    {
        return $this->agent;
    }

    /**
     * @param Agent $agent
     */
    public function setAgent($agent): void
    {
        $this->agent = $agent;
    }

    /**
     * @return CrmCustomer
     */
    public function getCustomer(): CrmCustomer
    {
        return $this->customer;
    }

    /**
     * @param CrmCustomer $customer
     */
    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return int
     */
    public function getPondNumber(): int
    {
        return $this->pondNumber;
    }

    /**
     * @param int $pondNumber
     */
    public function setPondNumber(int $pondNumber): void
    {
        $this->pondNumber = $pondNumber;
    }

    /**
     * @return Setting
     */
    public function getHatchery(): Setting
    {
        return $this->hatchery;
    }

    /**
     * @param Setting $hatchery
     */
    public function setHatchery($hatchery): void
    {
        $this->hatchery = $hatchery;
    }

    /**
     * @return Setting
     */
    public function getFeed(): Setting
    {
        return $this->feed;
    }

    /**
     * @param Setting $feed
     */
    public function setFeed($feed): void
    {
        $this->feed = $feed;
    }

    /**
     * @return Setting
     */
    public function getFeedType(): Setting
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
     * @return Setting
     */
    public function getMainCultureSpecies(): Setting
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
    public function getOtherCultureSpecies(): Setting
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
    public function getFeedItemName(): string
    {
        return $this->feedItemName;
    }

    /**
     * @param string $feedItemName
     */
    public function setFeedItemName($feedItemName): void
    {
        $this->feedItemName = $feedItemName;
    }

    /**
     * @return \DateTime
     */
    public function getReportingDate(): \DateTime
    {
        return $this->reportingDate;
    }

    /**
     * @param \DateTime $reportingDate
     */
    public function setReportingDate(\DateTime $reportingDate): void
    {
        $this->reportingDate = $reportingDate;
    }

    /**
     * @return \DateTime
     */
    public function getStockingDate(): \DateTime
    {
        return $this->stockingDate;
    }

    /**
     * @param \DateTime $stockingDate
     */
    public function setStockingDate(\DateTime $stockingDate): void
    {
        $this->stockingDate = $stockingDate;
    }

    /**
     * @return string
     */
    public function getCultureAreaDecimal(): string
    {
        return $this->cultureAreaDecimal;
    }

    /**
     * @param string $cultureAreaDecimal
     */
    public function setCultureAreaDecimal(string $cultureAreaDecimal): void
    {
        $this->cultureAreaDecimal = $cultureAreaDecimal;
    }

    /**
     * @return float
     */
    public function getNoOfInitialFish(): float
    {
        return $this->noOfInitialFish;
    }

    /**
     * @param float $noOfInitialFish
     */
    public function setNoOfInitialFish(float $noOfInitialFish): void
    {
        $this->noOfInitialFish = $noOfInitialFish;
    }

    /**
     * @return float
     */
    public function getNoOfFinalFish(): float
    {
        return $this->noOfFinalFish;
    }

    /**
     * @param float $noOfFinalFish
     */
    public function setNoOfFinalFish(float $noOfFinalFish): void
    {
        $this->noOfFinalFish = $noOfFinalFish;
    }

    /**
     * @return float
     */
    public function getStockingDensity(): float
    {
        return $this->stockingDensity;
    }

    /**
     * @param float $stockingDensity
     */
    public function setStockingDensity(float $stockingDensity): void
    {
        $this->stockingDensity = $stockingDensity;
    }

    /**
     * @return float
     */
    public function getAverageInitialWeightGm(): float
    {
        return $this->averageInitialWeightGm;
    }

    /**
     * @param float $averageInitialWeightGm
     */
    public function setAverageInitialWeightGm(float $averageInitialWeightGm): void
    {
        $this->averageInitialWeightGm = $averageInitialWeightGm;
    }

    /**
     * @return float
     */
    public function getTotalInitialWeightKg(): float
    {
        return $this->totalInitialWeightKg;
    }

    /**
     * @param float $totalInitialWeightKg
     */
    public function setTotalInitialWeightKg(float $totalInitialWeightKg): void
    {
        $this->totalInitialWeightKg = $totalInitialWeightKg;
    }

    /**
     * @return string
     */
    public function getSpeciesDescription(): string
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

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
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
    public function getFinalFcr(): float
    {
        return $this->finalFcr;
    }

    /**
     * @param float $finalFcr
     */
    public function setFinalFcr($finalFcr): void
    {
        $this->finalFcr = $finalFcr;
    }

    /**
     * @return float
     */
    public function getFinalAdg(): float
    {
        return $this->finalAdg;
    }

    /**
     * @param float $finalAdg
     */
    public function setFinalAdg($finalAdg): void
    {
        $this->finalAdg = $finalAdg;
    }


}
