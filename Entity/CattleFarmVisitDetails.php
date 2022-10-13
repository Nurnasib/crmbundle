<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use NumberFormatter;


/**
 * @ORM\Table(name="crm_cattle_farm_visit_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CattleFarmVisitDetailsRepository")
 */
class CattleFarmVisitDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="crmCattleFarmVisit")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmCattleFarmVisit")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var \DateTime
     * @ORM\Column(name="repoting_month", type="date", nullable=true)
     */

    private $reportingMonth;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="crmCattleFarmVisitDetails")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="crmCattleFarmVisitDetails")
     */
    private $customer;

    /**
     * @var \DateTime
     * @ORM\Column(name="visiting_date", type="date", nullable=true)
     */

    private $visitingDate;

    /**
     * @var float
     * @Orm\Column(name="cattlePopulationOx", type="float", nullable=true)
     */
    private $cattlePopulationOx=0;

    /**
     * @var float
     * @Orm\Column(name="cattlePopulationCow", type="float", nullable=true)
     */
    private $cattlePopulationCow=0;

    /**
     * @var float
     * @Orm\Column(name="cattlePopulationCalf", type="float", nullable=true)
     */
    private $cattlePopulationCalf=0;

    /**
     * @var float
     * @Orm\Column(name="avgMilkYieldPerDay", type="float", nullable=true)
     */

    private $avgMilkYieldPerDay=0;

    /**
     * @var float
     * @Orm\Column(name="conceptionRate", type="float", nullable=true)
     */

    private $conceptionRate=0;

    /**
     * @var string
     * @Orm\Column(name="fodder_green_grass_kg", type="string", nullable=true)
     */

    private $fodderGreenGrassKg;

    /**
     * @var string
     * @Orm\Column(name="fodder_straw_kg", type="string", nullable=true)
     */

    private $fodderStrawKg;

    /**
     * @var string
     * @Orm\Column(name="typeOfConcentrateFeed", type="string", nullable=true)
     */

    private $typeOfConcentrateFeed;

    /**
     * @var float
     * @Orm\Column(name="marketPriceMilkPerLiter", type="float")
     */

    private $marketPriceMilkPerLiter=0;

    /**
     * @var float
     * @Orm\Column(name="marketPriceMeatPerKg", type="float")
     */

    private $marketPriceMeatPerKg=0;

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
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var Api
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Api", inversedBy="diseaseMapping")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $appBatch;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */

    private $appId;

    /**
     * @var CrmVisit
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmVisit" , inversedBy="diseaseMapping")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $visit;

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
     * @return User
     */
    public function getEmployee(): User
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
     * @return \DateTime
     */
    public function getReportingMonth(): \DateTime
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
     * @return Agent
     */
    public function getAgent(): Agent
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
    public function getCustomer(): CrmCustomer
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
     * @return \DateTime
     */
    public function getVisitingDate()
    {
        return $this->visitingDate;
    }

    /**
     * @param \DateTime $visitingDate
     */
    public function setVisitingDate(\DateTime $visitingDate): void
    {
        $this->visitingDate = $visitingDate;
    }

    /**
     * @return float
     */
    public function getCattlePopulationOx(): float
    {
        return $this->cattlePopulationOx;
    }

    /**
     * @param float $cattlePopulationOx
     */
    public function setCattlePopulationOx(float $cattlePopulationOx): void
    {
        $this->cattlePopulationOx = $cattlePopulationOx;
    }

    /**
     * @return float
     */
    public function getCattlePopulationCow(): float
    {
        return $this->cattlePopulationCow;
    }

    /**
     * @param float $cattlePopulationCow
     */
    public function setCattlePopulationCow(float $cattlePopulationCow): void
    {
        $this->cattlePopulationCow = $cattlePopulationCow;
    }

    /**
     * @return float
     */
    public function getCattlePopulationCalf(): float
    {
        return $this->cattlePopulationCalf;
    }

    /**
     * @param float $cattlePopulationCalf
     */
    public function setCattlePopulationCalf(float $cattlePopulationCalf): void
    {
        $this->cattlePopulationCalf = $cattlePopulationCalf;
    }

    /**
     * @return float
     */
    public function getAvgMilkYieldPerDay(): float
    {
        return $this->avgMilkYieldPerDay;
    }

    /**
     * @param float $avgMilkYieldPerDay
     */
    public function setAvgMilkYieldPerDay(float $avgMilkYieldPerDay): void
    {
        $this->avgMilkYieldPerDay = $avgMilkYieldPerDay;
    }

    /**
     * @return float
     */
    public function getConceptionRate(): float
    {
        return $this->conceptionRate;
    }

    /**
     * @param float $conceptionRate
     */
    public function setConceptionRate(float $conceptionRate): void
    {
        $this->conceptionRate = $conceptionRate;
    }

    /**
     * @return string
     */
    public function getFodderGreenGrassKg(): string
    {
        return $this->fodderGreenGrassKg;
    }

    /**
     * @param string $fodderGreenGrassKg
     */
    public function setFodderGreenGrassKg(string $fodderGreenGrassKg): void
    {
        $this->fodderGreenGrassKg = $fodderGreenGrassKg;
    }

    /**
     * @return string
     */
    public function getFodderStrawKg(): string
    {
        return $this->fodderStrawKg;
    }

    /**
     * @param string $fodderStrawKg
     */
    public function setFodderStrawKg(string $fodderStrawKg): void
    {
        $this->fodderStrawKg = $fodderStrawKg;
    }

    /**
     * @return string
     */
    public function getTypeOfConcentrateFeed(): string
    {
        return $this->typeOfConcentrateFeed;
    }

    /**
     * @param string $typeOfConcentrateFeed
     */
    public function setTypeOfConcentrateFeed(string $typeOfConcentrateFeed): void
    {
        $this->typeOfConcentrateFeed = $typeOfConcentrateFeed;
    }

    /**
     * @return float
     */
    public function getMarketPriceMilkPerLiter(): float
    {
        return $this->marketPriceMilkPerLiter;
    }

    /**
     * @param float $marketPriceMilkPerLiter
     */
    public function setMarketPriceMilkPerLiter(float $marketPriceMilkPerLiter): void
    {
        $this->marketPriceMilkPerLiter = $marketPriceMilkPerLiter;
    }

    /**
     * @return float
     */
    public function getMarketPriceMeatPerKg(): float
    {
        return $this->marketPriceMeatPerKg;
    }

    /**
     * @param float $marketPriceMeatPerKg
     */
    public function setMarketPriceMeatPerKg(float $marketPriceMeatPerKg): void
    {
        $this->marketPriceMeatPerKg = $marketPriceMeatPerKg;
    }

    /**
     * @return string
     */
    public function getRemarks(): string
    {
        return $this->remarks;
    }

    /**
     * @param string $remarks
     */
    public function setRemarks(string $remarks): void
    {
        $this->remarks = $remarks;
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
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
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
    public function setAppBatch($appBatch): void
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
    public function setAppId($appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return CrmVisit
     */
    public function getVisit()
    {
        return $this->visit;
    }

    /**
     * @param CrmVisit $visit
     */
    public function setVisit($visit): void
    {
        $this->visit = $visit;
    }

}
