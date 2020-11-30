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

use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\LayerLifeCycleRepository")
 * @ORM\Table(name="crm_layer_life_cycle")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class LayerLifeCycle
{
    const LIFE_CYCLE_STATE_COMPLETE = 'COMPLETE';
    const LIFE_CYCLE_STATE_CANCEL = 'CANCEL';
    const LIFE_CYCLE_STATE_IN_PROGRESS = 'IN_PROGRESS';

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var LayerLifeCycleDetails
     * @ORM\OneToMany(targetEntity="LayerLifeCycleDetails", mappedBy="crmLayerLifeCycle")
     */
    private $crmLayerLifeCycleDetails;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="crmLayerLifeCycle")
     */
    private $employee;


    /**
     * @var CrmCustomer
     * @ORM\ManyToOne(targetEntity="CrmCustomer" , inversedBy="crmLayerLifeCycle")
     */
    private $customer;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmLayerLifeCycle")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $report;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="crmLayerLifeCycle")
     */
    private $agent;

    /**
     * @var float
     * @ORM\Column(name="total_birds", type="float")
     */
    private $totalBirds=0;

    /**
     * @var \DateTime
     * @ORM\Column(name="hatchery_date", type="date", nullable=true)
     */
    private $hatcheryDate;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */

    private $updated;

    /**
     * @var string
     *
     * @ORM\Column(name="life_cycle_state", type="string", length=20, nullable=true)
     */
    private $lifeCycleState;

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
     * @return LayerLifeCycleDetails
     */
    public function getCrmLayerLifeCycleDetails()
    {
        return $this->crmLayerLifeCycleDetails;
    }

    /**
     * @param LayerLifeCycleDetails $crmLayerLifeCycleDetails
     */
    public function setCrmLayerLifeCycleDetails(LayerLifeCycleDetails $crmLayerLifeCycleDetails): void
    {
        $this->crmLayerLifeCycleDetails = $crmLayerLifeCycleDetails;
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
     * @return float
     */
    public function getTotalBirds()
    {
        return $this->totalBirds;
    }

    /**
     * @param float $totalBirds
     */
    public function setTotalBirds($totalBirds)
    {
        $this->totalBirds = $totalBirds;
    }

    /**
     * @return \DateTime
     */
    public function getHatcheryDate()
    {
        return $this->hatcheryDate;
    }

    /**
     * @param \DateTime $hatcheryDate
     */
    public function setHatcheryDate(\DateTime $hatcheryDate): void
    {
        $this->hatcheryDate = $hatcheryDate;
    }

    /**
     * @return string
     */
    public function getHatchery()
    {
        return $this->hatchery;
    }

    /**
     * @param string $hatchery
     */
    public function setHatchery(string $hatchery): void
    {
        $this->hatchery = $hatchery;
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
    public function setCreated(\DateTime $created): void
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
    public function setUpdated(\DateTime $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getLifeCycleState()
    {
        return $this->lifeCycleState;
    }

    /**
     * @param string $lifeCycleState
     */
    public function setLifeCycleState(string $lifeCycleState): void
    {
        $this->lifeCycleState = $lifeCycleState;
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


}
