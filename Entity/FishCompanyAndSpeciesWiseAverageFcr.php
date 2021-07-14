<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fish_company_species_wise_average_fcr")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FishCompanyAndSpeciesWiseAverageFcrRepository")
 */
class FishCompanyAndSpeciesWiseAverageFcr
{

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="fcr_of_feed", type="string", nullable=true)
     */

    private $fcrOfFeed; //BEFORE OR AFTER

    /**
     * @var FishCompanyAndSpeciesWiseAverageFcrDetails
     * @ORM\OneToMany(targetEntity="FishCompanyAndSpeciesWiseAverageFcrDetails", mappedBy="fishCompanyAndSpeciesWiseAverageFcr")
     */
    private $fishCompanyAndSpeciesWiseAverageFcrDetails;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishCompanyAndSpeciesWiseAverageFcr")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="fishCompanyAndSpeciesWiseAverageFcr")
     */
    private $employee;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="fishCompanyAndSpeciesWiseAverageFcr")
     */
    private $agent;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="fishCompanyAndSpeciesWiseAverageFcr")
     */
    private $customer;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishCompanyAndSpeciesWiseAverageFcr")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishCompanyAndSpeciesWiseAverageFcr")
     * @ORM\JoinColumn(name="feed_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedType;

    /**
     * @var \DateTime
     * @ORM\Column(name="reporting_month", type="date", nullable=true)
     */
    private $reportingMonth;

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
    public function getReportingMonth()
    {
        return $this->reportingMonth;
    }

    /**
     * @param \DateTime $reportingMonth
     * @ORM\PrePersist
     */
    public function setReportingMonth(\DateTime $reportingMonth): void
    {
        $this->reportingMonth = $reportingMonth;
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
     * @return string
     */
    public function getFcrOfFeed()
    {
        return $this->fcrOfFeed;
    }

    /**
     * @param string $fcrOfFeed
     */
    public function setFcrOfFeed(string $fcrOfFeed): void
    {
        $this->fcrOfFeed = $fcrOfFeed;
    }

    /**
     * @return FishCompanyAndSpeciesWiseAverageFcrDetails
     */
    public function getFishCompanyAndSpeciesWiseAverageFcrDetails()
    {
        return $this->fishCompanyAndSpeciesWiseAverageFcrDetails;
    }

    /**
     * @param FishCompanyAndSpeciesWiseAverageFcrDetails $fishCompanyAndSpeciesWiseAverageFcrDetails
     */
    public function setFishCompanyAndSpeciesWiseAverageFcrDetails(FishCompanyAndSpeciesWiseAverageFcrDetails $fishCompanyAndSpeciesWiseAverageFcrDetails): void
    {
        $this->fishCompanyAndSpeciesWiseAverageFcrDetails = $fishCompanyAndSpeciesWiseAverageFcrDetails;
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
     * @return Setting
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param Setting $feed
     */
    public function setFeed(Setting $feed): void
    {
        $this->feed = $feed;
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
    public function setFeedType(Setting $feedType): void
    {
        $this->feedType = $feedType;
    }


}
