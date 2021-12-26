<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fish_life_cycle")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FishLifeCycleRepository")
 */
class FishLifeCycle
{
    const REPORT_TYPE_BEFORE = 'BEFORE';
    const REPORT_TYPE_AFTER = 'AFTER';

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Api" , inversedBy="crmFishLifeCycle")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $appBatch;

    /**
     * @var $appBatch
     * @ORM\Column(type="integer",nullable=true)
     */
    private $appId;

    /**
     * @var string
     * @ORM\Column(name="report_type", type="string",nullable=true)
     */

    private $reportType; //BEFORE OR AFTER

    /**
     * @var FishLifeCycleDetails
     * @ORM\OneToMany(targetEntity="FishLifeCycleDetails", mappedBy="fishLifeCycle")
     */
    private $fishLifeCycleDetails;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycle")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="fishLifeCycle")
     */
    private $employee;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="fishLifeCycle")
     */
    private $customer;

    /**
     * @var \DateTime
     * @ORM\Column(name="reporting_month", type="date", nullable=true)
     */
    private $reportingMonth;

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
     * @return FishLifeCycleDetails
     */
    public function getFishLifeCycleDetails()
    {
        return $this->fishLifeCycleDetails;
    }

    /**
     * @param FishLifeCycleDetails $fishLifeCycleDetails
     */
    public function setFishLifeCycleDetails($fishLifeCycleDetails)
    {
        $this->fishLifeCycleDetails = $fishLifeCycleDetails;
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
     * @ORM\PrePersist
     */
    public function setReportingMonth(\DateTime $reportingMonth): void
    {
        $this->reportingMonth = $reportingMonth;
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
    public function setEmployee(User $employee)
    {
        $this->employee = $employee;
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
     * @return Setting
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param Setting $report
     */
    public function setReport($report)
    {
        $this->report = $report;
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
     * @return string
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * @param string $reportType
     */
    public function setReportType(string $reportType): void
    {
        $this->reportType = $reportType;
    }


}
