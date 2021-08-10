<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(name="crm_fish_tilapia_fry_sales")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\TilapiaFrySalesRepository")
 */
class TilapiaFrySales
{
    const TILAPIA_FRY_SALES_NOURISH = 'NOURISH';
    const TILAPIA_FRY_SALES_OTHER = 'OTHER';
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="tilapiaFrySales")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="tilapiaFrySales")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="tilapiaFrySales")
     */
    private $agent;

    /**
     * @var string
     * @Orm\Column(type="string", length=20, nullable=true)
     */
    private $otherCompetitorAgentName;

    /**
     * @var string
     * @Orm\Column(type="string", length=20, nullable=true)
     */
    private $type;

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
    private $quantity=0;

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
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param Setting $feed
     */
    public function setFeed(Setting $feed): void
    {
        $this->feed = $feed;
    }

    /**
     * @return Agent
     */
    public function getAgent()
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
     * @return string
     */
    public function getOtherCompetitorAgentName()
    {
        return $this->otherCompetitorAgentName;
    }

    /**
     * @param string $otherCompetitorAgentName
     */
    public function setOtherCompetitorAgentName(string $otherCompetitorAgentName): void
    {
        $this->otherCompetitorAgentName = $otherCompetitorAgentName;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return float
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity(float $quantity): void
    {
        $this->quantity = $quantity;
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
