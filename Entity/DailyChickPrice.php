<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(
 *     name="crm_daily_chick_price",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"employee_id", "reporting_date"})}
 *     )
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\DailyChickPriceRepository")
 */
class DailyChickPrice
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var DailyChickPriceDetails
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\DailyChickPriceDetails", mappedBy="crmDailyChickPrice")
     */
    private $crmDailyChickPriceDetails;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="crmDailyChickPrice")
     */
    private $employee;

    /**
     * @var \DateTime
     * @ORM\Column(name="reporting_date", type="date", nullable=true)
     */
    private $reportingDate;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Location", inversedBy="crmDailyChickPrice")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $location;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->crmDailyChickPriceDetails = new ArrayCollection();
    }

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
     * @return DailyChickPriceDetails
     */
    public function getCrmDailyChickPriceDetails()
    {
        return $this->crmDailyChickPriceDetails;
    }

    /**
     * @param DailyChickPriceDetails $crmDailyChickPriceDetails
     */
    public function setCrmDailyChickPriceDetails(DailyChickPriceDetails $crmDailyChickPriceDetails): void
    {
        $this->crmDailyChickPriceDetails = $crmDailyChickPriceDetails;
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
    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
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
    public function getReportingDate()
    {
        return $this->reportingDate;
    }

    /**
     * @param \DateTime $reportingDate
     */
    public function setReportingDate(\DateTime $reportingDate): void
    {
        $this->reportingDate = $reportingDate;
    }


}
