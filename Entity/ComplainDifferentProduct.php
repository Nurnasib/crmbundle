<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 *
 * @ORM\Table(name="crm_complain_different_product")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ComplainDifferentProductRepository")
 */
class ComplainDifferentProduct
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
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="complainDifferentProduct")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="complainDifferentProduct")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer", inversedBy="complainDifferentProduct")
     */
    private $customer;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="complainDifferentProduct")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="complainDifferentProduct")
     * @ORM\JoinColumn(name="product_name_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $product_name;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
     */
    private $complains;

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
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $report
     */
    public function setReport(\Terminalbd\CrmBundle\Entity\Setting $report): void
    {
        $this->report = $report;
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
     * @return \Terminalbd\CrmBundle\Entity\CrmCustomer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\CrmCustomer $customer
     */
    public function setCustomer(\Terminalbd\CrmBundle\Entity\CrmCustomer $customer): void
    {
        $this->customer = $customer;
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
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getProductName()
    {
        return $this->product_name;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $product_name
     */
    public function setProductName(\Terminalbd\CrmBundle\Entity\Setting $product_name): void
    {
        $this->product_name = $product_name;
    }

    /**
     * @return string
     */
    public function getComplains()
    {
        return $this->complains;
    }

    /**
     * @param string $complains
     */
    public function setComplains(string $complains): void
    {
        $this->complains = $complains;
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
