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
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\LayerLifeCycleStandardRepository")
 * @ORM\Table(name="crm_layerLifeCycle_standard")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerLifecycleStandard
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
     * @ORM\Column(name="age", type="string")
     */
    private $age;

    /**
     * @var string
     * @ORM\Column(name="bird_type",type="string")
     */
    private $birdType;

    /**
     * @var string
     * @ORM\Column(name="dailyFeedConsumption", type="string",nullable=true)
     */
    private $dailyFeedConsumption;

    /**
     * @var string
     * @ORM\Column(name="cumilative_feed", type="string",nullable=true)
     */
    private $cumilativeFeed;


    /**
     * @var string
     * @ORM\Column(name="maximum_weight", type="string",nullable=true)
     */
    private $maximumWeight;


    /**
     * @var string
     * @ORM\Column(name="minimum_weight", type="string",nullable=true)
     */
    private $minimumWeight;



    /**
     * @var string
     * @ORM\Column(name="body_weight", type="string",nullable=true)
     */
    private $bodyWeight;

    /**
     * @var string
     * @ORM\Column(name="egg_production", type="string", nullable=true)
     */
    private $eggProduction;

    /**
     * @var string
     * @ORM\Column(name="egg_weight", type="string", nullable=true)
     */
    private $eggWeight;

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
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param string $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getBirdType()
    {
        return $this->birdType;
    }

    /**
     * @param string $birdType
     */
    public function setBirdType($birdType)
    {
        $this->birdType = $birdType;
    }

    /**
     * @return string
     */
    public function getDailyFeedConsumption()
    {
        return $this->dailyFeedConsumption;
    }

    /**
     * @param string $dailyFeedConsumption
     */
    public function setDailyFeedConsumption($dailyFeedConsumption)
    {
        $this->dailyFeedConsumption = $dailyFeedConsumption;
    }

    /**
     * @return string
     */
    public function getCumilativeFeed()
    {
        return $this->cumilativeFeed;
    }

    /**
     * @param string $cumilativeFeed
     */
    public function setCumilativeFeed($cumilativeFeed)
    {
        $this->cumilativeFeed = $cumilativeFeed;
    }

    /**
     * @return string
     */
    public function getMaximumWeight()
    {
        return $this->maximumWeight;
    }

    /**
     * @param string $maximumWeight
     */
    public function setMaximumWeight($maximumWeight)
    {
        $this->maximumWeight = $maximumWeight;
    }

    /**
     * @return string
     */
    public function getMinimumWeight()
    {
        return $this->minimumWeight;
    }

    /**
     * @param string $minimumWeight
     */
    public function setMinimumWeight($minimumWeight)
    {
        $this->minimumWeight = $minimumWeight;
    }



    /**
     * @return string
     */
    public function getBodyWeight()
    {
        return $this->bodyWeight;
    }

    /**
     * @param string $bodyWeight
     */
    public function setBodyWeight($bodyWeight)
    {
        $this->bodyWeight = $bodyWeight;
    }

    /**
     * @return string
     */
    public function getEggProduction()
    {
        return $this->eggProduction;
    }

    /**
     * @param string $eggProduction
     */
    public function setEggProduction($eggProduction)
    {
        $this->eggProduction = $eggProduction;
    }

    /**
     * @return string
     */
    public function getEggWeight()
    {
        return $this->eggWeight;
    }

    /**
     * @param string $eggWeight
     */
    public function setEggWeight($eggWeight)
    {
        $this->eggWeight = $eggWeight;
    }



}
