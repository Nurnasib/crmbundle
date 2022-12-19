<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(name="crm_company_wise_feed_sale")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CompanyWiseFeedSaleRepository")
 */
class CompanyWiseFeedSale
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="companyWiseFeedSale")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="companyWiseFeedSale")
     * @ORM\JoinColumn(name="feed_company_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedCompany;

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
     * @var string
     * @Orm\Column(type="string", nullable=true)
     */
    private $breedName;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
     */
    private $productWiseQty;

    /**
     * @var float
     * @Orm\Column(type="float")
     */
    private $totalQty=0;

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
    public function setEmployee($employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return Setting
     */
    public function getFeedCompany()
    {
        return $this->feedCompany;
    }

    /**
     * @param Setting $feedCompany
     */
    public function setFeedCompany(Setting $feedCompany): void
    {
        $this->feedCompany = $feedCompany;
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
     * @return string
     */
    public function getProductWiseQty()
    {
        return $this->productWiseQty;
    }

    /**
     * @param string $productWiseQty
     */
    public function setProductWiseQty($productWiseQty): void
    {
        $this->productWiseQty = $productWiseQty;
    }

    /**
     * @return float
     */
    public function getTotalQty()
    {
        return $this->totalQty;
    }

    /**
     * @param float $totalQty
     */
    public function setTotalQty(float $totalQty): void
    {
        $this->totalQty = $totalQty;
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
