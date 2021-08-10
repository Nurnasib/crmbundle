<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(name="crm_fish_sales_price")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FishSalesPriceRepository")
 */
class FishSalesPrice
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="fishSalesPrice")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishSalesPrice")
     * @ORM\JoinColumn(name="species_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $speciesType;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishSalesPrice")
     * @ORM\JoinColumn(name="fish_size_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $fishSize;

    /**
     * @var string
     * @Orm\Column(type="string", length=20, nullable=true)
     */
    private $monthName;

    /**
     * @var integer
     * @Orm\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * @var float
     * @Orm\Column(type="float")
     */
    private $price=0;

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
     * @return Setting
     */
    public function getSpeciesType(): Setting
    {
        return $this->speciesType;
    }

    /**
     * @param Setting $speciesType
     */
    public function setSpeciesType(Setting $speciesType): void
    {
        $this->speciesType = $speciesType;
    }

    /**
     * @return Setting
     */
    public function getFishSize(): Setting
    {
        return $this->fishSize;
    }

    /**
     * @param Setting $fishSize
     */
    public function setFishSize(Setting $fishSize): void
    {
        $this->fishSize = $fishSize;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getMonthName()
    {
        return $this->monthName;
    }

    /**
     * @param string $monthName
     */
    public function setMonthName(string $monthName): void
    {
        $this->monthName = $monthName;
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
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

}
