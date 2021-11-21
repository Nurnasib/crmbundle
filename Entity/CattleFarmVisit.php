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
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CattleFarmVisitRepository")
 * @ORM\Table(name="crm_cattle_farm_visit")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class CattleFarmVisit
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var CattleFarmVisitDetails
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\CattleFarmVisitDetails", mappedBy="crmCattleFarmVisit")
     */
    private $crmCattleFarmVisitDetails;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="crmCattleFarmVisit")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmCattleFarmVisit")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var \DateTime
     * @ORM\Column(name="repoting_month", type="date", nullable=true)
     */

    private $reportingMonth;

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
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    private $appId;

    /**
     * @ORM\OneToOne(targetEntity="Terminalbd\CrmBundle\Entity\Api", inversedBy="cattleFarmVisit")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $batch;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return CattleFarmVisitDetails
     */
    public function getCrmCattleFarmVisitDetails(): CattleFarmVisitDetails
    {
        return $this->crmCattleFarmVisitDetails;
    }

    /**
     * @param CattleFarmVisitDetails $crmCattleFarmVisitDetails
     */
    public function setCrmCattleFarmVisitDetails(CattleFarmVisitDetails $crmCattleFarmVisitDetails): void
    {
        $this->crmCattleFarmVisitDetails = $crmCattleFarmVisitDetails;
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
    public function setEmployee(User $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return Setting
     */
    public function getReport(): Setting
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
    public function getReportingMonth(): \DateTime
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
     * @return \DateTime
     */
    public function getCreated(): \DateTime
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
    public function getUpdated(): \DateTime
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
     * @return int
     */
    public function getAppId(): int
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
     * @return mixed
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param mixed $batch
     */
    public function setBatch($batch): void
    {
        $this->batch = $batch;
    }

}
