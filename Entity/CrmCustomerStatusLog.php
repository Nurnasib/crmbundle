<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * CrmCustomer
 *
 * @ORM\Table(name="crm_customer_status_logs")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CrmCustomerStatusLogRepository")
 *
 */
class CrmCustomerStatusLog
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="crmCustomerStatusLogs")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $employee;
    
    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer", inversedBy="crmCustomerStatusLogs")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $customer;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="crmCustomerStatusLogs")
     * @ORM\JoinColumn(name="cause_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $cause;

    //Which company string
    /**
     * @var string
     * @ORM\Column(name="company_name", type="string", nullable=true)
     */
    private $companyName;

    /**
     * @var string
     * @ORM\Column(name="reason", type="text", nullable=true)
     */
    private $reason;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status; // e.g., 'active', 'closed', etc.

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;


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

    public function getEmployee()
    {
        return $this->employee;
    }

    public function setEmployee(User $employee): void
    {
        $this->employee = $employee;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer(?CrmCustomer $customer): void
    {
        $this->customer = $customer;
    }

    public function getCause()
    {
        return $this->cause;
    }

    public function setCause(?Setting $cause): void
    {
        $this->cause = $cause;
    }

    public function getCompanyName()
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


}
