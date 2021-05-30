<?php

namespace Terminalbd\CrmBundle\Entity;

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
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\ApiDetails", mappedBy="batch")
     */
    private $apiDetails;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $deviceId;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $employeeId;

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
     * @return int
     */
    public function getDeviceId(): int
    {
        return $this->deviceId;
    }

    /**
     * @param int $deviceId
     */
    public function setDeviceId(int $deviceId): void
    {
        $this->deviceId = $deviceId;
    }

    /**
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    /**
     * @param int $employeeId
     */
    public function setEmployeeId(int $employeeId): void
    {
        $this->employeeId = $employeeId;
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

    public function addApiDetails(ApiDetails $apiDetails ): self
    {
        if (!$this->apiDetails->contains($apiDetails)) {
            $this->apiDetails[] = $apiDetails;
            $apiDetails->setApi($this);
        }

        return $this;
    }

    public function removeApiDetails(ApiDetails $breedDetail): self
    {
        if ($this->breedDetails->removeElement($breedDetail)) {
            // set the owning side to null (unless already changed)
            if ($breedDetail->getBreed() === $this) {
                $breedDetail->setBreed(null);
            }
        }

        return $this;
    }

}
