<?php

namespace Terminalbd\CrmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ExpenseChartDetailRepository")
 * @ORM\Table(name="crm_expense_chart_detail")
 */
class ExpenseChartDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\ExpenseChart", inversedBy="expenseChartDetails")
     * @ORM\JoinColumn(name="expense_chart_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $expenseChart;

    /**
     * @ORM\ManyToOne(targetEntity=\Terminalbd\CrmBundle\Entity\Setting::class)
     */
    private $particular;

    /**
     * @ORM\ManyToOne(targetEntity=\Terminalbd\CrmBundle\Entity\Setting::class)
     */
    private $area;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=20, options={"default" : "TAKA"})
     */
    private $amount_type='TAKA'; // TAKA, PERCENTAGE, PER_UNIT

    /**
     * @ORM\Column(type="string", length=20, options={"default" : "DAILY"}, nullable=true)
     */
    private $paymentDuration="DAILY"; // DAILY, MONTHLY, YEARLY

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExpenseChart(): ?ExpenseChart
    {
        return $this->expenseChart;
    }

    public function setExpenseChart(?ExpenseChart $expenseChart): self
    {
        $this->expenseChart = $expenseChart;

        return $this;
    }

    public function getParticular(): ?\Terminalbd\CrmBundle\Entity\Setting
    {
        return $this->particular;
    }

    public function setParticular(?\Terminalbd\CrmBundle\Entity\Setting $particular): self
    {
        $this->particular = $particular;

        return $this;
    }

    public function getArea(): ?\Terminalbd\CrmBundle\Entity\Setting
    {
        return $this->area;
    }

    public function setArea(?\Terminalbd\CrmBundle\Entity\Setting $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmountType(): ?string
    {
        return $this->amount_type;
    }

    public function setAmountType(?string $amount_type): self
    {
        $this->amount_type = $amount_type;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentDuration(): string
    {
        return $this->paymentDuration;
    }

    /**
     * @param string $paymentDuration
     */
    public function setPaymentDuration(string $paymentDuration): void
    {
        $this->paymentDuration = $paymentDuration;
    }



}
