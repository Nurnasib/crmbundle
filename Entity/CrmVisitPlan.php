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

use App\Entity\Admin\Location;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CrmVIsitPlanRepository")
 * @ORM\Table(name="crm_visit_plan")
 * @author Rakeybul Hasan <rakeybul.rbs@gmail.com>
 */
class CrmVisitPlan
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="crmVisitPlan")
     * @ORM\JoinColumn(name="employee_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     *
     */
    private $employee;

    /**
     * @var string
     * @ORM\Column(name="visiting_area", type="string", nullable=true )
     */
    private $visitingArea;

    /**
     * @var \DateTime
     * @ORM\Column(name="visit_date", type="date", nullable=true)
     */
    private $visitDate;

    /**
     * @ORM\Column( type="json", name="agent_list", nullable=true)
     */
    private $agentList;

    /**
     * @ORM\Column( type="json", name="area_list", nullable=true)
     */
    private $areaList;


    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime")
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
     * @return User
     */
    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    /**
     * @param User|null $employee
     */
    public function setEmployee(?User $employee): void
    {
        $this->employee = $employee;
    }

    /**
     * @return string|null
     */
    public function getVisitingArea(): ?string
    {
        return $this->visitingArea;
    }

    /**
     * @param string $visitingArea
     */
    public function setVisitingArea(?string $visitingArea): void
    {
        $this->visitingArea = $visitingArea;
    }

    /**
     * @return \DateTime
     */
    public function getVisitDate(): \DateTime
    {
        return $this->visitDate;
    }

    /**
     * @param \DateTime $visitDate
     */
    public function setVisitDate(\DateTime $visitDate): void
    {
        $this->visitDate = $visitDate;
    }

    /**
     * @return string
     */
    public function getAgentList(): ?array
    {
        return $this->agentList;
    }

    /**
     * @param string $agentList
     */
    public function setAgentList(?array $agentList): void
    {
        $this->agentList = $agentList;
    }

    /**
     * @return string
     */
    public function getAreaList(): ?array
    {
        return $this->areaList;
    }

    /**
     * @param string $areaList
     */
    public function setAreaList(?array $areaList): void
    {
        $this->areaList = $areaList;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
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
