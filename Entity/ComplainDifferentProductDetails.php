<?php

namespace Terminalbd\CrmBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_complain_different_product_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ComplainDifferentProductDetailsRepository")
 */
class ComplainDifferentProductDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var ComplainDifferentProduct
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\ComplainDifferentProduct", inversedBy="details")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $complain;

    /**
     * @var ComplainParameter
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\ComplainParameter", inversedBy="complains")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $ComplainParameter;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $day;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $quantity;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

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
     * @return ComplainDifferentProduct
     */
    public function getComplain(): ComplainDifferentProduct
    {
        return $this->complain;
    }

    /**
     * @param ComplainDifferentProduct $complain
     */
    public function setComplain(ComplainDifferentProduct $complain): void
    {
        $this->complain = $complain;
    }

    /**
     * @return ComplainParameter
     */
    public function getComplainParameter(): ComplainParameter
    {
        return $this->ComplainParameter;
    }

    /**
     * @param ComplainParameter $ComplainParameter
     */
    public function setComplainParameter(ComplainParameter $ComplainParameter): void
    {
        $this->ComplainParameter = $ComplainParameter;
    }


    /**
     * @return string
     */
    public function getDay(): string
    {
        return $this->day;
    }

    /**
     * @param string $day
     */
    public function setDay(string $day): void
    {
        $this->day = $day;
    }

    /**
     * @return string
     */
    public function getQuantity(): string
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     */
    public function setQuantity(string $quantity): void
    {
        $this->quantity = $quantity;
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
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }


}
