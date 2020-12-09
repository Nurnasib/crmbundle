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
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ExpenseConveyanceDetailsRepository")
 * @ORM\Table(name="crm_expense_conveyance_details")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class ExpenseConveyanceDetails
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="visiting_date", type="date", nullable=true)
     */
    private $visitingDate;

    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Location" , inversedBy="expense")
     */
    private $visitingArea;

    /**
     * @var CrmVisit
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmVisit" , inversedBy="expenseConveyanceDetails")
     */
    private $crmVisit;

    /**
     * @var Expense
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Expense" , inversedBy="expenseConveyanceDetails")
     */
    private $expense;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting" , inversedBy="expenseConveyanceDetails")
     */
    private $purpose;

    /**
     * @var string
     * @ORM\Column(name="destination", nullable=true)
     */
    private $destination;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting" , inversedBy="expenseConveyanceDetails")
     */
    private $transportType;

    /**
     * @var float
     *
     * @ORM\Column(name="meter_reading_from", type="float")
     */
    private $meterReadingFrom=0;

    /**
     * @var float
     *
     * @ORM\Column(name="meter_reading_to", type="float")
     */
    private $meterReadingTo=0;

    /**
     * @var float
     *
     * @ORM\Column(name="mileage", type="float")
     */
    private $mileage=0;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount=0;

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
     * @return \DateTime
     */
    public function getVisitingDate()
    {
        return $this->visitingDate;
    }

    /**
     * @param \DateTime $visitingDate
     */
    public function setVisitingDate(\DateTime $visitingDate): void
    {
        $this->visitingDate = $visitingDate;
    }

    /**
     * @return Location
     */
    public function getVisitingArea()
    {
        return $this->visitingArea;
    }

    /**
     * @param Location $visitingArea
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
    public function setCrmVisit(CrmVisit $crmVisit): void
    {
        $this->crmVisit = $crmVisit;
    }

    /**
     * @return Expense
     */
    public function getExpense()
    {
        return $this->expense;
    }

    /**
     * @param Expense $expense
     */
    public function setExpense(Expense $expense): void
    {
        $this->expense = $expense;
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
    public function setPurpose(Setting $purpose): void
    {
        $this->purpose = $purpose;
    }

    /**
     * @return string
     */
    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     */
    public function setDestination(string $destination): void
    {
        $this->destination = $destination;
    }

    /**
     * @return Setting
     */
    public function getTransportType()
    {
        return $this->transportType;
    }

    /**
     * @param Setting $transportType
     */
    public function setTransportType(Setting $transportType): void
    {
        $this->transportType = $transportType;
    }

    /**
     * @return float
     */
    public function getMeterReadingFrom()
    {
        return $this->meterReadingFrom;
    }

    /**
     * @param float $meterReadingFrom
     */
    public function setMeterReadingFrom(float $meterReadingFrom): void
    {
        $this->meterReadingFrom = $meterReadingFrom;
    }

    /**
     * @return float
     */
    public function getMeterReadingTo()
    {
        return $this->meterReadingTo;
    }

    /**
     * @param float $meterReadingTo
     */
    public function setMeterReadingTo(float $meterReadingTo): void
    {
        $this->meterReadingTo = $meterReadingTo;
    }

    /**
     * @return float
     */
    public function getMileage()
    {
        return $this->mileage;
    }

    /**
     * @param float $mileage
     */
    public function setMileage(float $mileage): void
    {
        $this->mileage = $mileage;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
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


}
