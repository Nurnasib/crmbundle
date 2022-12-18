<?php

namespace Terminalbd\CrmBundle\Entity;


use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * CrmVisitDetails
 *
 * @ORM\Table(name="crm_visit_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CrmVisitDetailsRepository")
 */
class CrmVisitDetails
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
     * @ORM\Column(name="farmCapacity", type="string",nullable=true)
     */
      private $farmCapacity;

    /**
     * @var string
     * @ORM\Column(name="comments" , type="text", nullable=true)
     */
    private $comments;
    
    /**
     * @var string
     * @ORM\Column(name="process" , type="string",nullable=true)
     */
    private $process;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent", inversedBy="crmVisits")
     * @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $agent;


    /**
     * @var CrmVisit
     * @ORM\ManyToOne(targetEntity="CrmVisit", inversedBy="crmVisitDetails")
     * @ORM\JoinColumn(name="crm_visit_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $crmVisit;


    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer", inversedBy="crmVisitDetails")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $crmCustomer;

     /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmVisitDetails")
     * @ORM\JoinColumn(name="purpose_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $purpose;


    /**
     * @var string
     * @ORM\Column(name="purpose_multiple" , type="text", nullable=true)
     */
    private $purposeMultiple;


    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmVisitDetails")
     * @ORM\JoinColumn(name="firm_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $firmType;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmVisitDetails")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $report;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated", type="datetime", nullable = true)
     */

    private $updated;

    /**
     * @var Api
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Api", inversedBy="appBatch")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=true)
     */
    private $appBatch;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */

    private $appId;

    /**
     * @var string
     * @ORM\Column(name="report_desc" , type="text", nullable=true)
     */
    private $reportDesc;

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
    public function getFarmCapacity()
    {
        return $this->farmCapacity;
    }

    /**
     * @param string $farmCapacity
     */
    public function setFarmCapacity($farmCapacity)
    {
        $this->farmCapacity = $farmCapacity;
    }

    /**
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }


    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
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
    public function setCrmVisit(CrmVisit $crmVisit)
    {
        $this->crmVisit = $crmVisit;
    }

    /**
     * @return CrmCustomer
     */
    public function getCrmCustomer()
    {
        return $this->crmCustomer;
    }

    /**
     * @param CrmCustomer $crmCustomer
     */
    public function setCrmCustomer(CrmCustomer $crmCustomer)
    {
        $this->crmCustomer = $crmCustomer;
    }

    /**
     * @return string
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess(string $process)
    {
        $this->process = $process;
    }

    /**
     * @return Setting
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @param Setting $purpose
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
    }

    /**
     * @return Setting
     */
    public function getFirmType()
    {
        return $this->firmType;
    }

    /**
     * @param Setting $firmType
     */
    public function setFirmType(Setting $firmType): void
    {
        $this->firmType = $firmType;
    }

    /**
     * @return Setting
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param Setting $report
     */
    public function setReport(Setting $report): void
    {
        $this->report = $report;
    }

    /**
     * @return Agent $agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return string
     */
    public function getPurposeMultiple()
    {
        return $this->purposeMultiple;
    }

    /**
     * @param string $purposeMultiple
     */
    public function setPurposeMultiple($purposeMultiple)
    {
        $this->purposeMultiple = $purposeMultiple;
    }

    /**
     * @return Api
     */
    public function getAppBatch()
    {
        return $this->appBatch;
    }

    /**
     * @param Api $appBatch
     */
    public function setAppBatch(Api $appBatch): void
    {
        $this->appBatch = $appBatch;
    }

    /**
     * @return int
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param int $appId
     */
    public function setAppId(int $appId): void
    {
        $this->appId = $appId;
    }

    /**
     * @return string
     */
    public function getReportDesc()
    {
        return $this->reportDesc;
    }

    /**
     * @param string $reportDesc
     */
    public function setReportDesc($reportDesc): void
    {
        $this->reportDesc = $reportDesc;
    }


}
