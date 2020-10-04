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
use App\Entity\Core\Agent;
//use App\Entity\Admin\Location;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\LayerPerformanceRepository")
 * @ORM\Table(name="crm_layer_performance")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerPerformance
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="chicklifecycle")
     */
    private $employee;


    /**
     * @var string
     * @ORM\Column(name="total_birds", type="string")
     */
    private $totalBirds;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="layerperformance")
     */
    private $agent;

    /**
     * @var string
     * @ORM\Column(name="age_wk", type="string")
     */
    private $ageWk;

    /**
     * @var string
     * @ORM\Column(name="bird_weight_achieved", type="string")
     */

    private $birdWeightAchieved;

    /**
     * @var string
     * @ORM\Column(name="bird_weight_target", type="string")
     */

    private $birdWeightTarget;

    /**
     * @var string
     * @ORM\Column(name="feed_intake_per_bird", type="string")
     */

    private $feedIntakePerBird;

    /**
     * @var string
     * @ORM\Column(name="feed_Target", type="string")
     */

    private $feedTarget;

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
    public function getAgeWk()
    {
        return $this->ageWk;
    }

    /**
     * @param string $ageWk
     */
    public function setAgeWk($ageWk)
    {
        $this->ageWk = $ageWk;
    }

    /**
     * @return string
     */
    public function getBirdWeightAchieved()
    {
        return $this->birdWeightAchieved;
    }

    /**
     * @param string $birdWeightAchieved
     */
    public function setBirdWeightAchieved($birdWeightAchieved)
    {
        $this->birdWeightAchieved = $birdWeightAchieved;
    }

    /**
     * @return string
     */
    public function getBirdWeightTarget()
    {
        return $this->birdWeightTarget;
    }

    /**
     * @param string $birdWeightTarget
     */
    public function setBirdWeightTarget($birdWeightTarget)
    {
        $this->birdWeightTarget = $birdWeightTarget;
    }



    /**
     * @return string
     */
    public function getFeedIntakePerBird()
    {
        return $this->feedIntakePerBird;
    }

    /**
     * @param string $feedIntakePerBird
     */
    public function setFeedIntakePerBird($feedIntakePerBird)
    {
        $this->feedIntakePerBird = $feedIntakePerBird;
    }

    /**
     * @return string
     */
    public function getFeedTarget()
    {
        return $this->feedTarget;
    }

    /**
     * @param string $feedTarget
     */
    public function setFeedTarget($feedTarget)
    {
        $this->feedTarget = $feedTarget;
    }

    /**
     * @return string
     */
    public function getEggProductionAchieved()
    {
        return $this->eggProductionAchieved;
    }

    /**
     * @param string $eggProductionAchieved
     */
    public function setEggProductionAchieved($eggProductionAchieved)
    {
        $this->eggProductionAchieved = $eggProductionAchieved;
    }

    /**
     * @return string
     */
    public function getEggProductionTarget()
    {
        return $this->eggProductionTarget;
    }

    /**
     * @param string $eggProductionTarget
     */
    public function setEggProductionTarget($eggProductionTarget)
    {
        $this->eggProductionTarget = $eggProductionTarget;
    }

    /**
     * @return string
     */
    public function getEggWeightAchieved()
    {
        return $this->eggWeightAchieved;
    }

    /**
     * @param string $eggWeightAchieved
     */
    public function setEggWeightAchieved($eggWeightAchieved)
    {
        $this->eggWeightAchieved = $eggWeightAchieved;
    }

    /**
     * @return string
     */
    public function getEggWeightStand()
    {
        return $this->eggWeightStand;
    }

    /**
     * @param string $eggWeightStand
     */
    public function setEggWeightStand($eggWeightStand)
    {
        $this->eggWeightStand = $eggWeightStand;
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
     * @var string
     * @ORM\Column(name="egg_production_achieved", type="string")
     */

    private $eggProductionAchieved;


    /**
     * @var string
     * @ORM\Column(name="egg_production_target", type="string")
     */

    private $eggProductionTarget;

    /**
     * @var string
     * @ORM\Column(name="egg_weight_achieved", type="string")
     */

    private $eggWeightAchieved;

    /**
     * @var string
     * @ORM\Column(name="egg_weight_stand", type="string")
     */

    private $eggWeightStand;



    /**
     * @var string
     * @ORM\Column(name="feed_type", type="string",nullable=true)
     */

    private $feedType;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="layerPerformance")
     */
    private $customer;


    /**
     * @var string
     * @ORM\Column(name="production_date", type="string",nullable=true)
     */

    private $productionDate;


    /**
     * @var string
     * @ORM\Column(name="batch_no", type="string",nullable=true)
     */

    private $batch_no;

    /**
     * @var string
     * @ORM\Column(name="feed_mill", type="string",nullable=true)
     */

    private $feedMill;

    /**
     * @var string
     * @ORM\Column(name="hatchery", type="string",nullable=true)
     */

    private $hatchery;

    /**
     * @var string
     * @ORM\Column(name="breed", type="string",nullable=true)
     */

    private $breed;


    /**
     * @var string
     * @ORM\Column(name="color", type="string",nullable=true)
     */

    private $color;


    /**
     * @var string
     * @ORM\Column(name="disease", type="string",nullable=true)
     */

    private $disease;


    /**
     * @var string
     * @Orm\Column(name="remarks", type="text",nullable=true)
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
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getDisease()
    {
        return $this->disease;
    }

    /**
     * @param string $disease
     */
    public function setDisease($disease)
    {
        $this->disease = $disease;
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
     * @return CrmCustomer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param CrmCustomer $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
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
    public function setAgent($agent)
    {
        $this->agent = $agent;
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
    public function setEmployee(User $employee)
    {
        $this->employee = $employee;
    }


}
