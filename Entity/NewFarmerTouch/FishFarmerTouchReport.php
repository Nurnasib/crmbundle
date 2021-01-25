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
 * @ORM\Table(name="crm_fish_farmer_touch_report")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\NewFarmerTouch\FishFarmerTouchRepository")
 */
class FishFarmerTouchReport
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
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="fishFarmerTouch")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer", inversedBy="fishFarmerTouch")
     */
    private $customer;

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
