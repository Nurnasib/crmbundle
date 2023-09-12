<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(name="crm_cattle_life_cycle")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CattleLifeCycleRepository")
 */
class CattleLifeCycle
{
    const LIFE_CYCLE_STATE_COMPLETE = 'COMPLETE';
    const LIFE_CYCLE_STATE_CANCEL = 'CANCEL';
    const LIFE_CYCLE_STATE_IN_PROGRESS = 'IN_PROGRESS';
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="cattlelifecycle")
     */
    private $customer;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmCattleLifeCycle")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $report;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="cattlelifecycle")
     */
    private $agent;

    /**
     * @var CattleLifeCycleDetails
     * @ORM\OneToMany(targetEntity="CattleLifeCycleDetails", mappedBy="crmCattleLifeCycle")
     */
    private $crmCattleLifeCycleDetails;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="cattlelifecycle")
     */
    private $employee;

    /**
     * @ORM\ManyToOne(targetEntity="Api" , inversedBy="cattlelifecycle")
     */
    private $appBatch;

    /**
     * @var integer
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $appId;

    /**
     * @var \DateTime
     * @ORM\Column(name="reporting_date", type="date", nullable=true)
     */
    private $reportingDate;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmCattleLifeCycle")
     * @ORM\JoinColumn(name="breed_type", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $breedType;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmCattleLifeCycle")
     * @ORM\JoinColumn(name="feed_type", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedType;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmCattleLifeCycle")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmCattleLifeCycle")
     * @ORM\JoinColumn(name="feed_mill_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedMill;

    /**
     * @var \DateTime
     * @ORM\Column(name="production_date", type="date", nullable=true)
     */
    private $productionDate;

    /**
     * @var string
     * @Orm\Column(name="batch_no", type="text", nullable=true)
     */

    private $batchNo;

    /**
     * @var string
     *
     * @ORM\Column(name="life_cycle_state", type="string", length=20, nullable=true)
     */
    private $lifeCycleState;

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
     * @var integer
     * @Orm\Column(name="farm_number", type="integer", options={"default"="1"})
     */
    private $farmNumber=1;
    
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
     * @return CrmCustomer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param CrmCustomer $customer
     */
    public function setCustomer(CrmCustomer $customer)
    {
        $this->customer = $customer;
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
     * @return CattleLifeCycleDetails
     */
    public function getCrmCattleLifeCycleDetails()
    {
        return $this->crmCattleLifeCycleDetails;
    }

    /**
     * @param CattleLifeCycleDetails $crmCattleLifeCycleDetails
     */
    public function setCrmCattleLifeCycleDetails(CattleLifeCycleDetails $crmCattleLifeCycleDetails): void
    {
        $this->crmCattleLifeCycleDetails = $crmCattleLifeCycleDetails;
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
    public function setEmployee($employee)
    {
        $this->employee = $employee;
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
     * @return string
     */
    public function getLifeCycleState()
    {
        return $this->lifeCycleState;
    }

    /**
     * @param string $lifeCycleState
     */
    public function setLifeCycleState(string $lifeCycleState): void
    {
        $this->lifeCycleState = $lifeCycleState;
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
    public function setRemarks(string $remarks): void
    {
        $this->remarks = $remarks;
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
     * @return \DateTime
     */
    public function getReportingDate()
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
     * @return Setting
     */
    public function getBreedType()
    {
        return $this->breedType;
    }

    /**
     * @param Setting $breedType
     */
    public function setBreedType($breedType)
    {
        $this->breedType = $breedType;
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
    public function setFeedType(Setting $feedType): void
    {
        $this->feedType = $feedType;
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
     * @return int
     */
    public function getFarmNumber()
    {
        return $this->farmNumber;
    }

    /**
     * @param int $farmNumber
     */
    public function setFarmNumber($farmNumber): void
    {
        $this->farmNumber = $farmNumber;
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
    public function setFeed(?Setting $feed): void
    {
        $this->feed = $feed;
    }

    /**
     * @return Setting
     */
    public function getFeedMill(): Setting
    {
        return $this->feedMill;
    }

    /**
     * @param Setting $feedMill
     */
    public function setFeedMill(?Setting $feedMill): void
    {
        $this->feedMill = $feedMill;
    }

    /**
     * @return \DateTime
     */
    public function getProductionDate(): \DateTime
    {
        return $this->productionDate;
    }

    /**
     * @param \DateTime $productionDate
     */
    public function setProductionDate(?\DateTime $productionDate): void
    {
        $this->productionDate = $productionDate;
    }

    /**
     * @return string
     */
    public function getBatchNo(): string
    {
        return $this->batchNo;
    }

    /**
     * @param string $batchNo
     */
    public function setBatchNo(?string $batchNo): void
    {
        $this->batchNo = $batchNo;
    }
    

}
