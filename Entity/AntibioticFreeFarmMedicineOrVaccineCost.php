<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 *
 * @ORM\Table(name="crm_antibiotic_free_farm_medicine_or_vaccine_cost")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\AntibioticFreeFarmMedicineOrVaccineCostRepository")
 */
class AntibioticFreeFarmMedicineOrVaccineCost
{
    const COST_TYPE_MEDICINE = 'MEDICINE';
    const COST_TYPE_VACCINE = 'VACCINE';
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var AntibioticFreeFarm
     * @ORM\ManyToOne(targetEntity="AntibioticFreeFarm", inversedBy="antibioticFreeFarmMedicineOrCost")
     * @ORM\JoinColumn(name="crm_antibiotic_free_farm_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $crmAntibioticFreeFarm;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */

    private $medicineOrVaccineName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */

    private $purposeOrDisease;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $ageDays=0;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */

    private $dosage;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $quantity=0;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $price=0;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $costType;

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
     * @return AntibioticFreeFarm
     */
    public function getCrmAntibioticFreeFarm()
    {
        return $this->crmAntibioticFreeFarm;
    }

    /**
     * @param AntibioticFreeFarm $crmAntibioticFreeFarm
     */
    public function setCrmAntibioticFreeFarm(AntibioticFreeFarm $crmAntibioticFreeFarm): void
    {
        $this->crmAntibioticFreeFarm = $crmAntibioticFreeFarm;
    }

    /**
     * @return string
     */
    public function getMedicineOrVaccineName()
    {
        return $this->medicineOrVaccineName;
    }

    /**
     * @param string $medicineOrVaccineName
     */
    public function setMedicineOrVaccineName(string $medicineOrVaccineName): void
    {
        $this->medicineOrVaccineName = $medicineOrVaccineName;
    }

    /**
     * @return string
     */
    public function getPurposeOrDisease()
    {
        return $this->purposeOrDisease;
    }

    /**
     * @param string $purposeOrDisease
     */
    public function setPurposeOrDisease(string $purposeOrDisease): void
    {
        $this->purposeOrDisease = $purposeOrDisease;
    }

    /**
     * @return float
     */
    public function getAgeDays()
    {
        return $this->ageDays;
    }

    /**
     * @param float $ageDays
     */
    public function setAgeDays(float $ageDays): void
    {
        $this->ageDays = $ageDays;
    }

    /**
     * @return string
     */
    public function getDosage()
    {
        return $this->dosage;
    }

    /**
     * @param string $dosage
     */
    public function setDosage(string $dosage): void
    {
        $this->dosage = $dosage;
    }

    /**
     * @return float
     */
    public function getQuantity()
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
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getCostType()
    {
        return $this->costType;
    }

    /**
     * @param string $costType
     */
    public function setCostType(string $costType): void
    {
        $this->costType = $costType;
    }



}
