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

use App\Entity\Admin\Location;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\PoultryMeatEggPriceRepository")
 * @ORM\Table(name="crm_poultry_meat_egg_price",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={"employee_id", "reporting_date", "region_id", "breed_type_id"})}
 *     )
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class PoultryMeatEggPrice
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var $employee
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="meatEggPrice")
     */
    private $employee;


    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Location" , inversedBy="poultryMeatEggPrice")
     */
    private $region;

    /**
     * @var $breedType
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting")
     */
    private $breedType;

    /**
     * @var $price
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status = true;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     */
    private $reportingDate;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
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
     * @return Location
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param Location $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return mixed
     */
    public function getBreedType()
    {
        return $this->breedType;
    }

    /**
     * @param mixed $breedType
     */
    public function setBreedType($breedType): void
    {
        $this->breedType = $breedType;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return \DateTime
     */
    public function getReportingDate(): \DateTime
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
