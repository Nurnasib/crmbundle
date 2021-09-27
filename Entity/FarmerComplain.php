<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(name="crm_visit_complain")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FarmerComplainRepository")
 */
class FarmerComplain
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="visitComplain")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $employee;

    /**
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $farmer;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $agent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\FarmerComplainDetails", mappedBy="complain")
     */
    private $complaindetails;

    /**
     * @var DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->complaindetails = new ArrayCollection();
    }

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
     * @return mixed
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param mixed $employee
     */
    public function setEmployee($employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return mixed
     */
    public function getFarmer()
    {
        return $this->farmer;
    }

    /**
     * @param mixed $farmer
     */
    public function setFarmer($farmer): void
    {
        $this->farmer = $farmer;
    }

    /**
     * @return Agent
     */
    public function getAgent(): Agent
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
     * @return ArrayCollection
     */
    public function getComplaindetails(): ArrayCollection
    {
        return $this->complaindetails;
    }

    /**
     * @param ArrayCollection $complaindetails
     */
    public function setComplaindetails(ArrayCollection $complaindetails): void
    {
        $this->complaindetails = $complaindetails;
    }


    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     */
    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


}
