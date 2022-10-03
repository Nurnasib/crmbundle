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
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ExpenseBatchRepository")
 * @ORM\Table(name="crm_expense_batch")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class ExpenseBatch
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="expense")
     */
    private $employee;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="expense")
     */
    private $approvedBy;

    /**
     * @var Expense
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\Expense", mappedBy="expenseBatch")
     */
    private $expenses;

    /**
     * @var ExpenseParticular
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\ExpenseParticular", mappedBy="expenseBatch",  cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $expenseParticulars;

    /**
     * @var \DateTime
     * @ORM\Column(name="expense_month", type="date", nullable=true)
     */
    private $expenseMonth;
    
    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;
    /**
     * @var \DateTime
     * @ORM\Column(name="approved_at", type="datetime", nullable=true)
     */
    private $approvedAt;

    /**
     * @var float
     *
     * @ORM\Column(name="total_riding", type="float", nullable=true)
     */

    private $totalRiding;

    /**
     * @var float
     *
     * @ORM\Column(name="per_miles_amount", type="float", nullable=true)
     */

    private $perMilesAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="total_miles_amount", type="float", nullable=true)
     */

    private $totalMilesAmount;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $status = 1;

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
     * @return User
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * @param User $approvedBy
     */
    public function setApprovedBy(User $approvedBy): void
    {
        $this->approvedBy = $approvedBy;
    }

    /**
     * @return Expense
     */
    public function getExpenses()
    {
        return $this->expenses;
    }

    /**
     * @param Expense $expenses
     */
    public function setExpenses($expenses): void
    {
        $this->expenses = $expenses;
    }

    /**
     * @return \DateTime
     */
    public function getExpenseMonth()
    {
        return $this->expenseMonth;
    }

    /**
     * @param \DateTime $expenseMonth
     */
    public function setExpenseMonth(\DateTime $expenseMonth): void
    {
        $this->expenseMonth = $expenseMonth;
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
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    /**
     * @param \DateTime $approvedAt
     */
    public function setApprovedAt(\DateTime $approvedAt): void
    {
        $this->approvedAt = $approvedAt;
    }

    /**
     * @return float
     */
    public function getTotalRiding()
    {
        return $this->totalRiding;
    }

    /**
     * @param float $totalRiding
     */
    public function setTotalRiding(float $totalRiding): void
    {
        $this->totalRiding = $totalRiding;
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
     * @return float
     */
    public function getPerMilesAmount()
    {
        return $this->perMilesAmount;
    }

    /**
     * @param float $perMilesAmount
     */
    public function setPerMilesAmount($perMilesAmount): void
    {
        $this->perMilesAmount = $perMilesAmount;
    }

    /**
     * @return float
     */
    public function getTotalMilesAmount()
    {
        return $this->totalMilesAmount;
    }

    /**
     * @param float $totalMilesAmount
     */
    public function setTotalMilesAmount($totalMilesAmount): void
    {
        $this->totalMilesAmount = $totalMilesAmount;
    }

}
