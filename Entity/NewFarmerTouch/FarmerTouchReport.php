<?php

namespace Terminalbd\CrmBundle\Entity\NewFarmerTouch;

use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 *
 * @ORM\Table(name="crm_farmer_touch_report")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\NewFarmerTouch\FarmerTouchRepository")
 */
class FarmerTouchReport
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
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="fishFarmerTouch")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $report;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="farmerTouch")
     * @ORM\JoinColumn(name="farmerType", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $farmerType;

    /**
     * @var CrmCustomer
     * @ORM\OneToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer", inversedBy="farmerTouch")
     */
    private $customer;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="farmerTouch")
     */
    private $agent;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="farmerTouch")
     */
    private $otherAgent;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting" , inversedBy="farmerIntroduce")
     */
    private $feed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting" , inversedBy="farmerIntroduce")
     */
    private $otherFeed;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="fishFarmerTouch")
     */
    private $employee;

    /**
     * @var \DateTime
     * @ORM\Column(name="visiting_date", type="date", nullable=true)
     */
    private $visitingDate;

    /**
     * @var string
     * @Orm\Column(name="culture_species_item_and_qty", type="text", nullable=true)
     */
    private $cultureSpeciesItemAndQty;

    /**
     * @var string
     * @Orm\Column(name="nourish_item_name", type="string", nullable=true)
     */
    private $nourishItemName;

    /**
     * @var string
     * @Orm\Column(name="other_culture_species", type="string", nullable=true)
     */
    private $otherCultureSpecies;

    /**
     * @var string
     * @Orm\Column(type="string", nullable=true)
     */
    private $conventionalFeed;

    /**
     * @var float
     *
     * @ORM\Column(name="culture_area_decimal", type="float")
     */

    private $cultureAreaDecimal=0;

    /**
     * @var float
     * @Orm\Column(name="yearlyFeedUseTon", type="float")
     */
    private $yearlyFeedUseTon=0;

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
     * @return CrmCustomer
     */
    public function getCustomer()
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
     * @return string
     */
    public function getCultureSpeciesItemAndQty()
    {
        return $this->cultureSpeciesItemAndQty;
    }

    /**
     * @param string $cultureSpeciesItemAndQty
     */
    public function setCultureSpeciesItemAndQty(string $cultureSpeciesItemAndQty): void
    {
        $this->cultureSpeciesItemAndQty = $cultureSpeciesItemAndQty;
    }

    public function getCultureSpeciesQtyByItem(){

        $returnArray = array();
        if ($this->getCultureSpeciesItemAndQty()){
            foreach (json_decode($this->getCultureSpeciesItemAndQty()) as $key=>$item){
                $returnArray[$key]=$item;
            }
        }

        return $returnArray;
    }

    /**
     * @return string
     */
    public function getNourishItemName()
    {
        return $this->nourishItemName;
    }

    /**
     * @param string $nourishItemName
     */
    public function setNourishItemName(string $nourishItemName): void
    {
        $this->nourishItemName = $nourishItemName;
    }

    /**
     * @return string
     */
    public function getOtherCultureSpecies()
    {
        return $this->otherCultureSpecies;
    }

    /**
     * @param string $otherCultureSpecies
     */
    public function setOtherCultureSpecies(string $otherCultureSpecies): void
    {
        $this->otherCultureSpecies = $otherCultureSpecies;
    }

    /**
     * @return string
     */
    public function getConventionalFeed()
    {
        return $this->conventionalFeed;
    }

    /**
     * @param string $conventionalFeed
     */
    public function setConventionalFeed(string $conventionalFeed): void
    {
        $this->conventionalFeed = $conventionalFeed;
    }

    /**
     * @return float
     */
    public function getCultureAreaDecimal()
    {
        return $this->cultureAreaDecimal;
    }

    /**
     * @param float $cultureAreaDecimal
     */
    public function setCultureAreaDecimal(float $cultureAreaDecimal): void
    {
        $this->cultureAreaDecimal = $cultureAreaDecimal;
    }

    /**
     * @return float
     */
    public function getYearlyFeedUseTon()
    {
        return $this->yearlyFeedUseTon;
    }

    /**
     * @param float $yearlyFeedUseTon
     */
    public function setYearlyFeedUseTon(float $yearlyFeedUseTon): void
    {
        $this->yearlyFeedUseTon = $yearlyFeedUseTon;
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
    public function setRemarks(string $remarks)
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

    /**
     * @return Setting
     */
    public function getFarmerType()
    {
        return $this->farmerType;
    }

    /**
     * @param Setting $farmerType
     */
    public function setFarmerType(Setting $farmerType): void
    {
        $this->farmerType = $farmerType;
    }

    /**
     * @return Agent
     */
    public function getOtherAgent()
    {
        return $this->otherAgent;
    }

    /**
     * @param Agent $otherAgent
     */
    public function setOtherAgent(Agent $otherAgent): void
    {
        $this->otherAgent = $otherAgent;
    }

    /**
     * @return Setting
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param Setting $feed
     */
    public function setFeed(Setting $feed): void
    {
        $this->feed = $feed;
    }

    /**
     * @return Setting
     */
    public function getOtherFeed()
    {
        return $this->otherFeed;
    }

    /**
     * @param Setting $otherFeed
     */
    public function setOtherFeed(Setting $otherFeed): void
    {
        $this->otherFeed = $otherFeed;
    }

}
