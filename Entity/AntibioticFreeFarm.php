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
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\AntibioticFreeFarmRepository")
 * @ORM\Table(name="crm_antibiotic_free_farm",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"employee_id", "customer_id", "reporting_month"})}
 *     )
 */
class AntibioticFreeFarm
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
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="antibioticFreeFarm")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="antibioticFreeFarm")
     * @ORM\JoinColumn(name="report_parent_parent_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $reportParentParent;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="antibioticFreeFarm")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer", inversedBy="antibioticFreeFarm")
     */
    private $customer;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="antibioticFreeFarm")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="antibioticFreeFarm")
     * @ORM\JoinColumn(name="hatchery_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $hatchery;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="antibioticFreeFarm")
     * @ORM\JoinColumn(name="breed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $breed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="antibioticFreeFarm")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $hatchingDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $reportingMonth;

//    particulars item section start 1-9:
    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $totalStockedChicksPcs=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $totalFeedUsedKg=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $ageDays=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $totalBroilerWeightKg=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $mortality=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $fcr=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $medicineTotalCost=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $vaccineTotalCost=0;

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
     * @return float
     */
    public function getMedicineTotalCost()
    {
        return $this->medicineTotalCost;
    }

    /**
     * @param float $medicineTotalCost
     */
    public function setMedicineTotalCost(float $medicineTotalCost): void
    {
        $this->medicineTotalCost = $medicineTotalCost;
    }

    public function calculateMedicineTotalCost()
    {
        
        return $this->medicineTotalCost;

    }

    /**
     * @return float
     */
    public function getVaccineTotalCost()
    {
        return $this->vaccineTotalCost;
    }

    /**
     * @param float $vaccineTotalCost
     */
    public function setVaccineTotalCost(float $vaccineTotalCost): void
    {
        $this->vaccineTotalCost = $vaccineTotalCost;
    }
    
    public function calculateVaccineTotalCost()
    {
        return $this->vaccineTotalCost;
    }

    public function calculateMedicineAndVaccineTotalCost (){
        return $this->calculateMedicineTotalCost()+$this->calculateVaccineTotalCost();
    }

    public function calculateMedicineCostPerBird()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price= $this->calculateMedicineTotalCost()/$this->totalStockedChicksPcs;
        }

        return $price;
    }

    public function calculateMedicineCostPerKg()
    {
        $price = 0;

        if($this->totalBroilerWeightKg>0){
            $price= $this->calculateMedicineTotalCost()/$this->totalBroilerWeightKg;
        }

        return $price;
    }

    public function calculateVaccineCostPerBird()
    {
        $price = 0;

        if($this->totalStockedChicksPcs>0){
            $price= $this->calculateVaccineTotalCost()/$this->totalStockedChicksPcs;
        }

        return $price;
    }

    public function calculateVaccineCostPerKg()
    {
        $price = 0;

        if($this->totalBroilerWeightKg>0){
            $price= $this->calculateVaccineTotalCost()/$this->totalBroilerWeightKg;
        }

        return $price;
    }

    public function calculateMedicineAndVaccineCostPerBird()
    {
        return $this->calculateMedicineCostPerBird()+$this->calculateVaccineCostPerBird();
    }

    public function calculateMedicineAndVaccineCostPerKg()
    {
        return $this->calculateMedicineCostPerKg()+$this->calculateVaccineCostPerKg();
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
     * @return Setting
     */
    public function getReportParentParent()
    {
        return $this->reportParentParent;
    }

    /**
     * @param Setting $reportParentParent
     */
    public function setReportParentParent(Setting $reportParentParent): void
    {
        $this->reportParentParent = $reportParentParent;
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
     * @return Setting
     */
    public function getHatchery()
    {
        return $this->hatchery;
    }

    /**
     * @param Setting $hatchery
     */
    public function setHatchery(Setting $hatchery): void
    {
        $this->hatchery = $hatchery;
    }

    /**
     * @return Setting
     */
    public function getBreed()
    {
        return $this->breed;
    }

    /**
     * @param Setting $breed
     */
    public function setBreed(Setting $breed): void
    {
        $this->breed = $breed;
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
     * @return \DateTime
     */
    public function getHatchingDate()
    {
        return $this->hatchingDate;
    }

    /**
     * @param \DateTime $hatchingDate
     */
    public function setHatchingDate(\DateTime $hatchingDate): void
    {
        $this->hatchingDate = $hatchingDate;
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
     */
    public function setReportingMonth(\DateTime $reportingMonth): void
    {
        $this->reportingMonth = $reportingMonth;
    }

    /**
     * @return float
     */
    public function getAgeDays()
    {
        return $this->ageDays;
    }

    /**
     * @param float $ageDays
     */
    public function setAgeDays(float $ageDays): void
    {
        $this->ageDays = $ageDays;
    }

    /**
     * @return float
     */
    public function getFcr()
    {
        return $this->fcr;
    }

    /**
     * @param float $fcr
     */
    public function setFcr(float $fcr): void
    {
        $this->fcr = $fcr;
    }

    public function calculateFcr()
    {
        $returnResult = 0;

        if($this->totalBroilerWeightKg>0){
           $returnResult = $this->totalFeedUsedKg/$this->totalBroilerWeightKg;
        }
        return $returnResult;
    }


    /**
     * @return float
     */
    public function getTotalStockedChicksPcs()
    {
        return $this->totalStockedChicksPcs;
    }

    /**
     * @param float $totalStockedChicksPcs
     */
    public function setTotalStockedChicksPcs(float $totalStockedChicksPcs): void
    {
        $this->totalStockedChicksPcs = $totalStockedChicksPcs;
    }

    /**
     * @return float
     */
    public function getTotalFeedUsedKg()
    {
        return $this->totalFeedUsedKg;
    }

    /**
     * @param float $totalFeedUsedKg
     */
    public function setTotalFeedUsedKg(float $totalFeedUsedKg): void
    {
        $this->totalFeedUsedKg = $totalFeedUsedKg;
    }

    /**
     * @return float
     */
    public function getTotalBroilerWeightKg()
    {
        return $this->totalBroilerWeightKg;
    }

    /**
     * @param float $totalBroilerWeightKg
     */
    public function setTotalBroilerWeightKg(float $totalBroilerWeightKg): void
    {
        $this->totalBroilerWeightKg = $totalBroilerWeightKg;
    }

    /**
     * @return float
     */
    public function getMortality()
    {
        return $this->mortality;
    }

    /**
     * @param float $mortality
     */
    public function setMortality(float $mortality): void
    {
        $this->mortality = $mortality;
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
