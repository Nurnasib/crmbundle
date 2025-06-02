<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_customer_service_report")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CustomerServiceReportRepository")
 */
class CustomerServiceReport
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
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="customerServiceReport")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="customerServiceReport")
     */
    private $employee;

    /**
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmVisit" , inversedBy="customerServiceReport")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $visit;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="customerServiceReport")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="customerServiceReport")
     */
    private $customer;

    /**
     * @var string
     * @Orm\Column(name="farmer_comments", type="text", nullable=true)
     */
    private $farmerComments;

    /**
     * @var string
     * @Orm\Column(name="visitor_comments", type="text", nullable=true)
     */
    private $visitorComments;

    /**
     * @var string
     * @Orm\Column(name="customer_service_type", type="json", nullable=true)
     */
    private $customerServiceType;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    //updated_at for update time
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var Api
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Api", inversedBy="appBatch")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $appBatch;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getReport(): Setting
    {
        return $this->report;
    }

    public function setReport(Setting $report): void
    {
        $this->report = $report;
    }

    public function getEmployee(): User
    {
        return $this->employee;
    }

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

    public function getAgent(): Agent
    {
        return $this->agent;
    }

    public function setAgent(Agent $agent): void
    {
        $this->agent = $agent;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer(CrmCustomer $customer): void
    {
        $this->customer = $customer;
    }

    public function getFarmerComments(): string
    {
        return $this->farmerComments;
    }

    public function setFarmerComments(?string $farmerComments): void
    {
        $this->farmerComments = $farmerComments;
    }

    public function getVisitorComments(): string
    {
        return $this->visitorComments;
    }

    public function setVisitorComments(?string $visitorComments): void
    {
        $this->visitorComments = $visitorComments;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getAppBatch()
    {
        return $this->appBatch;
    }

    public function setAppBatch(Api $appBatch): void
    {
        $this->appBatch = $appBatch;
    }

    public function getCustomerServiceType(): string
    {
        return $this->customerServiceType;
    }

    public function setCustomerServiceType(?string $customerServiceType): void
    {
        $this->customerServiceType = $customerServiceType;
    }


}
