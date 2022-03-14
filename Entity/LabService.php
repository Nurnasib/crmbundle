<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 *
 * @ORM\Table(name="crm_lab_services",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"employee_id", "lab_id", "service_id", "breed_name", "reporting_year"})}
 *     )
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\LabServiceRepository")
 */
class LabService
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="labService")
     */
    private $employee;

    /**
     * @var string
     * @Orm\Column(type="string", nullable=true)
     */
    private $breedName;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="labService")
     * @ORM\JoinColumn(name="lab_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $lab;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="labService")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $service;

    /**
     * @var string
     * @Orm\Column(type="string", nullable=true)
     */
    private $reportingYear;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $january=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $february=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $march=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $april=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $may=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $june=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $july=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $august=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $september=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $october=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $november=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */

    private $december=0;

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
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getLab()
    {
        return $this->lab;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $lab
     */
    public function setLab(\Terminalbd\CrmBundle\Entity\Setting $lab): void
    {
        $this->lab = $lab;
    }

    /**
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $service
     */
    public function setService(\Terminalbd\CrmBundle\Entity\Setting $service): void
    {
        $this->service = $service;
    }

    /**
     * @return float
     */
    public function getJanuary()
    {
        return $this->january;
    }

    /**
     * @param float $january
     */
    public function setJanuary(float $january): void
    {
        $this->january = $january;
    }

    /**
     * @return float
     */
    public function getFebruary()
    {
        return $this->february;
    }

    /**
     * @param float $february
     */
    public function setFebruary(float $february): void
    {
        $this->february = $february;
    }

    /**
     * @return float
     */
    public function getMarch()
    {
        return $this->march;
    }

    /**
     * @param float $march
     */
    public function setMarch(float $march): void
    {
        $this->march = $march;
    }

    /**
     * @return float
     */
    public function getApril()
    {
        return $this->april;
    }

    /**
     * @param float $april
     */
    public function setApril(float $april): void
    {
        $this->april = $april;
    }

    /**
     * @return float
     */
    public function getMay()
    {
        return $this->may;
    }

    /**
     * @param float $may
     */
    public function setMay(float $may): void
    {
        $this->may = $may;
    }

    /**
     * @return float
     */
    public function getJune()
    {
        return $this->june;
    }

    /**
     * @param float $june
     */
    public function setJune(float $june): void
    {
        $this->june = $june;
    }

    /**
     * @return float
     */
    public function getJuly()
    {
        return $this->july;
    }

    /**
     * @param float $july
     */
    public function setJuly(float $july): void
    {
        $this->july = $july;
    }

    /**
     * @return float
     */
    public function getAugust()
    {
        return $this->august;
    }

    /**
     * @param float $august
     */
    public function setAugust(float $august): void
    {
        $this->august = $august;
    }

    /**
     * @return float
     */
    public function getSeptember()
    {
        return $this->september;
    }

    /**
     * @param float $september
     */
    public function setSeptember(float $september): void
    {
        $this->september = $september;
    }

    /**
     * @return float
     */
    public function getOctober()
    {
        return $this->october;
    }

    /**
     * @param float $october
     */
    public function setOctober(float $october): void
    {
        $this->october = $october;
    }

    /**
     * @return float
     */
    public function getNovember()
    {
        return $this->november;
    }

    /**
     * @param float $november
     */
    public function setNovember(float $november): void
    {
        $this->november = $november;
    }

    /**
     * @return float
     */
    public function getDecember()
    {
        return $this->december;
    }

    /**
     * @param float $december
     */
    public function setDecember(float $december): void
    {
        $this->december = $december;
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
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getBreedName()
    {
        return $this->breedName;
    }

    /**
     * @param string $breedName
     */
    public function setBreedName(string $breedName): void
    {
        $this->breedName = $breedName;
    }

    /**
     * @return string
     */
    public function getReportingYear()
    {
        return $this->reportingYear;
    }

    /**
     * @param string $reportingYear
     */
    public function setReportingYear(string $reportingYear): void
    {
        $this->reportingYear = $reportingYear;
    }

}
