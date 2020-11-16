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
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="chicklifecycle")
     */
    private $customer;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmChickLifeCycle")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $report;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="chicklifecycle")
     */
    private $agent;

    /**
     * @var ChickLifeCycleDetails
     * @ORM\OneToMany(targetEntity="ChickLifeCycleDetails", mappedBy="crmChickLifeCycle")
     */
    private $crmChickLifeCycleDetails;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="chicklifecycle")
     */
    private $employee;

    /**
     * @var \DateTime
     * @ORM\Column(name="reporting_date", type="date", nullable=true)
     */
    private $reportingDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="hatching_date", type="datetime", nullable=true)
     */
    private $hatchingDate;

    /**
     * @var string
     * @Orm\Column(name="hatchery", type="string", length=50, nullable=true)
     */

    private $hatchery;

    /**
     * @var string
     * @Orm\Column(name="breed", type="string", length=50, nullable=true)
     */

    private $breed;


    /**
     * @var string
     * @Orm\Column(name="feed", type="string", length=50, nullable=true)
     */

    private $feed;

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
     * @return ChickLifeCycleDetails
     */
    public function getCrmChickLifeCycleDetails()
    {
        return $this->crmChickLifeCycleDetails;
    }

    /**
     * @param ChickLifeCycleDetails $crmChickLifeCycleDetails
     */
    public function setCrmChickLifeCycleDetails(ChickLifeCycleDetails $crmChickLifeCycleDetails): void
    {
        $this->crmChickLifeCycleDetails = $crmChickLifeCycleDetails;
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
    public function getHatchery()
    {
        return $this->hatchery;
    }

    /**
     * @param string $hatchery
     */
    public function setHatchery(string $hatchery): void
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
    public function setBreed(string $breed): void
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
    public function setFeed(string $feed): void
    {
        $this->feed = $feed;
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


}
