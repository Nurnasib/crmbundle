<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * CrmCustomer
 *
 * @ORM\Table(name="crm_visit")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CrmVisitRepository")
 */
class CrmVisit
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
     * @ORM\Column(name="cso_id", type="string",nullable=true)
     */

    private $cso_id;

    /**
     * @var string
     * @ORM\Column(name="working_duration", type="string",nullable=true)
     */

    private $workingDuration;

    /**
     * @var string
     * @ORM\Column(name="area_name", type="string",nullable=true)
     */

    private $area_name;


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
     * @ORM\OneToMany(targetEntity="CrmVisitDetails", mappedBy="crmVisitId")
     */
    private $crmVisitDetails;

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
    public function getCsoId()
    {
        return $this->cso_id;
    }

    /**
     * @param string $cso_id
     */
    public function setCsoId($cso_id)
    {
        $this->cso_id = $cso_id;
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
     * @return mixed
     */
    public function getCrmVisitDetails()
    {
        return $this->crmVisitDetails;
    }

    /**
     * @return string
     */
    public function getWorkingDuration()
    {
        return $this->workingDuration;
    }

    /**
     * @param string $workingDuration
     */
    public function setWorkingDuration($workingDuration)
    {
        $this->workingDuration = $workingDuration;
    }

    /**
     * @return string
     */
    public function getAreaName()
    {
        return $this->area_name;
    }

    /**
     * @param string $area_name
     */
    public function setAreaName($area_name)
    {
        $this->area_name = $area_name;
    }




}
