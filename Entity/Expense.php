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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ExpenseRepository")
 * @ORM\Table(name="crm_expense")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class Expense
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="schedule_visit", nullable=true )
     */
    private $scheduleVisit;

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Location" , inversedBy="expense")
     */
    private $visitingArea;

    /**
     * @var CrmVisit
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmVisit" , inversedBy="expense")
     */
    private $crmVisit;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $appId;

    /**
     * @var Setting
     * @ORM\ManyToMany(targetEntity="Setting")
     * @ORM\JoinTable(name="crm_expence_purpose")
     */
    private $purpose;
    public function __construct()
    {
        $this->purpose = new ArrayCollection();
        $this->vehicle = new ArrayCollection();
    }

    /**
     * @var Setting
     * @ORM\ManyToMany(targetEntity="Setting")
     * @ORM\JoinTable(name="crm_expence_vehicle")
     */
    private $vehicle;

    /**
     * @var string
     * @ORM\Column(name="conveyance", nullable=true)
     */
    private $conveyance;

    /**
     * @var string
     * @ORM\Column(name="daily_allowance", nullable=true)
     */
    private $dailyAllowance;

    /**
     * @var string
     * @ORM\Column(name="hotel_rent", nullable=true)
     */
    private $hotelRent;

    /**
     * @var string
     * @ORM\Column(name="photostate", nullable=true)
     */
    private $photostate;


    /**
     * @var string
     * @ORM\Column(name="courier", nullable=true)
     */
    private $courier;


    /**
     * @var string
     * @ORM\Column(name="food", nullable=true)
     */
    private $food;


    /**
     * @var string
     * @ORM\Column(name="mobile", nullable=true)
     */
    private $mobile;



    /**
     * @var string
     * @ORM\Column(name="maintenace", nullable=true)
     */
    private $maintenace;


    /**
     * @var string
     * @ORM\Column(name="toll_bill", nullable=true)
     */
    private $tollBill;

    /**
     * @var string
     * @ORM\Column(name="service_charge", nullable=true)
     */
    private $serviceCharge;

    /**
     * @var string
     * @ORM\Column(name="others", nullable=true)
     */
    private $others;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status = false;

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
     * @return string
     */
    public function getScheduleVisit()
    {
        return $this->scheduleVisit;
    }

    /**
     * @param string $scheduleVisit
     */
    public function setScheduleVisit($scheduleVisit)
    {
        $this->scheduleVisit = $scheduleVisit;
    }

    /**
     * @return Setting
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @param Setting $purpose
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
    }

    /**
     * @return Setting
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param Setting $vehicle
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * @return string
     */
    public function getConveyance()
    {
        return $this->conveyance;
    }

    /**
     * @param string $conveyance
     */
    public function setConveyance($conveyance)
    {
        $this->conveyance = $conveyance;
    }

    /**
     * @return string
     */
    public function getDailyAllowance()
    {
        return $this->dailyAllowance;
    }

    /**
     * @param string $dailyAllowance
     */
    public function setDailyAllowance($dailyAllowance)
    {
        $this->dailyAllowance = $dailyAllowance;
    }

    /**
     * @return string
     */
    public function getHotelRent()
    {
        return $this->hotelRent;
    }

    /**
     * @param string $hotelRent
     */
    public function setHotelRent($hotelRent)
    {
        $this->hotelRent = $hotelRent;
    }

    /**
     * @return string
     */
    public function getPhotostate()
    {
        return $this->photostate;
    }

    /**
     * @param string $photostate
     */
    public function setPhotostate($photostate)
    {
        $this->photostate = $photostate;
    }

    /**
     * @return string
     */
    public function getCourier()
    {
        return $this->courier;
    }

    /**
     * @param string $courier
     */
    public function setCourier($courier)
    {
        $this->courier = $courier;
    }

    /**
     * @return string
     */
    public function getFood()
    {
        return $this->food;
    }

    /**
     * @param string $food
     */
    public function setFood($food)
    {
        $this->food = $food;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getMaintenace()
    {
        return $this->maintenace;
    }

    /**
     * @param string $maintenace
     */
    public function setMaintenace($maintenace)
    {
        $this->maintenace = $maintenace;
    }

    /**
     * @return string
     */
    public function getTollBill()
    {
        return $this->tollBill;
    }

    /**
     * @param string $tollBill
     */
    public function setTollBill($tollBill)
    {
        $this->tollBill = $tollBill;
    }

    /**
     * @return string
     */
    public function getServiceCharge()
    {
        return $this->serviceCharge;
    }

    /**
     * @param string $serviceCharge
     */
    public function setServiceCharge($serviceCharge)
    {
        $this->serviceCharge = $serviceCharge;
    }

    /**
     * @return string
     */
    public function getOthers()
    {
        return $this->others;
    }

    /**
     * @param string $others
     */
    public function setOthers($others)
    {
        $this->others = $others;
    }

    /**
     * @return string
     */
    public function getVisitingArea()
    {
        return $this->visitingArea;
    }

    /**
     * @param string $visitingArea
     */
    public function setVisitingArea($visitingArea)
    {
        $this->visitingArea = $visitingArea;
    }

    /**
     * @return CrmVisit
     */
    public function getCrmVisit()
    {
        return $this->crmVisit;
    }

    /**
     * @param CrmVisit $crmVisit
     */
    public function setCrmVisit($crmVisit)
    {
        $this->crmVisit = $crmVisit;
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     */
    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
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

}
