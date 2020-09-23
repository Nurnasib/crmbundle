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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\LayerLifeCycleRepository")
 * @ORM\Table(name="crm_layer_life_cycle")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerLifeCycle
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="total_birds", type="string")
     */
    private $totalBirds;


    /**
     * @var string
     * @ORM\Column(name="hatchery_date", type="string")
     */
    private $hatcheryDate;

    /**
     * @var string
     * @ORM\Column(name="visiting_date", type="string")
     */
    private $visitingDate;



    /**
     * @var string
     * @ORM\Column(name="age_week", type="string")
     */
    private $ageWeek;


    /**
     * @var string
     * @ORM\Column(name="hatchery", type="string")
     */
    private $hatchery;

    /**
     * @var string
     * @ORM\Column(name="breed", type="string")
     */

    private $breed;


    /**
     * @var string
     * @ORM\Column(name="dead_bird", type="string")
     */
    private $deadBird;


    /**
     * @var string
     * @ORM\Column(name="avg_weight", type="string")
     */

    private $avgWeight;

    /**
     * @var string
     * @ORM\Column(name="target_weight", type="string")
     */

    private $targetWeight;

    /**
     * @var string
     * @ORM\Column(name="uniformity", type="string")
     */

    private $uniformity;

    /**
     * @var string
     * @ORM\Column(name="feed_per_bird", type="string")
     */

    private $feedPerBird;



    /**
     * @var string
     * @ORM\Column(name="target_feed_per_bird", type="string")
     */

    private $targetFeedPerBird;

    /**
     * @var string
     * @ORM\Column(name="total_eggs", type="string")
     */

    private $totalEggs;


    /**
     * @var string
     * @ORM\Column(name="target_egg_production", type="string")
     */

    private $targetEggProduction;


    /**
     * @var string
     * @ORM\Column(name="egg_weight_actual", type="string")
     */

    private $eggWeightActual;



    /**
     * @var string
     * @ORM\Column(name="egg_weight_standard", type="string")
     */

    private $eggWeightStandard;

    /**
     * @var string
     * @ORM\Column(name="feed_type", type="string")
     */

    private $feedType;

    /**
     * @var string
     * @ORM\Column(name="production_date", type="string")
     */

    private $productionDate;

    /**
     * @var string
     * @ORM\Column(name="batch_no", type="string")
     */

    private $batch_no;

    /**
     * @var string
     * @ORM\Column(name="feed_mill", type="string")
     */

    private $feedMill;


    /**
     * @var string
     * @ORM\Column(name="medicine", type="string")
     */

    private $medicine;


    /**
     * @var string
     * @ORM\Column(name="remarks", type="string")
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
     * @ORM\Column(name="updated", type="datetime", nullable = true)
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
     * @return string
     */
    public function getTotalBirds()
    {
        return $this->totalBirds;
    }

    /**
     * @param string $totalBirds
     */
    public function setTotalBirds($totalBirds)
    {
        $this->totalBirds = $totalBirds;
    }

    /**
     * @return string
     */
    public function getHatcheryDate()
    {
        return $this->hatcheryDate;
    }

    /**
     * @param string $hatcheryDate
     */
    public function setHatcheryDate($hatcheryDate)
    {
        $this->hatcheryDate = $hatcheryDate;
    }

    /**
     * @return string
     */
    public function getHatchery()
    {
        return $this->hatchery;
    }

    /**
     * @param string $hatchery
     */
    public function setHatchery($hatchery)
    {
        $this->hatchery = $hatchery;
    }

    /**
     * @return string
     */
    public function getBreed()
    {
        return $this->breed;
    }

    /**
     * @param string $breed
     */
    public function setBreed($breed)
    {
        $this->breed = $breed;
    }

    /**
     * @return string
     */
    public function getDeadBird()
    {
        return $this->deadBird;
    }

    /**
     * @param string $deadBird
     */
    public function setDeadBird($deadBird)
    {
        $this->deadBird = $deadBird;
    }

    /**
     * @return string
     */
    public function getAvgWeight()
    {
        return $this->avgWeight;
    }

    /**
     * @param string $avgWeight
     */
    public function setAvgWeight($avgWeight)
    {
        $this->avgWeight = $avgWeight;
    }

    /**
     * @return string
     */
    public function getTargetWeight()
    {
        return $this->targetWeight;
    }

    /**
     * @param string $targetWeight
     */
    public function setTargetWeight($targetWeight)
    {
        $this->targetWeight = $targetWeight;
    }

    /**
     * @return string
     */
    public function getUniformity()
    {
        return $this->uniformity;
    }

    /**
     * @param string $uniformity
     */
    public function setUniformity($uniformity)
    {
        $this->uniformity = $uniformity;
    }

    /**
     * @return string
     */
    public function getFeedPerBird()
    {
        return $this->feedPerBird;
    }

    /**
     * @param string $feedPerBird
     */
    public function setFeedPerBird($feedPerBird)
    {
        $this->feedPerBird = $feedPerBird;
    }

    /**
     * @return string
     */
    public function getTargetFeedPerBird()
    {
        return $this->targetFeedPerBird;
    }

    /**
     * @param string $targetFeedPerBird
     */
    public function setTargetFeedPerBird($targetFeedPerBird)
    {
        $this->targetFeedPerBird = $targetFeedPerBird;
    }

    /**
     * @return string
     */
    public function getTotalEggs()
    {
        return $this->totalEggs;
    }

    /**
     * @param string $totalEggs
     */
    public function setTotalEggs($totalEggs)
    {
        $this->totalEggs = $totalEggs;
    }

    /**
     * @return string
     */
    public function getTargetEggProduction()
    {
        return $this->targetEggProduction;
    }

    /**
     * @param string $targetEggProduction
     */
    public function setTargetEggProduction($targetEggProduction)
    {
        $this->targetEggProduction = $targetEggProduction;
    }

    /**
     * @return string
     */
    public function getEggWeightActual()
    {
        return $this->eggWeightActual;
    }

    /**
     * @param string $eggWeightActual
     */
    public function setEggWeightActual($eggWeightActual)
    {
        $this->eggWeightActual = $eggWeightActual;
    }

    /**
     * @return string
     */
    public function getEggWeightStandard()
    {
        return $this->eggWeightStandard;
    }

    /**
     * @param string $eggWeightStandard
     */
    public function setEggWeightStandard($eggWeightStandard)
    {
        $this->eggWeightStandard = $eggWeightStandard;
    }

    /**
     * @return string
     */
    public function getFeedType()
    {
        return $this->feedType;
    }

    /**
     * @param string $feedType
     */
    public function setFeedType($feedType)
    {
        $this->feedType = $feedType;
    }

    /**
     * @return string
     */
    public function getProductionDate()
    {
        return $this->productionDate;
    }

    /**
     * @param string $productionDate
     */
    public function setProductionDate($productionDate)
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
    public function setBatchNo($batch_no)
    {
        $this->batch_no = $batch_no;
    }

    /**
     * @return string
     */
    public function getFeedMill()
    {
        return $this->feedMill;
    }

    /**
     * @param string $feedMill
     */
    public function setFeedMill($feedMill)
    {
        $this->feedMill = $feedMill;
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
    public function setMedicine($medicine)
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
    public function setRemarks($remarks)
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
    public function setCreated($created)
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
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getVisitingDate()
    {
        return $this->visitingDate;
    }

    /**
     * @param string $visitingDate
     */
    public function setVisitingDate($visitingDate)
    {
        $this->visitingDate = $visitingDate;
    }

    /**
     * @return string
     */
    public function getAgeWeek()
    {
        return $this->ageWeek;
    }

    /**
     * @param string $ageWeek
     */
    public function setAgeWeek($ageWeek)
    {
        $this->ageWeek = $ageWeek;
    }



}
