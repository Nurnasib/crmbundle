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

use App\Entity\User;
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
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="expense")
     */
    private $employee;

    /**
     * @var ExpenseBatch
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\ExpenseBatch", inversedBy="expenses")
     * @ORM\JoinColumn(name="expense_batch_id", referencedColumnName="id", nullable=true)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */

    private $expenseBatch;


    /**
     * @var ExpenseParticular
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\ExpenseParticular", mappedBy="expense",  cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $expenseParticulars;

    /**
     * @var ExpenseConveyanceDetails
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\ExpenseConveyanceDetails", mappedBy="expense",  cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $expenseConveyanceDetails;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting" , inversedBy="expense")
     */
    private $workingArea;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $appId;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="expense_date", type="date", nullable=true)
     */
    private $expenseDate;
    
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column( type="text", name="comments", nullable=true)
     */
    private $comments;

    /**
     * @var string
     * @ORM\Column( type="text", name="details_comments", nullable=true)
     */
    private $detailsComments;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $isAreaChange = 0;

    public function __construct()
    {
        $this->expenseParticulars = new ArrayCollection();
        $this->expenseConveyanceDetails = new ArrayCollection();
    }

    /**
     * @return ExpenseBatch
     */
    public function getExpenseBatch()
    {
        return $this->expenseBatch;
    }

    /**
     * @param ExpenseBatch $expenseBatch
     */
    public function setExpenseBatch(ExpenseBatch $expenseBatch): void
    {
        $this->expenseBatch = $expenseBatch;
    }

    /**
     * @var string
     * @ORM\Column(name="visit_location", type="text", nullable=true)
     */
    private $visitLocation;

    /**
     * @var float
     *
     * @ORM\Column(name="riding", type="float", nullable=true)
     */

    private $riding;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $status = 0;

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
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
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
     * @return \DateTime
     */
    public function getExpenseDate()
    {
        return $this->expenseDate;
    }

    /**
     * @param \DateTime $expenseDate
     */
    public function setExpenseDate(\DateTime $expenseDate): void
    {
        $this->expenseDate = $expenseDate;
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
     * @return float
     */
    public function getRiding()
    {
        return $this->riding;
    }

    /**
     * @param float $riding
     */
    public function setRiding(float $riding): void
    {
        $this->riding = $riding;
    }

    /**
     * @return string
     */
    public function getVisitLocation()
    {
        return $this->visitLocation;
    }

    /**
     * @param string $visitLocation
     */
    public function setVisitLocation(string $visitLocation): void
    {
        $this->visitLocation = $visitLocation;
    }

    /**
     * @return Setting
     */
    public function getWorkingArea()
    {
        return $this->workingArea;
    }

    /**
     * @param Setting $workingArea
     */
    public function setWorkingArea( $workingArea): void
    {
        $this->workingArea = $workingArea;
    }

    /**
     * @return ExpenseParticular
     */
    public function getExpenseParticulars()
    {
        return $this->expenseParticulars;
    }

    /**
     * @param ExpenseParticular $expenseParticulars
     */
    public function setExpenseParticulars($expenseParticulars): void
    {
        $this->expenseParticulars = $expenseParticulars;
    }

    /**
     * @return ExpenseConveyanceDetails
     */
    public function getExpenseConveyanceDetails()
    {
        return $this->expenseConveyanceDetails;
    }

    /**
     * @param ExpenseConveyanceDetails $expenseConveyanceDetails
     */
    public function setExpenseConveyanceDetails(ExpenseConveyanceDetails $expenseConveyanceDetails): void
    {
        $this->expenseConveyanceDetails = $expenseConveyanceDetails;
    }

    /**
     * @return string
     */
    public function getComments(): ?string
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     */
    public function setComments(?string $comments): void
    {
        $this->comments = $comments;
    }

    /**
     * @return string
     */
    public function getDetailsComments(): ?string
    {
        return $this->detailsComments;
    }

    /**
     * @param string $detailsComments
     */
    public function setDetailsComments(?string $detailsComments): void
    {
        $this->detailsComments = $detailsComments;
    }

    /**
     * @return int
     */
    public function getIsAreaChange(): int
    {
        return $this->isAreaChange;
    }

    /**
     * @param int $isAreaChange
     */
    public function setIsAreaChange(int $isAreaChange): void
    {
        $this->isAreaChange = $isAreaChange;
    }


}
