<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Core\Setting;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ExpenseChartRepository")
 * @ORM\Table(name="crm_expense_chart")
 *
 */
class ExpenseChart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="expenseChart", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="employee_id", nullable=true, referencedColumnName="id", onDelete="CASCADE")
     */
    private $employee;

    /**
     * @ORM\ManyToOne(targetEntity=Setting::class, inversedBy="expenseCharts")
     * @ORM\JoinColumn(name="designation_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $designation;

    /**
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\ExpenseChartDetail", mappedBy="expenseChart")
     */
    private $expenseChartDetails;

    /**
     * @var string
     *
     * @ORM\Column(type="string",nullable=true)
     */
    private $typeOfVehicle='motorcyle'; // car, motorcyle, bus

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $perKmRate=3.5;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->expenseChartDetails = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getEmployee():?User
    {
        return $this->employee;
    }

    /**
     * @param mixed $employee
     */
    public function setEmployee(? User $employee): void
    {
        $this->employee = $employee;
    }

    public function getDesignation(): ?Setting
    {
        return $this->designation;
    }

    public function setDesignation(?Setting $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    public function getExpenseChartDetails()
    {
        return $this->expenseChartDetails;
    }

    public function addExpenseChartDetail(ExpenseChartDetail $expenseChartDetail): self
    {
        if (!$this->expenseChartDetails->contains($expenseChartDetail)) {
            $this->expenseChartDetails[] = $expenseChartDetail;
            $expenseChartDetail->setExpenseChart($this);
        }

        return $this;
    }

    public function removeExpenseChartDetail(ExpenseChartDetail $expenseChartDetail): self
    {
        if ($this->expenseChartDetails->removeElement($expenseChartDetail)) {
            // set the owning side to null (unless already changed)
            if ($expenseChartDetail->getExpenseChart() === $this) {
                $expenseChartDetail->setExpenseChart(null);
            }
        }

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getTypeOfVehicle(): ?string
    {
        return $this->typeOfVehicle;
    }

    /**
     * @param string $typeOfVehicle
     */
    public function setTypeOfVehicle(string $typeOfVehicle): void
    {
        $this->typeOfVehicle = $typeOfVehicle;
    }

    /**
     * @return mixed
     */
    public function getPerKmRate()
    {
        return $this->perKmRate;
    }

    /**
     * @param mixed $perKmRate
     */
    public function setPerKmRate($perKmRate): void
    {
        $this->perKmRate = $perKmRate;
    }

}
