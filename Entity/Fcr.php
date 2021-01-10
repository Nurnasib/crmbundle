<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fcr")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FcrRepository")
 */
class Fcr
{
    const FCR_FEED_BEFORE = 'BEFORE';
    const FCR_FEED_AFTER = 'AFTER';
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var FcrDetails
     * @ORM\OneToMany(targetEntity="FcrDetails", mappedBy="fcr")
     */
    private $fcrDetails;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fcr")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="fcr")
     */
    private $employee;

    /**
     * @var string
     * @ORM\Column(name="fcr_of_feed", type="string",nullable=true)
     */

    private $fcrOfFeed;

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
     * @return string
     */
    public function getFcrOfFeed()
    {
        return $this->fcrOfFeed;
    }

    /**
     * @param string $fcrOfFeed
     */
    public function setFcrOfFeed($fcrOfFeed)
    {
        $this->fcrOfFeed = $fcrOfFeed;
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
     * @return FcrDetails
     */
    public function getFcrDetails()
    {
        return $this->fcrDetails;
    }

    /**
     * @param FcrDetails $fcrDetails
     */
    public function setFcrDetails(FcrDetails $fcrDetails): void
    {
        $this->fcrDetails = $fcrDetails;
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
    public function setReport(Setting $report): void
    {
        $this->report = $report;
    }

}
