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
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\LayerStandardRepository")
 * @ORM\Table(name="crm_layer_standard")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerStandard
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
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerStandard")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */

    private $report;

    /**
     * @var float
     * @ORM\Column(name="age", type="float")
     */
    private $age=0;

    /**
     * @var float
     * @ORM\Column(name="target_feed_consumption", type="float")
     */

    private $targetFeedConsumption=0;

    /**
     * @var float
     * @ORM\Column(name="target_body_weight", type="float")
     */
    private $targetBodyWeight=0;

    /**
     * @var float
     * @ORM\Column(name="target_egg_production", type="float")
     */
    private $targetEggProduction=0;

    /**
     * @var float
     * @ORM\Column(name="target_egg_weight", type="float")
     */
    private $targetEggWeight=0;

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
    public function setReport($report)
    {
        $this->report = $report;
    }

    /**
     * @return float
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param float $age
     */
    public function setAge(float $age): void
    {
        $this->age = $age;
    }

    /**
     * @return float
     */
    public function getTargetFeedConsumption()
    {
        return $this->targetFeedConsumption;
    }

    /**
     * @param float $targetFeedConsumption
     */
    public function setTargetFeedConsumption(float $targetFeedConsumption): void
    {
        $this->targetFeedConsumption = $targetFeedConsumption;
    }

    /**
     * @return float
     */
    public function getTargetBodyWeight()
    {
        return $this->targetBodyWeight;
    }

    /**
     * @param float $targetBodyWeight
     */
    public function setTargetBodyWeight(float $targetBodyWeight): void
    {
        $this->targetBodyWeight = $targetBodyWeight;
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
    public function getTargetEggWeight()
    {
        return $this->targetEggWeight;
    }

    /**
     * @param float $targetEggWeight
     */
    public function setTargetEggWeight($targetEggWeight)
    {
        $this->targetEggWeight = $targetEggWeight;
    }

}
