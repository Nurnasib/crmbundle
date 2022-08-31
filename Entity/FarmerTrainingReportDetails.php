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
 * @ORM\Table(name="crm_farmer_training_report_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FarmerTrainingReportRepository")
 */
class FarmerTrainingReportDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var FarmerTrainingReport
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\FarmerTrainingReport", inversedBy="farmerTrainingReportDetails")
     * @ORM\JoinColumn(name="farmer_training_report_id", referencedColumnName="id", onDelete="CASCADE")
     */

    private $farmerTrainingReport;

    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer" , inversedBy="farmerTrainingReportDetails")
     */
    private $customer;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
     */
    private $trainingMaterialQty;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
     */
    private $farmerCapacity;

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
     * @return FarmerTrainingReport
     */
    public function getFarmerTrainingReport()
    {
        return $this->farmerTrainingReport;
    }

    /**
     * @param FarmerTrainingReport $farmerTrainingReport
     */
    public function setFarmerTrainingReport(FarmerTrainingReport $farmerTrainingReport): void
    {
        $this->farmerTrainingReport = $farmerTrainingReport;
    }

    /**
     * @return CrmCustomer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param CrmCustomer $customer
     */
    public function setCustomer(CrmCustomer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return string
     */
    public function getTrainingMaterialQty()
    {
        return $this->trainingMaterialQty;
    }

    /**
     * @param string $trainingMaterialQty
     */
    public function setTrainingMaterialQty(string $trainingMaterialQty): void
    {
        $this->trainingMaterialQty = $trainingMaterialQty;
    }


    /**
     * @return string
     */
    public function getFarmerCapacity()
    {
        return $this->farmerCapacity;
    }

    /**
     * @param string $farmerCapacity
     */
    public function setFarmerCapacity(string $farmerCapacity): void
    {
        $this->farmerCapacity = $farmerCapacity;
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
