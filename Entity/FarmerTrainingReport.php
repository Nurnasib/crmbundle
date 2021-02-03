<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(name="crm_farmer_training_report")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FarmerTrainingReportRepository")
 */
class FarmerTrainingReport
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="farmerTrainingReport")
     * @ORM\JoinColumn(name="agent_purpose_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $agentPurpose;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="farmerTrainingReport")
     */
    private $agent;

    /**
     * @var FarmerTrainingReportDetails
     * @ORM\OneToMany(targetEntity="Terminalbd\CrmBundle\Entity\FarmerTrainingReportDetails", mappedBy="farmerTrainingReport")
     */
    private $farmerTrainingReportDetails;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="farmerTrainingReport")
     */
    private $employee;

    /**
     * @var \DateTime
     * @ORM\Column(name="training_date", type="date", nullable=true)
     */
    private $trainingDate;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="farmerTrainingReport")
     * @ORM\JoinColumn(name="breed_name", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $breedName;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
     */
    private $trainingMaterial;

    /**
     * @var string
     * @Orm\Column(type="string", nullable=true)
     */

    private $trainingTopics;

    /**
     * @var string
     * @Orm\Column(name="remarks", type="text", nullable=true)
     */

    private $remarks;

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
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param Agent $agent
     */
    public function setAgent(Agent $agent): void
    {
        $this->agent = $agent;
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
    public function setEmployee($employee)
    {
        $this->employee = $employee;
    }

    /**
     * @return Setting
     */
    public function getAgentPurpose()
    {
        return $this->agentPurpose;
    }

    /**
     * @param Setting $agentPurpose
     */
    public function setAgentPurpose(Setting $agentPurpose): void
    {
        $this->agentPurpose = $agentPurpose;
    }

    /**
     * @return FarmerTrainingReportDetails
     */
    public function getFarmerTrainingReportDetails()
    {
        return $this->farmerTrainingReportDetails;
    }

    /**
     * @param FarmerTrainingReportDetails $farmerTrainingReportDetails
     */
    public function setFarmerTrainingReportDetails(FarmerTrainingReportDetails $farmerTrainingReportDetails): void
    {
        $this->farmerTrainingReportDetails = $farmerTrainingReportDetails;
    }

    /**
     * @return \DateTime
     */
    public function getTrainingDate()
    {
        return $this->trainingDate;
    }

    /**
     * @param \DateTime $trainingDate
     */
    public function setTrainingDate(\DateTime $trainingDate): void
    {
        $this->trainingDate = $trainingDate;
    }

    /**
     * @return Setting
     */
    public function getBreedName()
    {
        return $this->breedName;
    }

    /**
     * @param Setting $breedName
     */
    public function setBreedName(Setting $breedName): void
    {
        $this->breedName = $breedName;
    }

    /**
     * @return string
     */
    public function getTrainingMaterial()
    {
        return $this->trainingMaterial;
    }

    /**
     * @param string $trainingMaterial
     */
    public function setTrainingMaterial(string $trainingMaterial): void
    {
        $this->trainingMaterial = $trainingMaterial;
    }

    /**
     * @return string
     */
    public function getTrainingTopics()
    {
        return $this->trainingTopics;
    }

    /**
     * @param string $trainingTopics
     */
    public function setTrainingTopics(string $trainingTopics): void
    {
        $this->trainingTopics = $trainingTopics;
    }

    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param string $remarks
     */
    public function setRemarks(string $remarks): void
    {
        $this->remarks = $remarks;
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
