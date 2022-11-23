<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fish_life_cycle_culture_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FishLifeCycleCultureDetailsRepository")
 */
class FishLifeCycleCultureDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var FishLifeCycleCulture
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\FishLifeCycleCulture", inversedBy="fishLifeCycleCultureDetails")
     * @ORM\JoinColumn(name="fish_life_cycle_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $fishLifeCycleCulture;

    /**
     * @var float
     * @Orm\Column(name="average_present_weight", type="float")
     */
    private $averagePresentWeight=0;

    /**
     * @var \DateTime
     * @ORM\Column(name="sampling_date", type="date", nullable=true)
     */
    private $samplingDate;

    /**
     * @var float
     * @Orm\Column(name="current_culture_days", type="float", nullable=true)
     */
    private $currentCultureDays;

    /**
     * @var float
     * @Orm\Column(name="avgWeightGainGm", type="float", nullable=true)
     */

    private $avgWeightGainGm=0;

    /**
     * @var float
     * @Orm\Column(name="totalWeightGainKg", type="float", nullable=true)
     */

    private $totalWeightGainKg=0;

    /**
     * @var float
     * @Orm\Column(name="current_feed_consumption_kg", type="float", nullable=true)
     */
    private $currentFeedConsumptionKg=0;

    /**
     * @var float
     * @Orm\Column(name="current_fcr", type="float", nullable=true)
     */
    private $currentFcr=0;

    /**
     * @var float
     * @Orm\Column(name="current_adg", type="float", nullable=true)
     */
    private $currentAdg=0;

    /**
     * @var float
     * @Orm\Column(type="float")
     */
    private $srPercentage=0;

    /**
     * @var float
     * @Orm\Column(name="no_of_final_fish", type="float", nullable=true)
     */
    private $noOfFinalFish=0;

    /**
     * @var string
     * @Orm\Column(name="farmer_remarks", type="text", nullable=true)
     */
    private $farmerRemarks;

    /**
     * @var string
     * @Orm\Column(name="employee_remarks", type="text", nullable=true)
     */
    private $employeeRemarks;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    private $appId;
    
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
     * @return FishLifeCycleCulture
     */
    public function getFishLifeCycleCulture()
    {
        return $this->fishLifeCycleCulture;
    }

    /**
     * @param FishLifeCycleCulture $fishLifeCycleCulture
     */
    public function setFishLifeCycleCulture(FishLifeCycleCulture $fishLifeCycleCulture): void
    {
        $this->fishLifeCycleCulture = $fishLifeCycleCulture;
    }

    /**
     * @return float
     */
    public function getAveragePresentWeight()
    {
        return $this->averagePresentWeight;
    }

    /**
     * @param float $averagePresentWeight
     */
    public function setAveragePresentWeight(float $averagePresentWeight): void
    {
        $this->averagePresentWeight = $averagePresentWeight;
    }

    /**
     * @return \DateTime
     */
    public function getSamplingDate()
    {
        return $this->samplingDate;
    }

    /**
     * @param \DateTime $samplingDate
     */
    public function setSamplingDate(\DateTime $samplingDate): void
    {
        $this->samplingDate = $samplingDate;
    }

    /**
     * @return float
     */
    public function getCurrentCultureDays()
    {
        return $this->currentCultureDays;
    }

    /**
     * @param float $currentCultureDays
     */
    public function setCurrentCultureDays($currentCultureDays): void
    {
        $this->currentCultureDays = $currentCultureDays;
    }

    /**
     * @return float
     */
    public function getAvgWeightGainGm()
    {
        return $this->avgWeightGainGm;
    }

    /**
     * @param float $avgWeightGainGm
     */
    public function setAvgWeightGainGm(float $avgWeightGainGm): void
    {
        $this->avgWeightGainGm = $avgWeightGainGm;
    }

    /**
     * @return float
     */
    public function getTotalWeightGainKg()
    {
        return $this->totalWeightGainKg;
    }

    /**
     * @param float $totalWeightGainKg
     */
    public function setTotalWeightGainKg(float $totalWeightGainKg): void
    {
        $this->totalWeightGainKg = $totalWeightGainKg;
    }

    /**
     * @return float
     */
    public function getCurrentFeedConsumptionKg()
    {
        return $this->currentFeedConsumptionKg;
    }

    /**
     * @param float $currentFeedConsumptionKg
     */
    public function setCurrentFeedConsumptionKg(float $currentFeedConsumptionKg): void
    {
        $this->currentFeedConsumptionKg = $currentFeedConsumptionKg;
    }

    /**
     * @return float
     */
    public function getCurrentFcr()
    {
        return $this->currentFcr;
    }

    /**
     * @param float $currentFcr
     */
    public function setCurrentFcr($currentFcr): void
    {
        $this->currentFcr = $currentFcr;
    }

    /**
     * @return float
     */
    public function getCurrentAdg()
    {
        return $this->currentAdg;
    }

    /**
     * @param float $currentAdg
     */
    public function setCurrentAdg(float $currentAdg): void
    {
        $this->currentAdg = $currentAdg;
    }

    /**
     * @return float
     */
    public function getSrPercentage()
    {
        return $this->srPercentage;
    }

    /**
     * @param float $srPercentage
     */
    public function setSrPercentage(float $srPercentage): void
    {
        $this->srPercentage = $srPercentage;
    }

    /**
     * @return string
     */
    public function getFarmerRemarks(): string
    {
        return $this->farmerRemarks;
    }

    /**
     * @param string $farmerRemarks
     */
    public function setFarmerRemarks(string $farmerRemarks): void
    {
        $this->farmerRemarks = $farmerRemarks;
    }

    /**
     * @return string
     */
    public function getEmployeeRemarks(): string
    {
        return $this->employeeRemarks;
    }

    /**
     * @param string $employeeRemarks
     */
    public function setEmployeeRemarks(string $employeeRemarks): void
    {
        $this->employeeRemarks = $employeeRemarks;
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
     * @return float
     */
    public function getNoOfFinalFish()
    {
        return $this->noOfFinalFish;
    }

    /**
     * @param float $noOfFinalFish
     */
    public function setNoOfFinalFish($noOfFinalFish): void
    {
        $this->noOfFinalFish = $noOfFinalFish;
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


}
