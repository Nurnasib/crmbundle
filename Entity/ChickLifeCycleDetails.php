<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use NumberFormatter;


/**
 * @ORM\Table(name="crm_chick_life_cycle_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ChickLifeCycleRepository")
 */
class ChickLifeCycleDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var ChickLifeCycle
     * @ORM\ManyToOne(targetEntity="ChickLifeCycle", inversedBy="crmChickLifeCycleDetails")
     * @ORM\JoinColumn(name="crm_chick_life_cycle_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $crmChickLifeCycle;


    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmChickLifeCycleDetails")
     * @ORM\JoinColumn(name="feed_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedType;

    /**
     * @var \DateTime
     * @ORM\Column(name="reporting_date", type="date", nullable=true)
     */
    private $reportingDate;

    /**
     * @var string
     * @ORM\Column(name="visiting_week", type="string", length=50, nullable=true)
     */

    private $visitingWeek;

    /**
     * @var float
     * @Orm\Column(name="age_days", type="float")
     */
    private $ageDays=0;

    /**
     * @var float
     * @Orm\Column(name="mortality_pes", type="float")
     */

    private $mortalityPes=0;

    /**
     * @var float
     * @Orm\Column(name="mortality_percent", type="float")
     */

    private $mortalityPercent=0;

    /**
     * @var float
     * @Orm\Column(name="weight_standard", type="float")
     */

    private $weightStandard=0;

    /**
     * @var float
     * @Orm\Column(name="weight_achieved", type="float")
     */

    private $weightAchieved=0;

    /**
     * @var float
     * @Orm\Column(name="feed_total_kg", type="float")
     */

    private $feedTotalKg=0;

    /**
     * @var float
     * @Orm\Column(name="per_bird", type="float")
     */

    private $perBird=0;

    /**
     * @var float
     * @Orm\Column(name="feed_standard", type="float")
     */

    private $feedStandard=0;

    /**
     * @var float
     * @Orm\Column(name="without_mortality", type="float")
     */

    private $withoutMortality=0;

    /**
     * @var float
     * @Orm\Column(name="with_mortality", type="float")
     */

    private $withMortality=0;

    /**
     * @var \DateTime
     * @ORM\Column(name="pro_date", type="datetime", nullable=true)
     */

    private $proDate;

    /**
     * @var string
     * @Orm\Column(name="batch_no", type="string", nullable=true)
     */

    private $batchNo;

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
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
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

    /**
     * @return ChickLifeCycle
     */
    public function getCrmChickLifeCycle()
    {
        return $this->crmChickLifeCycle;
    }

    /**
     * @param ChickLifeCycle $crmChickLifeCycle
     */
    public function setCrmChickLifeCycle($crmChickLifeCycle)
    {
        $this->crmChickLifeCycle = $crmChickLifeCycle;
    }

    /**
     * @return Setting
     */
    public function getFeedType()
    {
        return $this->feedType;
    }

    /**
     * @param Setting $feedType
     */
    public function setFeedType($feedType)
    {
        $this->feedType = $feedType;
    }

    /**
     * @return \DateTime
     */
    public function getReportingDate()
    {
        return $this->reportingDate;
    }

    /**
     * @param \DateTime $reportingDate
     */
    public function setReportingDate(\DateTime $reportingDate): void
    {
        $this->reportingDate = $reportingDate;
    }

    /**
     * @return string
     */
    public function getVisitingWeek()
    {
        $locale = 'en_US';
        $nf = new NumberFormatter($locale, NumberFormatter::ORDINAL);
        return $nf->format($this->visitingWeek).' week';
    }

    /**
     * @param string $visitingWeek
     */
    public function setVisitingWeek(string $visitingWeek): void
    {
        $this->visitingWeek = $visitingWeek;
    }

    /**
     * @return float
     */
    public function getTotalBirds()
    {
        return $this->getCrmChickLifeCycle()->getTotalBirds();
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
    public function getMortalityPes()
    {
        return $this->mortalityPes;
    }

    /**
     * @param float $mortalityPes
     */
    public function setMortalityPes(float $mortalityPes): void
    {
        $this->mortalityPes = $mortalityPes;
    }

    /**
     * @return float
     */
    public function getMortalityPercent()
    {
        return number_format($this->mortalityPercent,2,'.','');
    }

    /**
     * @param float $mortalityPercent
     */
    public function setMortalityPercent(float $mortalityPercent): void
    {
        $this->mortalityPercent = $mortalityPercent;
    }

    public function calculateMortalityPercent(){
        $result = 0;
        if($this->getTotalBirds()>0){
            $result = ($this->getMortalityPes()*100)/$this->getTotalBirds();
        }
        return  $result;
    }

    /**
     * @return float
     */
    public function getWeightStandard()
    {
        return $this->weightStandard;
    }

    /**
     * @param float $weightStandard
     */
    public function setWeightStandard(float $weightStandard): void
    {
        $this->weightStandard = $weightStandard;
    }

    /**
     * @return float
     */
    public function getWeightAchieved()
    {
        return $this->weightAchieved;
    }

    /**
     * @param float $weightAchieved
     */
    public function setWeightAchieved(float $weightAchieved): void
    {
        $this->weightAchieved = $weightAchieved;
    }

    /**
     * @return float
     */
    public function getFeedTotalKg()
    {
        return $this->feedTotalKg;
    }

    /**
     * @param float $feedTotalKg
     */
    public function setFeedTotalKg(float $feedTotalKg): void
    {
        $this->feedTotalKg = $feedTotalKg;
    }

    /**
     * @return float
     */
    public function getPerBird()
    {
        return  number_format($this->perBird,2,'.','');
    }

    /**
     * @param float $perBird
     */
    public function setPerBird(float $perBird): void
    {
        $this->perBird = $perBird;
    }

    public function calculatePerBird(){

        $result =0;
        if($this->getTotalBirds()>0){
            $result = (($this->getFeedTotalKg()/$this->getTotalBirds())*1000);
        }

        return $result;
    }

    /**
     * @return float
     */
    public function getFeedStandard()
    {
        return $this->feedStandard;
    }

    /**
     * @param float $feedStandard
     */
    public function setFeedStandard(float $feedStandard): void
    {
        $this->feedStandard = $feedStandard;
    }

    /**
     * @return float
     */
    public function getWithoutMortality()
    {
        return number_format($this->withoutMortality,2,'.','');
    }

    /**
     * @param float $withoutMortality
     */
    public function setWithoutMortality(float $withoutMortality): void
    {
        $this->withoutMortality = $withoutMortality;
    }

    public function calculateWithoutMortality(){
        $result = 0;
        if($this->getTotalBirds()>0 && $this->getWeightAchieved()>0) {

            $result = (($this->getFeedTotalKg() / $this->getTotalBirds()) / $this->getWeightAchieved()) * 1000;
        }
        return $result;

    }

    /**
     * @return float
     */
    public function getWithMortality()
    {
        return number_format($this->withMortality,2,'.','');
    }

    /**
     * @param float $withMortality
     */
    public function setWithMortality(float $withMortality): void
    {
        $this->withMortality = $withMortality;
    }

    public function calculateWithMortality(){
        $result = 0;
        if($this->getTotalBirds()>0 && $this->getWeightAchieved()>0){

            $result = (($this->getFeedTotalKg()/($this->getTotalBirds()-$this->getMortalityPes()))/$this->getWeightAchieved())*1000;
        }

        return $result;

    }

    /**
     * @return \DateTime
     */
    public function getProDate()
    {
        return $this->proDate;
    }

    /**
     * @param \DateTime $proDate
     */
    public function setProDate(\DateTime $proDate): void
    {
        $this->proDate = $proDate;
    }

    /**
     * @return string
     */
    public function getBatchNo()
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
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


}
