<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


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
     * @var string
     * @ORM\Column(name="visiting_week", type="string", length=50, nullable=true)
     */

    private $visitingWeek;

    /**
     * @var float
     *
     * @ORM\Column(name="total_birds", type="float")
     */

    private $totalBirds=0;

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
     * @var string
     * @Orm\Column(name="feedType", type="string", nullable=true)
     */

    private $feedType;


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
    public function getCrmChickLifeCycle(): ChickLifeCycle
    {
        return $this->crmChickLifeCycle;
    }

    /**
     * @param ChickLifeCycle $crmChickLifeCycle
     */
    public function setCrmChickLifeCycle(ChickLifeCycle $crmChickLifeCycle): void
    {
        $this->crmChickLifeCycle = $crmChickLifeCycle;
    }

    /**
     * @return string
     */
    public function getVisitingWeek(): string
    {
        return $this->visitingWeek;
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
    public function getTotalBirds(): float
    {
        return $this->totalBirds;
    }

    /**
     * @param float $totalBirds
     */
    public function setTotalBirds(float $totalBirds): void
    {
        $this->totalBirds = $totalBirds;
    }

    /**
     * @return float
     */
    public function getAgeDays(): float
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
    public function getMortalityPes(): float
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
    public function getMortalityPercent(): float
    {
        return $this->mortalityPercent;
    }

    /**
     * @param float $mortalityPercent
     */
    public function setMortalityPercent(float $mortalityPercent): void
    {
        $this->mortalityPercent = $mortalityPercent;
    }

    /**
     * @return float
     */
    public function getWeightStandard(): float
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
    public function getWeightAchieved(): float
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
    public function getFeedTotalKg(): float
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
    public function getPerBird(): float
    {
        return $this->perBird;
    }

    /**
     * @param float $perBird
     */
    public function setPerBird(float $perBird): void
    {
        $this->perBird = $perBird;
    }

    /**
     * @return float
     */
    public function getFeedStandard(): float
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
    public function getWithoutMortality(): float
    {
        return $this->withoutMortality;
    }

    /**
     * @param float $withoutMortality
     */
    public function setWithoutMortality(float $withoutMortality): void
    {
        $this->withoutMortality = $withoutMortality;
    }

    /**
     * @return float
     */
    public function getWithMortality(): float
    {
        return $this->withMortality;
    }

    /**
     * @param float $withMortality
     */
    public function setWithMortality(float $withMortality): void
    {
        $this->withMortality = $withMortality;
    }

    /**
     * @return string
     */
    public function getFeedType(): string
    {
        return $this->feedType;
    }

    /**
     * @param string $feedType
     */
    public function setFeedType(string $feedType): void
    {
        $this->feedType = $feedType;
    }

    /**
     * @return \DateTime
     */
    public function getProDate(): \DateTime
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
     * @return string
     */
    public function getRemarks(): string
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

}
