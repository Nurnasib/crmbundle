<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use NumberFormatter;


/**
 * @ORM\Table(name="crm_daily_chick_price_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\DailyChickPriceDetailsRepository")
 */
class DailyChickPriceDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var DailyChickPrice
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\DailyChickPrice", inversedBy="crmDailyChickPriceDetails")
     * @ORM\JoinColumn(name="crm_daily_chick_price_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $crmDailyChickPrice;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmDailyChickPriceDetails")
     * @ORM\JoinColumn(name="chick_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $chickType;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmChickLifeCycleDetails")
     * @ORM\JoinColumn(name="feed_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feed;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */

    private $price=0;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
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
     * @return DailyChickPrice
     */
    public function getCrmDailyChickPrice()
    {
        return $this->crmDailyChickPrice;
    }

    /**
     * @param DailyChickPrice $crmDailyChickPrice
     */
    public function setCrmDailyChickPrice(DailyChickPrice $crmDailyChickPrice): void
    {
        $this->crmDailyChickPrice = $crmDailyChickPrice;
    }

    /**
     * @return Setting
     */
    public function getChickType()
    {
        return $this->chickType;
    }

    /**
     * @param Setting $chickType
     */
    public function setChickType(Setting $chickType): void
    {
        $this->chickType = $chickType;
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
     * @return float
     */
    public function getPrice()
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
    public function getUpdatedAt()
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
