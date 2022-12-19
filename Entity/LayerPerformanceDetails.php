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
use NumberFormatter;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\LayerPerformanceDetailsRepository")
 * @ORM\Table(name="crm_layer_performance_details")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerPerformanceDetails
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="crmLayerPerformanceDetails")
     */
    private $employee;

    /**
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmVisit" , inversedBy="crmLayerPerformanceDetails")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $visit;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerPerformanceDetails")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $report;
    /**
     * @var \DateTime
     * @ORM\Column(name="repoting_month", type="date", nullable=true)
     */

    private $reportingMonth;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="crmLayerPerformanceDetails")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="crmLayerPerformanceDetails")
     */
    private $customer;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerPerformanceDetails")
     * @ORM\JoinColumn(name="hatchery_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $hatchery;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerPerformanceDetails")
     * @ORM\JoinColumn(name="breed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $breed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerPerformanceDetails")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerPerformanceDetails")
     * @ORM\JoinColumn(name="feed_mill_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedMill;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerPerformanceDetails")
     * @ORM\JoinColumn(name="feed_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedType;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerPerformanceDetails")
     * @ORM\JoinColumn(name="color_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $color;

    /**
     * @var float
     * @ORM\Column(name="total_birds", type="float")
     */
    private $totalBirds=0;

    /**
     * @var float
     * @ORM\Column(name="age_week", type="float")
     */
    private $ageWeek=0;

    /**
     * @var float
     * @ORM\Column(name="bird_weight_achieved", type="float")
     */

    private $birdWeightAchieved=0;

    /**
     * @var float
     * @ORM\Column(name="bird_weight_target", type="float")
     */

    private $birdWeightTarget=0;

    /**
     * @var float
     * @ORM\Column(name="feed_intake_per_bird", type="float")
     */

    private $feedIntakePerBird=0;

    /**
     * @var float
     * @ORM\Column(name="feed_Target", type="float")
     */

    private $feedTarget=0;

    /**
     * @var float
     * @ORM\Column(name="egg_production_achieved", type="float")
     */

    private $eggProductionAchieved=0;


    /**
     * @var float
     * @ORM\Column(name="egg_production_target", type="float")
     */

    private $eggProductionTarget=0;

    /**
     * @var float
     * @ORM\Column(name="egg_weight_achieved", type="float")
     */

    private $eggWeightAchieved=0;

    /**
     * @var float
     * @ORM\Column(name="egg_weight_stand", type="float")
     */

    private $eggWeightStand=0;

    /**
     * @var \DateTime
     * @ORM\Column(name="production_date", type="date", nullable=true)
     */

    private $productionDate;

    /**
     * @var string
     * @ORM\Column(name="batch_no", type="string",nullable=true)
     */

    private $batch_no;

    /**
     * @var string
     * @ORM\Column(name="disease", type="string", nullable=true)
     */
    private $disease;

    /**
     * @var string
     * @Orm\Column(name="remarks", type="text", nullable=true)
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
     * @var Api
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Api", inversedBy="appBatch")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    
    private $appBatch;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */

    private $appId;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="crmLayerPerformanceDetails")
     */
    private $deletedBy;

    /**
     * @var \DateTime
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;
    
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
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param Agent $agent
     */
    public function setAgent(Agent $agent): void
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
    public function getAgeWeek()
    {
        return $this->ageWeek;
    }

    /**
     * @param float $ageWeek
     */
    public function setAgeWeek(float $ageWeek): void
    {
        $this->ageWeek = $ageWeek;
    }
    public function getFormattingAgeWeek()
    {
        /*$locale = 'en_US';
        $nf = new NumberFormatter($locale, NumberFormatter::ORDINAL);
        return $nf->format($this->ageWeek).' week';*/

        return $this->ageWeek;
    }

    /**
     * @return float
     */
    public function getBirdWeightAchieved()
    {
        return $this->birdWeightAchieved;
    }

    /**
     * @param float $birdWeightAchieved
     */
    public function setBirdWeightAchieved(float $birdWeightAchieved): void
    {
        $this->birdWeightAchieved = $birdWeightAchieved;
    }

    /**
     * @return float
     */
    public function getBirdWeightTarget()
    {
        return $this->birdWeightTarget;
    }

    /**
     * @param float $birdWeightTarget
     */
    public function setBirdWeightTarget(float $birdWeightTarget): void
    {
        $this->birdWeightTarget = $birdWeightTarget;
    }

    /**
     * @return float
     */
    public function getFeedIntakePerBird()
    {
        return $this->feedIntakePerBird;
    }

    /**
     * @param float $feedIntakePerBird
     */
    public function setFeedIntakePerBird(float $feedIntakePerBird): void
    {
        $this->feedIntakePerBird = $feedIntakePerBird;
    }

    /**
     * @return float
     */
    public function getFeedTarget()
    {
        return $this->feedTarget;
    }

    /**
     * @param float $feedTarget
     */
    public function setFeedTarget(float $feedTarget): void
    {
        $this->feedTarget = $feedTarget;
    }

    /**
     * @return float
     */
    public function getEggProductionAchieved()
    {
        return $this->eggProductionAchieved;
    }

    /**
     * @param float $eggProductionAchieved
     */
    public function setEggProductionAchieved(float $eggProductionAchieved): void
    {
        $this->eggProductionAchieved = $eggProductionAchieved;
    }

    /**
     * @return float
     */
    public function getEggProductionTarget()
    {
        return $this->eggProductionTarget;
    }

    /**
     * @param float $eggProductionTarget
     */
    public function setEggProductionTarget(float $eggProductionTarget): void
    {
        $this->eggProductionTarget = $eggProductionTarget;
    }

    /**
     * @return float
     */
    public function getEggWeightAchieved()
    {
        return $this->eggWeightAchieved;
    }

    /**
     * @param float $eggWeightAchieved
     */
    public function setEggWeightAchieved(float $eggWeightAchieved): void
    {
        $this->eggWeightAchieved = $eggWeightAchieved;
    }

    /**
     * @return float
     */
    public function getEggWeightStand()
    {
        return $this->eggWeightStand;
    }

    /**
     * @param float $eggWeightStand
     */
    public function setEggWeightStand(float $eggWeightStand): void
    {
        $this->eggWeightStand = $eggWeightStand;
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
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param Setting $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
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
     * @return Setting
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param Setting $color
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
    public function setDisease(string $disease): void
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

    /**
     * @return User
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param User $employee
     */
    public function setEmployee(User $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return mixed
     */
    public function getVisit()
    {
        return $this->visit;
    }

    /**
     * @param mixed $visit
     */
    public function setVisit($visit): void
    {
        $this->visit = $visit;
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
    public function setReport(Setting $report): void
    {
        $this->report = $report;
    }

    /**
     * @return \DateTime
     */
    public function getReportingMonth()
    {
        return $this->reportingMonth;
    }

    /**
     * @param \DateTime $reportingMonth
     */
    public function setReportingMonth(\DateTime $reportingMonth): void
    {
        $this->reportingMonth = $reportingMonth;
    }

    /**
     * @return Api
     */
    public function getAppBatch()
    {
        return $this->appBatch;
    }

    /**
     * @param Api $appBatch
     */
    public function setAppBatch(Api $appBatch): void
    {
        $this->appBatch = $appBatch;
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
    public function setAppId(int $appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return User
     */
    public function getDeletedBy(): User
    {
        return $this->deletedBy;
    }

    /**
     * @param User $deletedBy
     */
    public function setDeletedBy(User $deletedBy): void
    {
        $this->deletedBy = $deletedBy;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt(\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }



}
