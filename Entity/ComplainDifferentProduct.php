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
    private $productName;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="complainDifferentProduct")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $transport;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="complainDifferentProduct")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $breed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="complainDifferentProduct")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $hatchery;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
     */
    private $complains;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $ageDays;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $boxNo;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $receivedDocQty;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $observation;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $serialNo;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $batchNo;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="complainDifferentProduct")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedMill;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="complainDifferentProduct")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $productionDate;

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
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $productName
     */
    public function setProductName(\Terminalbd\CrmBundle\Entity\Setting $productName): void
    {
        $this->productName = $productName;
    }

    /**
     * @return string
     */
    public function getComplains()
    {
        return $this->complains;
    }

    /**
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getTransport(): \Terminalbd\CrmBundle\Entity\Setting
    {
        return $this->transport;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $transport
     */
    public function setTransport(\Terminalbd\CrmBundle\Entity\Setting $transport): void
    {
        $this->transport = $transport;
    }

    /**
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getBreed(): \Terminalbd\CrmBundle\Entity\Setting
    {
        return $this->breed;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $breed
     */
    public function setBreed(\Terminalbd\CrmBundle\Entity\Setting $breed): void
    {
        $this->breed = $breed;
    }

    /**
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getHatchery(): \Terminalbd\CrmBundle\Entity\Setting
    {
        return $this->hatchery;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $hatchery
     */
    public function setHatchery(\Terminalbd\CrmBundle\Entity\Setting $hatchery): void
    {
        $this->hatchery = $hatchery;
    }

    /**
     * @param string $complains
     */
    public function setComplains(string $complains): void
    {
        $this->complains = $complains;
    }

    /**
     * @return string
     */
    public function getAgeDays(): string
    {
        return $this->ageDays;
    }

    /**
     * @param string $ageDays
     */
    public function setAgeDays(string $ageDays): void
    {
        $this->ageDays = $ageDays;
    }

    /**
     * @return string
     */
    public function getBoxNo(): string
    {
        return $this->boxNo;
    }

    /**
     * @param string $boxNo
     */
    public function setBoxNo(string $boxNo): void
    {
        $this->boxNo = $boxNo;
    }

    /**
     * @return string
     */
    public function getReceivedDocQty(): string
    {
        return $this->receivedDocQty;
    }

    /**
     * @param string $receivedDocQty
     */
    public function setReceivedDocQty(string $receivedDocQty): void
    {
        $this->receivedDocQty = $receivedDocQty;
    }

    /**
     * @return string
     */
    public function getObservation(): string
    {
        return $this->observation;
    }

    /**
     * @param string $observation
     */
    public function setObservation(string $observation): void
    {
        $this->observation = $observation;
    }

    /**
     * @return string
     */
    public function getSerialNo(): string
    {
        return $this->serialNo;
    }

    /**
     * @param string $serialNo
     */
    public function setSerialNo(string $serialNo): void
    {
        $this->serialNo = $serialNo;
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
    public function setBatchNo(string $batchNo): void
    {
        $this->batchNo = $batchNo;
    }

    /**
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getFeedMill(): \Terminalbd\CrmBundle\Entity\Setting
    {
        return $this->feedMill;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $feedMill
     */
    public function setFeedMill(\Terminalbd\CrmBundle\Entity\Setting $feedMill): void
    {
        $this->feedMill = $feedMill;
    }

    /**
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getFeed(): \Terminalbd\CrmBundle\Entity\Setting
    {
        return $this->feed;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $feed
     */
    public function setFeed(\Terminalbd\CrmBundle\Entity\Setting $feed): void
    {
        $this->feed = $feed;
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
    public function setProductionDate(\DateTime $productionDate): void
    {
        $this->productionDate = $productionDate;
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
