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
 * @ORM\Table(name="crm_cattle_life_cycle_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CattleLifeCycleRepository")
 */
class CattleLifeCycleDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var CattleLifeCycle
     * @ORM\ManyToOne(targetEntity="CattleLifeCycle", inversedBy="crmCattleLifeCycleDetails")
     * @ORM\JoinColumn(name="crm_cattle_life_cycle_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $crmCattleLifeCycle;

    /**
     * @var \DateTime
     * @ORM\Column(name="visiting_date", type="date", nullable=true)
     */

    private $visitingDate;

    /**
     * @var float
     * @Orm\Column(name="age_of_cattle_month", type="float")
     */
    private $ageOfCattleMonth=0;

    /**
     * @var float
     * @Orm\Column(name="previous_body_weight", type="float")
     */

    private $previousBodyWeight=0;

    /**
     * @var float
     * @Orm\Column(name="present_body_weight", type="float")
     */

    private $presentBodyWeight=0;

    /**
     * @var float
     * @Orm\Column(name="body_weight_difference", type="float")
     */

    private $bodyWeightDifference=0;

    /**
     * @var float
     * @Orm\Column(name="duration_of_bwt_difference", type="float")
     */

    private $durationOfBwtDifference=0;

    /**
     * @var float
     * @Orm\Column(name="lactation_no", type="float")
     */

    private $lactationNo=0;

    /**
     * @var float
     * @Orm\Column(name="age_of_lactation", type="float")
     */

    private $ageOfLactation=0;

    /**
     * @var float
     * @Orm\Column(name="average_weight_per_day", type="float")
     */

    private $averageWeightPerDay=0;

    /**
     * @var float
     * @Orm\Column(name="average_weight_per_kg_consumption_feed", type="float")
     */

    private $averageWeightPerKgConsumptionFeed=0;

    /**
     * @var float
     * @Orm\Column(name="average_weight_per_kg_dm", type="float")
     */

    private $averageWeightPerKgDm=0;

    /**
     * @var float
     * @Orm\Column(name="milk_fat_percentage", type="float")
     */

    private $milkFatPercentage=0;

    /**
     * @var float
     * @Orm\Column(name="consumption_feed_intake_ready_feed", type="float")
     */

    private $consumptionFeedIntakeReadyFeed=0;

    /**
     * @var float
     * @Orm\Column(name="consumption_feed_intake_conventional", type="float")
     */

    private $consumptionFeedIntakeConventional=0;

    /**
     * @var float
     * @Orm\Column(name="consumption_feed_intake_total", type="float")
     */

    private $consumptionFeedIntakeTotal=0;

    /**
     * @var float
     * @Orm\Column(name="fodder_green_grass_kg", type="float")
     */

    private $fodderGreenGrassKg=0;

    /**
     * @var float
     * @Orm\Column(name="fodder_straw_kg", type="float")
     */

    private $fodderStrawKg=0;

    /**
     * @var float
     * @Orm\Column(name="dm_of_fodder_green_grass_kg", type="float")
     */

    private $dmOfFodderGreenGrassKg=0;

    /**
     * @var float
     * @Orm\Column(name="dm_of_fodder_straw_kg", type="float")
     */

    private $dmOfFodderStrawKg=0;

    /**
     * @var float
     * @Orm\Column(name="total_dm_kg", type="float")
     */

    private $totalDmKg=0;

    /**
     * @var float
     * @Orm\Column(name="dm_requirement_by_bwt_kg", type="float")
     */

    private $dmRequirementByBwtKg=0;

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
     * @return CattleLifeCycle
     */
    public function getCrmCattleLifeCycle(): CattleLifeCycle
    {
        return $this->crmCattleLifeCycle;
    }

    /**
     * @param CattleLifeCycle $crmCattleLifeCycle
     */
    public function setCrmCattleLifeCycle(CattleLifeCycle $crmCattleLifeCycle): void
    {
        $this->crmCattleLifeCycle = $crmCattleLifeCycle;
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
    public function getAgeOfCattleMonth()
    {
        return $this->ageOfCattleMonth;
    }

    /**
     * @param float $ageOfCattleMonth
     */
    public function setAgeOfCattleMonth($ageOfCattleMonth): void
    {
        $this->ageOfCattleMonth = $ageOfCattleMonth;
    }

    /**
     * @return float
     */
    public function getPreviousBodyWeight()
    {
        return $this->previousBodyWeight;
    }

    /**
     * @param float $previousBodyWeight
     */
    public function setPreviousBodyWeight($previousBodyWeight): void
    {
        $this->previousBodyWeight = $previousBodyWeight;
    }

    /**
     * @return float
     */
    public function getPresentBodyWeight()
    {
        return $this->presentBodyWeight;
    }

    /**
     * @param float $presentBodyWeight
     */
    public function setPresentBodyWeight($presentBodyWeight): void
    {
        $this->presentBodyWeight = $presentBodyWeight;
    }

    /**
     * @return float
     */
    public function getBodyWeightDifference()
    {
        return $this->bodyWeightDifference;
    }

    /**
     * @param float $bodyWeightDifference
     */
    public function setBodyWeightDifference($bodyWeightDifference): void
    {
        $this->bodyWeightDifference = $bodyWeightDifference;
    }

    public function calculateBodyWeightDifference(){
        return $this->getPresentBodyWeight()-$this->getPreviousBodyWeight();
    }

    /**
     * @return float
     */
    public function getDurationOfBwtDifference()
    {
        return $this->durationOfBwtDifference;
    }

    /**
     * @param float $durationOfBwtDifference
     */
    public function setDurationOfBwtDifference($durationOfBwtDifference): void
    {
        $this->durationOfBwtDifference = $durationOfBwtDifference;
    }

    /**
     * @return float
     */
    public function getLactationNo()
    {
        return $this->lactationNo;
    }

    /**
     * @param float $lactationNo
     */
    public function setLactationNo($lactationNo): void
    {
        $this->lactationNo = $lactationNo;
    }

    /**
     * @return float
     */
    public function getAgeOfLactation()
    {
        return $this->ageOfLactation;
    }

    /**
     * @param float $ageOfLactation
     */
    public function setAgeOfLactation($ageOfLactation): void
    {
        $this->ageOfLactation = $ageOfLactation;
    }

    /**
     * @return float
     */
    public function getAverageWeightPerDay()
    {
        $result= $this->averageWeightPerDay;
        return number_format($result,2,'.','');
    }

    /**
     * @param float $averageWeightPerDay
     */
    public function setAverageWeightPerDay($averageWeightPerDay): void
    {
        $this->averageWeightPerDay = $averageWeightPerDay;
    }

    public function calculateAverageWeightPerDay(){
        $result = 0;
        if($this->getDurationOfBwtDifference()>0){
            $result= $this->getBodyWeightDifference()/$this->getDurationOfBwtDifference();
        }
        return $result;
    }

    /**
     * @return float
     */
    public function getAverageWeightPerKgConsumptionFeed()
    {
        $result=$this->averageWeightPerKgConsumptionFeed;
        return number_format($result,2,'.','');
    }

    /**
     * @param float $averageWeightPerKgConsumptionFeed
     */
    public function setAverageWeightPerKgConsumptionFeed($averageWeightPerKgConsumptionFeed): void
    {
        $this->averageWeightPerKgConsumptionFeed = $averageWeightPerKgConsumptionFeed;
    }

    public function calculateAverageWeightPerKgConsumptionFeed(){
        $result = 0;
        $totalFeedIntakeTotal = $this->getConsumptionFeedIntakeReadyFeed()+$this->getConsumptionFeedIntakeConventional();
        if($totalFeedIntakeTotal>0){
            $result = $this->getAverageWeightPerDay()/$totalFeedIntakeTotal;
        }
        return $result;
    }

    /**
     * @return float
     */
    public function getAverageWeightPerKgDm()
    {
        $result=$this->averageWeightPerKgDm;
        return number_format($result,2,'.','');
    }

    /**
     * @param float $averageWeightPerKgDm
     */
    public function setAverageWeightPerKgDm($averageWeightPerKgDm): void
    {
        $this->averageWeightPerKgDm = $averageWeightPerKgDm;
    }

    public function calculateAverageWeightPerKgDm(){
        $result = 0;
        $feedIntakeTotal= $this->getConsumptionFeedIntakeReadyFeed()+$this->getConsumptionFeedIntakeConventional();
        $totalDmKg = $feedIntakeTotal+$this->getDmOfFodderGreenGrassKg()+$this->getDmOfFodderStrawKg();
        if($totalDmKg>0){
            $result = $this->getAverageWeightPerDay()/$totalDmKg;
        }
        return $result;
    }

    /**
     * @return float
     */
    public function getMilkFatPercentage()
    {
        return $this->milkFatPercentage;
    }

    /**
     * @param float $milkFatPercentage
     */
    public function setMilkFatPercentage($milkFatPercentage): void
    {
        $this->milkFatPercentage = $milkFatPercentage;
    }

    /**
     * @return float
     */
    public function getConsumptionFeedIntakeReadyFeed()
    {
        return $this->consumptionFeedIntakeReadyFeed;
    }

    /**
     * @param float $consumptionFeedIntakeReadyFeed
     */
    public function setConsumptionFeedIntakeReadyFeed($consumptionFeedIntakeReadyFeed): void
    {
        $this->consumptionFeedIntakeReadyFeed = $consumptionFeedIntakeReadyFeed;
    }

    /**
     * @return float
     */
    public function getConsumptionFeedIntakeConventional()
    {
        return $this->consumptionFeedIntakeConventional;
    }

    /**
     * @param float $consumptionFeedIntakeConventional
     */
    public function setConsumptionFeedIntakeConventional($consumptionFeedIntakeConventional): void
    {
        $this->consumptionFeedIntakeConventional = $consumptionFeedIntakeConventional;
    }

    /**
     * @return float
     */
    public function getConsumptionFeedIntakeTotal()
    {
        $result=$this->consumptionFeedIntakeTotal;
        return number_format($result,2,'.','');
    }

    /**
     * @param float $consumptionFeedIntakeTotal
     */
    public function setConsumptionFeedIntakeTotal($consumptionFeedIntakeTotal): void
    {
        $this->consumptionFeedIntakeTotal = $consumptionFeedIntakeTotal;
    }

    public function calculationConsumptionFeedIntakeTotal(){
        $result= $this->getConsumptionFeedIntakeReadyFeed()+$this->getConsumptionFeedIntakeConventional();
        return $result;
    }

    /**
     * @return float
     */
    public function getFodderGreenGrassKg()
    {
        return $this->fodderGreenGrassKg;
    }

    /**
     * @param float $fodderGreenGrassKg
     */
    public function setFodderGreenGrassKg($fodderGreenGrassKg): void
    {
        $this->fodderGreenGrassKg = $fodderGreenGrassKg;
    }

    /**
     * @return float
     */
    public function getFodderStrawKg()
    {
        return $this->fodderStrawKg;
    }

    /**
     * @param float $fodderStrawKg
     */
    public function setFodderStrawKg($fodderStrawKg): void
    {
        $this->fodderStrawKg = $fodderStrawKg;
    }

    /**
     * @return float
     */
    public function getDmOfFodderGreenGrassKg()
    {
        $result=$this->dmOfFodderGreenGrassKg;
        return number_format($result,2,'.','');
    }

    /**
     * @param float $dmOfFodderGreenGrassKg
     */
    public function setDmOfFodderGreenGrassKg($dmOfFodderGreenGrassKg): void
    {
        $this->dmOfFodderGreenGrassKg = $dmOfFodderGreenGrassKg;
    }

    public function calculateDmOfFodderGreenGrassKg()
    {
        $result = ($this->getFodderGreenGrassKg()*15)/100;
        return $result;
    }

    /**
     * @return float
     */
    public function getDmOfFodderStrawKg()
    {
        $result = $this->dmOfFodderStrawKg;
        return number_format($result,2,'.','');
    }

    /**
     * @param float $dmOfFodderStrawKg
     */
    public function setDmOfFodderStrawKg($dmOfFodderStrawKg): void
    {
        $this->dmOfFodderStrawKg = $dmOfFodderStrawKg;
    }

    public function calculateDmOfFodderStrawKg()
    {
        $result = ($this->getFodderStrawKg()*85)/100;
        return $result;
    }

    /**
     * @return float
     */
    public function getTotalDmKg()
    {
        $result = $this->totalDmKg;
        return number_format($result,2,'.','');
    }

    /**
     * @param float $totalDmKg
     */
    public function setTotalDmKg($totalDmKg): void
    {
        $this->totalDmKg = $totalDmKg;
    }

    public function calculateTotalDmKg(){
        $feedIntakeTotal = $this->getConsumptionFeedIntakeReadyFeed()+$this->getConsumptionFeedIntakeConventional();
        $result = $feedIntakeTotal+$this->getDmOfFodderGreenGrassKg()+$this->getDmOfFodderStrawKg();
        return $result;
    }
    /**
     * @return float
     */
    public function getDmRequirementByBwtKg()
    {
        $result = $this->dmRequirementByBwtKg;
        return number_format($result,2,'.','');
    }

    /**
     * @param float $dmRequirementByBwtKg
     */
    public function setDmRequirementByBwtKg($dmRequirementByBwtKg): void
    {
        $this->dmRequirementByBwtKg = $dmRequirementByBwtKg;
    }

    public function calculateDmRequirementByBwtKg()
    {
        $result = ($this->getPreviousBodyWeight()*3)/100;
        return $result;
    }
    public function calculateDmRequirementByBwtKgForDairy()
    {
        $result = (($this->getPresentBodyWeight()*2)/100)+(($this->getAverageWeightPerDay()*33)/100);
        return $result;
    }
    /**
     * @return string
     */
    public function getNameOfReadyFeed()
    {
        return $this->getCrmCattleLifeCycle()->getFeedType()?$this->getCrmCattleLifeCycle()->getFeedType()->getName():'';
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
