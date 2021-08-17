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
 * @ORM\Table(name="crm_fish_feed_complain")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FishFeedComplainRepository")
 */
class FishFeedComplain
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
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="fishFeedComplain")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="fishFeedComplain")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer", inversedBy="fishFeedComplain")
     */
    private $customer;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="fishFeedComplain")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="fishFeedComplain")
     * @ORM\JoinColumn(name="product_name_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $productName;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="fishFeedComplain")
     * @ORM\JoinColumn(name="feed_mill_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedMaill;

    /**
     * @var string
     * @Orm\Column(type="string", nullable=true)
     */
    private $feedItem;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
     */
    private $feedItemDetails;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
     */
    private $performanceRelatedComplain;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
     */
    private $physicalApperanceRelatedComplain;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
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
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getFeedMaill()
    {
        return $this->feedMaill;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $feedMaill
     */
    public function setFeedMaill(\Terminalbd\CrmBundle\Entity\Setting $feedMaill): void
    {
        $this->feedMaill = $feedMaill;
    }

    /**
     * @return string
     */
    public function getFeedItem()
    {
        return $this->feedItem;
    }

    /**
     * @param string $feedItem
     */
    public function setFeedItem(string $feedItem): void
    {
        $this->feedItem = $feedItem;
    }

    /**
     * @return string
     */
    public function getFeedItemDetails()
    {
        return $this->feedItemDetails;
    }

    /**
     * @param string $feedItemDetails
     */
    public function setFeedItemDetails(string $feedItemDetails): void
    {
        $this->feedItemDetails = $feedItemDetails;
    }

    /**
     * @return string
     */
    public function getPerformanceRelatedComplain()
    {
        return $this->performanceRelatedComplain;
    }

    /**
     * @param string $performanceRelatedComplain
     */
    public function setPerformanceRelatedComplain(string $performanceRelatedComplain): void
    {
        $this->performanceRelatedComplain = $performanceRelatedComplain;
    }

    /**
     * @return string
     */
    public function getPhysicalApperanceRelatedComplain()
    {
        return $this->physicalApperanceRelatedComplain;
    }

    /**
     * @param string $physicalApperanceRelatedComplain
     */
    public function setPhysicalApperanceRelatedComplain(string $physicalApperanceRelatedComplain): void
    {
        $this->physicalApperanceRelatedComplain = $physicalApperanceRelatedComplain;
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
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

}
