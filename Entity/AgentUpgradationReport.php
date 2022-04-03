<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
//use Terminalbd\CrmBundle\Entity\Setting;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Table(name="crm_agent_upgradation_report")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\AgentUpgradationReportRepository")
 */
class AgentUpgradationReport
{
    const AGENT_STATUS_NEW = 'NEW';
    const AGENT_STATUS_UPGRADE = 'UPGRADE';
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="agentUpgradationReport")
     * @ORM\JoinColumn(name="agent_purpose_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $agentPurpose;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="agentUpgradationReport")
     */
    private $agent;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="agentUpgradationReport")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="agentUpgradationReport")
     * @ORM\JoinColumn(name="breed_name", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $breedName;

    /**
     * @var string
     * @Orm\Column(type="string", nullable=true)
     */

    private $agentStatus;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $previousSaleTon=0;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $presentSaleTon=0;

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
     * @var \DateTime
     * @ORM\Column(name="reporting_month", type="date", nullable=true)
     */
    private $reportingMonth;

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
    public function getAgentStatus()
    {
        return $this->agentStatus;
    }

    /**
     * @param string $agentStatus
     */
    public function setAgentStatus(string $agentStatus): void
    {
        $this->agentStatus = $agentStatus;
    }

    /**
     * @return float
     */
    public function getPreviousSaleTon()
    {
        return $this->previousSaleTon;
    }

    /**
     * @param float $previousSaleTon
     */
    public function setPreviousSaleTon(float $previousSaleTon): void
    {
        $this->previousSaleTon = $previousSaleTon;
    }

    /**
     * @return float
     */
    public function getPresentSaleTon()
    {
        return $this->presentSaleTon;
    }

    /**
     * @param float $presentSaleTon
     */
    public function setPresentSaleTon(float $presentSaleTon): void
    {
        $this->presentSaleTon = $presentSaleTon;
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

    /**
     * @return \DateTime
     */
    public function getReportingMonth()
    {
        return $this->reportingMonth;
    }

    /**
     * @param \DateTime $reportingMonth
     */
    public function setReportingMonth(\DateTime $reportingMonth): void
    {
        $this->reportingMonth = $reportingMonth;
    }

    public function calculationPreviousCategory()
    {
        $result = '';
        if($this->getBreedName()->getSlug()=='cattle-breed'){
            if($this->previousSaleTon>=30){
              $result='A';
            }elseif ($this->previousSaleTon>=20 && $this->previousSaleTon<30){
                $result='B';
            }elseif ($this->previousSaleTon>=10 && $this->previousSaleTon<20){
                $result='C';
            }elseif ($this->previousSaleTon<10){
                $result='F';
            }
        }elseif ($this->getBreedName()->getSlug()=='fish-breed'){
            if($this->previousSaleTon>=50){
                $result='A';
            }elseif ($this->previousSaleTon>=30 && $this->previousSaleTon<50){
                $result='B';
            }elseif ($this->previousSaleTon>=15 && $this->previousSaleTon<30){
                $result='C';
            }elseif ($this->previousSaleTon<15){
                $result='F';
            }
        }
        return $result;
        
    }

    public function calculationPresentCategory()
    {
        $result = '';
        if($this->getBreedName()->getSlug()=='cattle-breed'){
            if($this->presentSaleTon>=30){
              $result='A';
            }elseif ($this->presentSaleTon>=20 && $this->presentSaleTon<30){
                $result='B';
            }elseif ($this->presentSaleTon>=10 && $this->presentSaleTon<20){
                $result='C';
            }elseif ($this->presentSaleTon<10){
                $result='F';
            }
        }elseif ($this->getBreedName()->getSlug()=='fish-breed'){
            if($this->presentSaleTon>=50){
                $result='A';
            }elseif ($this->presentSaleTon>=30 && $this->presentSaleTon<50){
                $result='B';
            }elseif ($this->presentSaleTon>=15 && $this->presentSaleTon<30){
                $result='C';
            }elseif ($this->presentSaleTon<15){
                $result='F';
            }
        }
        return $result;

    }

}
