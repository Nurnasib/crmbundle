<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fcr_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FcrDetailsRepository")
 */
class FcrDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var Fcr
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Fcr", inversedBy="fcrDetails")
     * @ORM\JoinColumn(name="fcr_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $fcr;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="fcr")
     */
    private $agent;

    /**
     * @var \DateTime
     * @ORM\Column(name="hatching_date", type="date", nullable=true)
     */
    private $hatchingDate;

    /**
     * @var float
     *
     * @ORM\Column(name="total_birds", type="float")
     */

    private $totalBirds=0;

    /**
     * @var float
     * @Orm\Column(name="age_day", type="float")
     */
    private $ageDay=0;

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
     * @Orm\Column(name="weight", type="float")
     */
    private $weight=0;

    /**
     * @var float
     * @Orm\Column(name="feed_consumption_total_kg", type="float")
     */
    private $feedConsumptionTotalKg=0;

    /**
     * @var float
     * @Orm\Column(name="feed_consumption_per_bird", type="float")
     */
    private $feedConsumptionPerBird=0;

    /**
     * @var float
     * @Orm\Column(name="fcr_without_mortality", type="float")
     */

    private $fcrWithoutMortality=0;

    /**
     * @var float
     * @Orm\Column(name="fcr_with_mortality", type="float")
     */

    private $fcrWithMortality=0;

    /**
     * @var string
     * @Orm\Column(name="hatchery", type="string", nullable=true)
     */
    private $hatchery;

    /**
     * @var string
     * @Orm\Column(name="breed", type="string", nullable=true)
     */
    private $breed;

    /**
     * @var string
     * @Orm\Column(name="feed", type="string", nullable=true)
     */
    private $feed;

    /**
     * @var string
     * @Orm\Column(name="feed_mill", type="string", nullable=true)
     */
    private $feedMill;

    /**
     * @var string
     * @Orm\Column(name="feed_type", type="string", nullable=true)
     */
    private $feedType;

    /**
     * @var \DateTime
     * @ORM\Column(name="pro_date", type="date", nullable=true)
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
     * @return \DateTime
     */
    public function getHatchingDate()
    {
        return $this->hatchingDate;
    }

    /**
     * @param \DateTime $hatchingDate
     */
    public function setHatchingDate(\DateTime $hatchingDate): void
    {
        $this->hatchingDate = $hatchingDate;
    }

    /**
     * @return Fcr
     */
    public function getFcr()
    {
        return $this->fcr;
    }

    /**
     * @param Fcr $fcr
     */
    public function setFcr(Fcr $fcr): void
    {
        $this->fcr = $fcr;
    }

    /**
     * @return float
     */
    public function getTotalBirds()
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
    public function getAgeDay(): float
    {
        return $this->ageDay;
    }

    /**
     * @param float $ageDay
     */
    public function setAgeDay(float $ageDay): void
    {
        $this->ageDay = $ageDay;
    }

    /**
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param string $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return float
     */
    public function getFeedConsumptionTotalKg(): float
    {
        return $this->feedConsumptionTotalKg;
    }

    /**
     * @param float $feedConsumptionTotalKg
     */
    public function setFeedConsumptionTotalKg(float $feedConsumptionTotalKg): void
    {
        $this->feedConsumptionTotalKg = $feedConsumptionTotalKg;
    }

    /**
     * @return float
     */
    public function getFeedConsumptionPerBird(): float
    {
        return $this->feedConsumptionPerBird;
    }

    /**
     * @param float $feedConsumptionPerBird
     */
    public function setFeedConsumptionPerBird(float $feedConsumptionPerBird): void
    {
        $this->feedConsumptionPerBird = $feedConsumptionPerBird;
    }

    public function calculatePerBird(){
        $result = 0;
        if($this->getTotalBirds()>0) {
            $result =  number_format((($this->getFeedConsumptionTotalKg()/$this->getTotalBirds())*1000),2,'.','');
        }
        return number_format($result,2,'.','');
    }

    /**
     * @return float
     */
    public function getFcrWithoutMortality(): float
    {
        return $this->fcrWithoutMortality;
    }

    /**
     * @param float $fcrWithoutMortality
     */
    public function setFcrWithoutMortality(float $fcrWithoutMortality): void
    {
        $this->fcrWithoutMortality = $fcrWithoutMortality;
    }

    public function calculateWithoutMortality(){
        $result = 0;
        if($this->getTotalBirds()>0 && $this->getWeight()>0) {

            $result = (($this->getFeedConsumptionTotalKg() / $this->getTotalBirds()) / $this->getWeight()) * 1000;
        }
        return number_format($result,2,'.','');

    }
    /**
     * @return float
     */
    public function getFcrWithMortality(): float
    {
        return $this->fcrWithMortality;
    }

    /**
     * @param float $fcrWithMortality
     */
    public function setFcrWithMortality(float $fcrWithMortality): void
    {
        $this->fcrWithMortality = $fcrWithMortality;
    }

    public function calculateWithMortality(){
        $result = 0;
        if($this->getTotalBirds()>0 && $this->getWeight()>0){

            $result = (($this->getFeedConsumptionTotalKg()/($this->getTotalBirds()-$this->getMortalityPes()))/$this->getWeight())*1000;
        }

        return number_format($result,2,'.','');

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
    public function setFeedMill(string $feedMill): void
    {
        $this->feedMill = $feedMill;
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
    public function setFeedType(string $feedType): void
    {
        $this->feedType = $feedType;
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
    public function getHatchery()
    {
        return $this->hatchery;
    }

    /**
     * @param string $hatchery
     */
    public function setHatchery(string $hatchery): void
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
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param string $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
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



    public function calculateMortalityPercent(){
        $return = 0;
        if($this->getTotalBirds()>0){
            $return = number_format(($this->getMortalityPes()*100)/$this->getTotalBirds(),2,'.','');
        }
        return $return;
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
    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;
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
