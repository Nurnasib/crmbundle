<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * CrmCustomer
 *
 * @ORM\Table(name="crm_api")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ApiRepository")
 */
class Api
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var $apiDetails
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\ApiDetails", mappedBy="batch", cascade={"persist", "remove"})
     */
    private $apiDetails;

    /**
     * @ORM\Column(type="string")
     */
    private $batchNo;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="appEntry")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="SET NULL")
     */
    private $employee;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $status = false;

    /**
     * @var $createdAt
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var $updatedAt
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;
    
    public function __construct()
    {
        $this->apiDetails = new ArrayCollection();
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
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return ArrayCollection
     */
    public function getApiDetails()
    {
        return $this->apiDetails;
    }

    public function addApiDetails(ApiDetails $apiDetail): self
    {
        if (!$this->apiDetails->contains($apiDetail)) {
            $this->apiDetails[] = $apiDetail;
            $apiDetail->setBatch($this);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBatchNo()
    {
        return $this->batchNo;
    }

    /**
     * @param mixed $batchNo
     */
    public function setBatchNo($batchNo): void
    {
        $this->batchNo = $batchNo;
    }




}
