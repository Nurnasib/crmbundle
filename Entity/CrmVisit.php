<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * CrmCustomer
 *
 * @ORM\Table(name="crm_visit")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CrmVisitRepository")
 */
class CrmVisit
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
     * @ORM\Column(name="cso_id", type="string",nullable=true)
     */

    private $cso_id;


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="crmVisits")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $employee;


    /**
     * @var Api
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Api", inversedBy="crmVisits")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $appBatch;


     /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Location", inversedBy="crmVisits")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $location;


    /**
     * @var string
     * @ORM\Column(name="working_duration", type="string",nullable=true)
     */
    private $workingDuration;

    /**
     * @var string
     * @ORM\Column(name="working_duration_to", type="string", nullable=true)
     */
    private $workingDurationTo;


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
     * @var CrmVisitDetails
     * @ORM\OneToMany(targetEntity="CrmVisitDetails", mappedBy="crmVisit",  cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $crmVisitDetails;

    /**
     * @var $appId
     * @ORM\Column(type="integer", nullable=true)
     */
    private $appId;


    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmVisits")
     * @ORM\JoinColumn(name="working_mode_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $workingMode;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmVisits")
     * @ORM\JoinColumn(name="area_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $area;

    /**
     * @var string
     * @ORM\Column(name="remarks", type="text", nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     * @ORM\Column(name="visit_date", type="date", nullable=true)
     */
    private $visitDate;

    /**
     * @var string
     * @ORM\Column(name="visit_time", type="string", nullable=true)
     */
    private $visitTime;

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
    public function getCsoId()
    {
        return $this->cso_id;
    }

    /**
     * @param string $cso_id
     */
    public function setCsoId($cso_id)
    {
        $this->cso_id = $cso_id;
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
     * @return CrmVisitDetails
     */
    public function getCrmVisitDetails()
    {
        return $this->crmVisitDetails;
    }

    public function removeCrmVisitDetails($crmVisitDetails)
    {
        $this->crmVisitDetails->remove($crmVisitDetails);
        $crmVisitDetails->setCrmVisit(null);
    }

    /**
     * @return string
     */
    public function getWorkingDuration()
    {
        return $this->workingDuration;
    }

    /**
     * @param string $workingDuration
     */
    public function setWorkingDuration($workingDuration)
    {
        $this->workingDuration = $workingDuration;
    }

    /**
     * @return string
     */
    public function getWorkingDurationTo()
    {
        return $this->workingDurationTo;
    }

    /**
     * @param string $workingDurationTo
     */
    public function setWorkingDurationTo($workingDurationTo): void
    {
        $this->workingDurationTo = $workingDurationTo;
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
    public function setEmployee(User $employee)
    {
        $this->employee = $employee;
    }

    /**
     * @return Api
     */
    public function getAppBatch(): Api
    {
        return $this->appBatch;
    }

    /**
     * @param Api $appBatch
     */
    public function setAppBatch($appBatch): void
    {
        $this->appBatch = $appBatch;
    }


    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation(Location $location)
    {
        $this->location = $location;
    }

    /**
     * @return mixed
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     */
    public function setAppId($appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return Setting
     */
    public function getWorkingMode()
    {
        return $this->workingMode;
    }

    /**
     * @param Setting $workingMode
     */
    public function setWorkingMode($workingMode): void
    {
        $this->workingMode = $workingMode;
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
    public function setRemarks(?string $remarks): void
    {
        $this->remarks = $remarks;
    }

    /**
     * @return Setting
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param Setting $area
     */
    public function setArea($area): void
    {
        $this->area = $area;
    }

    /**
     * @return \DateTime
     */
    public function getVisitDate(): \DateTime
    {
        return $this->visitDate;
    }

    /**
     * @param \DateTime $visitDate
     */
    public function setVisitDate(\DateTime $visitDate): void
    {
        $this->visitDate = $visitDate;
    }

    /**
     * @return string
     */
    public function getVisitTime(): string
    {
        return $this->visitTime;
    }

    /**
     * @param string $visitTime
     */
    public function setVisitTime(string $visitTime): void
    {
        $this->visitTime = $visitTime;
    }


}
