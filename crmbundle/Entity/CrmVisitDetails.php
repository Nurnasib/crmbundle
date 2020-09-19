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
     * @ORM\Column(name="comments" , type="string",nullable=true)
     */
    private $comments;

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="CrmVisit", inversedBy="crmVisitDetails")
     * @ORM\JoinColumn(name="crm_visit_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $crmVisitId;

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="CrmCustomer", inversedBy="crmVisitDetails")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $crmCustomer;

    /**
     * @var string
     * @ORM\Column(name="purpose", type="text",nullable=true)
     */
    private $purpose;

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
     * @return string
     */
    public function getCrmVisitId()
    {
        return $this->crmVisitId;
    }

    /**
     * @param string $crmVisitId
     */
    public function setCrmVisitId($crmVisitId)
    {
        $this->crmVisitId = $crmVisitId;
    }

    /**
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @param string $purpose
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
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






}
