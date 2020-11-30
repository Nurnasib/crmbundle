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
 * @ORM\Table(name="crm_cattle_farm_visit_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CattleFarmVisitDetailsRepository")
 */
class CattleFarmVisitDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var CattleFarmVisit
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CattleFarmVisit", inversedBy="crmCattleFarmVisitDetails")
     * @ORM\JoinColumn(name="crm_cattle_performance_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $crmCattleFarmVisit;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="crmCattleFarmVisitDetails")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="crmCattleFarmVisitDetails")
     */
    private $customer;

    /**
     * @var \DateTime
     * @ORM\Column(name="visiting_date", type="date", nullable=true)
     */

    private $visitingDate;

    /**
     * @var float
     * @Orm\Column(name="cattlePopulationOx", type="float")
     */
    private $cattlePopulationOx=0;

    /**
     * @var float
     * @Orm\Column(name="cattlePopulationCow", type="float")
     */
    private $cattlePopulationCow=0;

    /**
     * @var float
     * @Orm\Column(name="cattlePopulationCalf", type="float")
     */
    private $cattlePopulationCalf=0;

    /**
     * @var float
     * @Orm\Column(name="avgMilkYieldPerDay", type="float")
     */

    private $avgMilkYieldPerDay=0;

    /**
     * @var float
     * @Orm\Column(name="conceptionRate", type="float")
     */

    private $conceptionRate=0;

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
     * @var string
     * @Orm\Column(name="typeOfConcentrateFeed", type="string", nullable=true)
     */

    private $typeOfConcentrateFeed;

    /**
     * @var float
     * @Orm\Column(name="marketPriceMilkPerLiter", type="float")
     */

    private $marketPriceMilkPerLiter=0;

    /**
     * @var float
     * @Orm\Column(name="marketPriceMeatPerKg", type="float")
     */

    private $marketPriceMeatPerKg=0;

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
     * @return CattleFarmVisit
     */
    public function getCrmCattleFarmVisit()
    {
        return $this->crmCattleFarmVisit;
    }

    /**
     * @param CattleFarmVisit $crmCattleFarmVisit
     */
    public function setCrmCattleFarmVisit($crmCattleFarmVisit)
    {
        $this->crmCattleFarmVisit = $crmCattleFarmVisit;
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
    public function getCattlePopulationOx()
    {
        return $this->cattlePopulationOx;
    }

    /**
     * @param float $cattlePopulationOx
     */
    public function setCattlePopulationOx($cattlePopulationOx)
    {
        $this->cattlePopulationOx = $cattlePopulationOx;
    }

    /**
     * @return float
     */
    public function getCattlePopulationCow()
    {
        return $this->cattlePopulationCow;
    }

    /**
     * @param float $cattlePopulationCow
     */
    public function setCattlePopulationCow($cattlePopulationCow)
    {
        $this->cattlePopulationCow = $cattlePopulationCow;
    }

    /**
     * @return float
     */
    public function getCattlePopulationCalf()
    {
        return $this->cattlePopulationCalf;
    }

    /**
     * @param float $cattlePopulationCalf
     */
    public function setCattlePopulationCalf($cattlePopulationCalf)
    {
        $this->cattlePopulationCalf = $cattlePopulationCalf;
    }

    /**
     * @return float
     */
    public function getAvgMilkYieldPerDay()
    {
        return $this->avgMilkYieldPerDay;
    }

    /**
     * @param float $avgMilkYieldPerDay
     */
    public function setAvgMilkYieldPerDay($avgMilkYieldPerDay)
    {
        $this->avgMilkYieldPerDay = $avgMilkYieldPerDay;
    }

    /**
     * @return float
     */
    public function getConceptionRate()
    {
        return $this->conceptionRate;
    }

    /**
     * @param float $conceptionRate
     */
    public function setConceptionRate($conceptionRate)
    {
        $this->conceptionRate = $conceptionRate;
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
    public function setFodderGreenGrassKg($fodderGreenGrassKg)
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
    public function setFodderStrawKg($fodderStrawKg)
    {
        $this->fodderStrawKg = $fodderStrawKg;
    }

    /**
     * @return string
     */
    public function getTypeOfConcentrateFeed()
    {
        return $this->typeOfConcentrateFeed;
    }

    /**
     * @param string $typeOfConcentrateFeed
     */
    public function setTypeOfConcentrateFeed($typeOfConcentrateFeed)
    {
        $this->typeOfConcentrateFeed = $typeOfConcentrateFeed;
    }

    /**
     * @return float
     */
    public function getMarketPriceMilkPerLiter()
    {
        return $this->marketPriceMilkPerLiter;
    }

    /**
     * @param float $marketPriceMilkPerLiter
     */
    public function setMarketPriceMilkPerLiter($marketPriceMilkPerLiter)
    {
        $this->marketPriceMilkPerLiter = $marketPriceMilkPerLiter;
    }

    /**
     * @return float
     */
    public function getMarketPriceMeatPerKg()
    {
        return $this->marketPriceMeatPerKg;
    }

    /**
     * @param float $marketPriceMeatPerKg
     */
    public function setMarketPriceMeatPerKg($marketPriceMeatPerKg)
    {
        $this->marketPriceMeatPerKg = $marketPriceMeatPerKg;
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
