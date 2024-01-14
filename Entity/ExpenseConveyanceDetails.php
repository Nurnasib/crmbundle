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
     * @var Expense
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Expense" , inversedBy="expenseConveyanceDetails")
     */
    private $expense;

    /**
     * @var string
     * @ORM\Column(name="transport_type", nullable=true)
     */
    private $transportType;

    /**
     * @var string
     * @ORM\Column( type="text", name="details", nullable=true)
     */
    private $details;


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
    private $totalMileage=0;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount=0;

    /**
     * @var float
     *
     * @ORM\Column(name="mobil_bill", type="float", nullable=true)
     */
    private $mobilBill=0;

    /**
     * @var float
     *
     * @ORM\Column(name="maintenance_bill", type="float", nullable=true)
     */
    private $maintenanceBill=0;

    /**
     * @var float
     *
     * @ORM\Column(name="servicing_bill", type="float", nullable=true)
     */
    private $servicingBill=0;

    /**
     * @var float
     *
     * @ORM\Column(name="toll_bill", type="float", nullable=true)
     */
    private $tollBill=0;

    /**
     * @var float
     *
     * @ORM\Column(name="fuel_bill", type="float", nullable=true)
     */
    private $fuelBill=0;

    /**
     * @var float
     *
     * @ORM\Column(name="parking_bill", type="float", nullable=true)
     */
    private $parkingBill=0;

    /**
     * @var float
     *
     * @ORM\Column(name="others_bill", type="float", nullable=true)
     */
    private $othersBill=0;

    /**
     * @ORM\Column( type="json", name="destination", nullable=true)
     */
    private $destination;


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
     * @return Expense
     */
    public function getExpense(): Expense
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
     * @return string
     */
    public function getTransportType(): string
    {
        return $this->transportType;
    }

    /**
     * @param string $transportType
     */
    public function setTransportType(string $transportType): void
    {
        $this->transportType = $transportType;
    }

    /**
     * @return string
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }

    /**
     * @param string $details
     */
    public function setDetails(?string $details): void
    {
        $this->details = $details;
    }

    /**
     * @return float|int
     */
    public function getMeterReadingFrom()
    {
        return $this->meterReadingFrom;
    }

    /**
     * @param float|int $meterReadingFrom
     */
    public function setMeterReadingFrom($meterReadingFrom): void
    {
        $this->meterReadingFrom = $meterReadingFrom;
    }

    /**
     * @return float|int
     */
    public function getMeterReadingTo()
    {
        return $this->meterReadingTo;
    }

    /**
     * @param float|int $meterReadingTo
     */
    public function setMeterReadingTo($meterReadingTo): void
    {
        $this->meterReadingTo = $meterReadingTo;
    }

    /**
     * @return float|int
     */
    public function getTotalMileage()
    {
        return $this->totalMileage;
    }

    /**
     * @param float|int $totalMileage
     */
    public function setTotalMileage($totalMileage): void
    {
        $this->totalMileage = $totalMileage;
    }

    /**
     * @return float|int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float|int $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return float|int
     */
    public function getMobilBill()
    {
        return $this->mobilBill;
    }

    /**
     * @param float|int $mobilBill
     */
    public function setMobilBill($mobilBill): void
    {
        $this->mobilBill = $mobilBill;
    }

    /**
     * @return float|int
     */
    public function getMaintenanceBill()
    {
        return $this->maintenanceBill;
    }

    /**
     * @param float|int $maintenanceBill
     */
    public function setMaintenanceBill($maintenanceBill): void
    {
        $this->maintenanceBill = $maintenanceBill;
    }

    /**
     * @return float|int
     */
    public function getServicingBill()
    {
        return $this->servicingBill;
    }

    /**
     * @param float|int $servicingBill
     */
    public function setServicingBill($servicingBill): void
    {
        $this->servicingBill = $servicingBill;
    }

    /**
     * @return float|int
     */
    public function getTollBill()
    {
        return $this->tollBill;
    }

    /**
     * @param float|int $tollBill
     */
    public function setTollBill($tollBill): void
    {
        $this->tollBill = $tollBill;
    }

    /**
     * @return float|int
     */
    public function getFuelBill()
    {
        return $this->fuelBill;
    }

    /**
     * @param float|int $fuelBill
     */
    public function setFuelBill($fuelBill): void
    {
        $this->fuelBill = $fuelBill;
    }

    /**
     * @return float|int
     */
    public function getParkingBill()
    {
        return $this->parkingBill;
    }

    /**
     * @param float|int $parkingBill
     */
    public function setParkingBill($parkingBill): void
    {
        $this->parkingBill = $parkingBill;
    }

    /**
     * @return float|int
     */
    public function getOthersBill()
    {
        return $this->othersBill;
    }

    /**
     * @param float|null $othersBill
     */
    public function setOthersBill($othersBill): void
    {
        $this->othersBill = $othersBill;
    }

    /**
     * @return string
     */
    public function getDestination(): ?array
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     */
    public function setDestination(?array $destination): void
    {
        $this->destination = $destination;
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


}
