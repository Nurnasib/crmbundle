<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Append-only trail of manual expense adjustments (edit / delete) made on the
 * Expense Adjustment page. Rows are never updated or deleted.
 *
 * expenseId and conveyanceDetailId are plain integers, not foreign keys, so the
 * log survives the deletion of the expense it describes.
 *
 * @ORM\Entity
 * @ORM\Table(name="crm_expense_adjustment_log", indexes={
 *     @ORM\Index(name="idx_expense_adjustment_employee_date", columns={"employee_id", "expense_date"})
 * })
 */
class ExpenseAdjustmentLog
{
    const ACTION_EDIT = 'edit';
    const ACTION_DELETE = 'delete';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(name="expense_id", type="integer")
     */
    private $expenseId;

    /**
     * @ORM\Column(name="conveyance_detail_id", type="integer", nullable=true)
     */
    private $conveyanceDetailId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="employee_id", nullable=true)
     */
    private $employee;

    /**
     * @ORM\Column(name="expense_date", type="date", nullable=true)
     */
    private $expenseDate;

    /**
     * self::ACTION_EDIT or self::ACTION_DELETE
     *
     * @ORM\Column(name="action", type="string", length=20)
     */
    private $action;

    /**
     * Edit: only the changed fields, before. Delete: full raw snapshot of the expense and its child rows.
     *
     * @ORM\Column(name="old_data", type="json", nullable=true)
     */
    private $oldData;

    /**
     * Edit: only the changed fields, after. Delete: null.
     *
     * @ORM\Column(name="new_data", type="json", nullable=true)
     */
    private $newData;

    /**
     * @ORM\Column(name="reason", type="text")
     */
    private $reason;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="changed_by_id", nullable=true)
     */
    private $changedBy;

    /**
     * @ORM\Column(name="changed_at", type="datetime")
     */
    private $changedAt;

    public function __construct()
    {
        $this->changedAt = new \DateTime();
    }

    public function getId() { return $this->id; }

    public function getExpenseId() { return $this->expenseId; }
    public function setExpenseId($expenseId): void { $this->expenseId = $expenseId; }

    public function getConveyanceDetailId() { return $this->conveyanceDetailId; }
    public function setConveyanceDetailId($conveyanceDetailId): void { $this->conveyanceDetailId = $conveyanceDetailId; }

    public function getEmployee(): ?User { return $this->employee; }
    public function setEmployee(?User $employee): void { $this->employee = $employee; }

    public function getExpenseDate() { return $this->expenseDate; }
    public function setExpenseDate($expenseDate): void { $this->expenseDate = $expenseDate; }

    public function getAction() { return $this->action; }
    public function setAction($action): void { $this->action = $action; }

    public function getOldData() { return $this->oldData; }
    public function setOldData($oldData): void { $this->oldData = $oldData; }

    public function getNewData() { return $this->newData; }
    public function setNewData($newData): void { $this->newData = $newData; }

    public function getReason() { return $this->reason; }
    public function setReason($reason): void { $this->reason = $reason; }

    public function getChangedBy(): ?User { return $this->changedBy; }
    public function setChangedBy(?User $changedBy): void { $this->changedBy = $changedBy; }

    public function getChangedAt() { return $this->changedAt; }
}
