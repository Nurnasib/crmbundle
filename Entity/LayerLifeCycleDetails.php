<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use NumberFormatter;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\LayerLifeCycleRepository")
 * @ORM\Table(name="crm_layer_life_cycle_details")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerLifeCycleDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var LayerLifeCycle
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\LayerLifeCycle", inversedBy="crmLayerLifeCycleDetails")
     * @ORM\JoinColumn(name="crm_layer_life_cycle_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $crmLayerLifeCycle;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerLifeCycleDetails")
     * @ORM\JoinColumn(name="feed_mill_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedMill;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerLifeCycleDetails")
     * @ORM\JoinColumn(name="feed_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedType;

    /**
     * @var \DateTime
     * @ORM\Column(name="visiting_date", type="date", nullable=true)
     */
    private $visitingDate;

    /**
     * @var float
     * @ORM\Column(name="age_week", type="float")
     */
    private $ageWeek=0;

    /**
     * @var float
     * @ORM\Column(name="dead_bird", type="float")
     */
    private $deadBird=0;


    /**
     * @var float
     * @ORM\Column(name="avg_weight", type="float")
     */

    private $avgWeight=0;

    /**
     * @var float
     * @ORM\Column(name="target_weight", type="float")
     */

    private $targetWeight=0;

    /**
     * @var float
     * @ORM\Column(name="uniformity", type="float")
     */

    private $uniformity=0;

    /**
     * @var float
     * @ORM\Column(name="feed_per_bird", type="float")
     */

    private $feedPerBird=0;

    /**
     * @var float
     * @ORM\Column(name="target_feed_per_bird", type="float")
     */

    private $targetFeedPerBird=0;

    /**
     * @var float
     * @ORM\Column(name="total_eggs", type="float")
     */

    private $totalEggs=0;


    /**
     * @var float
     * @ORM\Column(name="egg_production", type="float")
     */

    private $eggProduction=0;

    /**
     * @var float
     * @ORM\Column(name="target_egg_production", type="float")
     */

    private $targetEggProduction=0;

    /**
     * @var float
     * @ORM\Column(name="egg_weight_actual", type="float")
     */

    private $eggWeightActual=0;

    /**
     * @var float
     * @ORM\Column(name="egg_weight_standard", type="float")
     */

    private $eggWeightStandard=0;

    /**
     * @var \DateTime
     * @ORM\Column(name="production_date", type="date", nullable=true)
     */

    private $productionDate;

    /**
     * @var string
     * @ORM\Column(name="batch_no", type="string", nullable=true)
     */

    private $batch_no;

    /**
     * @var string
     * @ORM\Column(name="medicine", type="string", nullable=true)
     */

    private $medicine;

    /**
     * @var string
     * @ORM\Column(name="remarks", type="string", nullable=true)
     */

    private $remarks;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */

    private $updated;

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
     * @return LayerLifeCycle
     */
    public function getCrmLayerLifeCycle()
    {
        return $this->crmLayerLifeCycle;
    }

    /**
     * @param LayerLifeCycle $crmLayerLifeCycle
     */
    public function setCrmLayerLifeCycle(LayerLifeCycle $crmLayerLifeCycle): void
    {
        $this->crmLayerLifeCycle = $crmLayerLifeCycle;
    }

    /**
     * @return float
     */
    public function getTotalBirds()
    {
        return $this->getCrmLayerLifeCycle()->getTotalBirds();
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
    public function getAgeWeek()
    {
        $locale = 'en_US';
        $nf = new NumberFormatter($locale, NumberFormatter::ORDINAL);
        return $nf->format($this->ageWeek).' wk';
    }

    /**
     * @param float $ageWeek
     */
    public function setAgeWeek(float $ageWeek): void
    {
        $this->ageWeek = $ageWeek;
    }

    /**
     * @return float
     */
    public function getAgeWeekOnlyNumber()
    {
        return $this->ageWeek;
    }

    /**
     * @return float
     */
    public function getDeadBird()
    {
        return $this->deadBird;
    }

    /**
     * @param float $deadBird
     */
    public function setDeadBird(float $deadBird): void
    {
        $this->deadBird = $deadBird;
    }

    public function calculatePresentBird(){
        return number_format($this->getTotalBirds()-$this->getDeadBird(),2,'.','');
    }

    /**
     * @return float
     */
    public function getAvgWeight()
    {
        return $this->avgWeight;
    }

    /**
     * @param float $avgWeight
     */
    public function setAvgWeight(float $avgWeight): void
    {
        $this->avgWeight = $avgWeight;
    }

    /**
     * @return float
     */
    public function getTargetWeight()
    {
        return $this->targetWeight;
    }

    /**
     * @param float $targetWeight
     */
    public function setTargetWeight(float $targetWeight): void
    {
        $this->targetWeight = $targetWeight;
    }

    /**
     * @return float
     */
    public function getUniformity()
    {
        return $this->uniformity;
    }

    /**
     * @param float $uniformity
     */
    public function setUniformity(float $uniformity): void
    {
        $this->uniformity = $uniformity;
    }

    /**
     * @return float
     */
    public function getFeedPerBird()
    {
        return $this->feedPerBird;
    }

    /**
     * @param float $feedPerBird
     */
    public function setFeedPerBird(float $feedPerBird): void
    {
        $this->feedPerBird = $feedPerBird;
    }

    /**
     * @return float
     */
    public function getTargetFeedPerBird()
    {
        return $this->targetFeedPerBird;
    }

    /**
     * @param float $targetFeedPerBird
     */
    public function setTargetFeedPerBird(float $targetFeedPerBird): void
    {
        $this->targetFeedPerBird = $targetFeedPerBird;
    }

    /**
     * @return float
     */
    public function getTotalEggs()
    {
        return $this->totalEggs;
    }

    /**
     * @param float $totalEggs
     */
    public function setTotalEggs(float $totalEggs): void
    {
        $this->totalEggs = $totalEggs;
    }

    /**
     * @return float
     */
    public function getEggProduction()
    {
        return number_format($this->eggProduction,2,'.','');
    }

    /**
     * @param float $eggProduction
     */
    public function setEggProduction(float $eggProduction): void
    {
        $this->eggProduction = $eggProduction;
    }

    public function calculateEggProduction()
    {
        $result = 0;
        $presentBird = $this->getTotalBirds()-$this->getDeadBird();
        if($presentBird>0){
            $result = ($this->getTotalEggs()*100)/$presentBird;
        }

        return $result;

    }

    /**
     * @return float
     */
    public function getTargetEggProduction()
    {
        return $this->targetEggProduction;
    }

    /**
     * @param float $targetEggProduction
     */
    public function setTargetEggProduction(float $targetEggProduction): void
    {
        $this->targetEggProduction = $targetEggProduction;
    }

    /**
     * @return float
     */
    public function getEggWeightActual()
    {
        return $this->eggWeightActual;
    }

    /**
     * @param float $eggWeightActual
     */
    public function setEggWeightActual(float $eggWeightActual): void
    {
        $this->eggWeightActual = $eggWeightActual;
    }

    /**
     * @return float
     */
    public function getEggWeightStandard()
    {
        return $this->eggWeightStandard;
    }

    /**
     * @param float $eggWeightStandard
     */
    public function setEggWeightStandard(float $eggWeightStandard): void
    {
        $this->eggWeightStandard = $eggWeightStandard;
    }

    /**
     * @return \DateTime
     */
    public function getProductionDate()
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
     * @return string
     */
    public function getBatchNo()
    {
        return $this->batch_no;
    }

    /**
     * @param string $batch_no
     */
    public function setBatchNo(string $batch_no): void
    {
        $this->batch_no = $batch_no;
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
    public function setHatchery($hatchery)
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
    public function setBreed($breed)
    {
        $this->breed = $breed;
    }

    /**
     * @return Setting
     */
    public function getFeedMill()
    {
        return $this->feedMill;
    }

    /**
     * @param Setting $feedMill
     */
    public function setFeedMill($feedMill)
    {
        $this->feedMill = $feedMill;
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
     * @return string
     */
    public function getMedicine()
    {
        return $this->medicine;
    }

    /**
     * @param string $medicine
     */
    public function setMedicine(string $medicine): void
    {
        $this->medicine = $medicine;
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
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated(\DateTime $updated): void
    {
        $this->updated = $updated;
    }

}
