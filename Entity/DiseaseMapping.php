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
 * @ORM\Table(name="crm_disease_mapping")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\DiseaseMappingRepository")
 */
class DiseaseMapping
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
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="diseaseMapping")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="diseaseMapping")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer", inversedBy="diseaseMapping")
     */
    private $customer;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="diseaseMapping")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="diseaseMapping")
     * @ORM\JoinColumn(name="hatchery_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $hatchery;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="diseaseMapping")
     * @ORM\JoinColumn(name="farm_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $farmType;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="diseaseMapping")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="diseaseMapping")
     * @ORM\JoinColumn(name="disease_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $disease;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $breed;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $visitingDate;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $flockSizeOrCapacity=0;

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

    private $cultureAreaForFish=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $dencityForFish=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $averageWeightForFish=0;

    /**
     * @var string
     * @Orm\Column(name="age_unit_type", type="string", nullable=true)
     */
    private $ageUnitType;

    /**
     * @var string
     * @Orm\Column(name="treatment", type="text", nullable=true)
     */
    private $treatment;
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
     * @var Api
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Api", inversedBy="diseaseMapping")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $appBatch;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */

    private $appId;

    /**
     * @var CrmVisit
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmVisit" , inversedBy="diseaseMapping")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $visit;

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
    public function setReport($report): void
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
    public function setCustomer($customer): void
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
    public function getHatchery()
    {
        return $this->hatchery;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $hatchery
     */
    public function setHatchery($hatchery): void
    {
        $this->hatchery = $hatchery;
    }

    /**
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getFarmType()
    {
        return $this->farmType;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $farmType
     */
    public function setFarmType($farmType): void
    {
        $this->farmType = $farmType;
    }

    /**
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $feed
     */
    public function setFeed($feed): void
    {
        $this->feed = $feed;
    }

    /**
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getDisease()
    {
        return $this->disease;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $disease
     */
    public function setDisease($disease): void
    {
        $this->disease = $disease;
    }

    public function getBreed()
    {
        return $this->breed;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $breed
     */
    public function setBreed($breed): void
    {
        $this->breed = $breed;
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
     * @return float
     */
    public function getFlockSizeOrCapacity()
    {
        return $this->flockSizeOrCapacity;
    }

    /**
     * @param float $flockSizeOrCapacity
     */
    public function setFlockSizeOrCapacity(float $flockSizeOrCapacity): void
    {
        $this->flockSizeOrCapacity = $flockSizeOrCapacity;
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
    public function setAgeDays($ageDays): void
    {
        $this->ageDays = $ageDays;
    }

    /**
     * @return string
     */
    public function getAgeUnitType()
    {
        return $this->ageUnitType;
    }

    /**
     * @param string $ageUnitType
     */
    public function setAgeUnitType($ageUnitType): void
    {
        $this->ageUnitType = $ageUnitType;
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
    public function setRemarks($remarks): void
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
     * @return string
     */
    public function getTreatment()
    {
        return $this->treatment;
    }

    /**
     * @param string $treatment
     */
    public function setTreatment($treatment): void
    {
        $this->treatment = $treatment;
    }

    /**
     * @return float
     */
    public function getCultureAreaForFish()
    {
        return $this->cultureAreaForFish;
    }

    /**
     * @param float $cultureAreaForFish
     */
    public function setCultureAreaForFish($cultureAreaForFish): void
    {
        $this->cultureAreaForFish = $cultureAreaForFish;
    }

    /**
     * @return float
     */
    public function getDencityForFish()
    {
        return $this->dencityForFish;
    }

    /**
     * @param float $dencityForFish
     */
    public function setDencityForFish($dencityForFish): void
    {
        $this->dencityForFish = $dencityForFish;
    }

    public function calculateDencityForFish(){
        $value=0;
        if($this->getCultureAreaForFish()>0){
            $value = $this->getFlockSizeOrCapacity()/$this->getCultureAreaForFish();
        }
        
        return $value;
    }

    /**
     * @return float
     */
    public function getAverageWeightForFish()
    {
        return $this->averageWeightForFish;
    }

    /**
     * @param float $averageWeightForFish
     */
    public function setAverageWeightForFish($averageWeightForFish): void
    {
        $this->averageWeightForFish = $averageWeightForFish;
    }

    /**
     * @return Api
     */
    public function getAppBatch()
    {
        return $this->appBatch;
    }

    /**
     * @param Api $appBatch
     */
    public function setAppBatch($appBatch): void
    {
        $this->appBatch = $appBatch;
    }

    /**
     * @return int
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param int $appId
     */
    public function setAppId($appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return CrmVisit
     */
    public function getVisit()
    {
        return $this->visit;
    }

    /**
     * @param CrmVisit $visit
     */
    public function setVisit($visit): void
    {
        $this->visit = $visit;
    }
    
    
}
