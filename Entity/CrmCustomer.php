<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;



/**
 * CrmCustomer
 *
 * @ORM\Table(name="crm_customers")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\CrmCustomerRepository")
 * @UniqueEntity(fields="mobile", message="This phone number already exists")
 *
 */
class CrmCustomer
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
     * @ORM\Column(name="name", type="string",nullable=true)
     */

    private $name;

    /**
     * @var string
     * @ORM\Column(name="mobile", type="string",nullable=true)
     */
    private $mobile;

    /**
     * @var string
     * @Orm\Column(name="address" ,type="string",nullable=true)
     */

    private $address;

    /**
     * @var string
     * @Orm\Column(name="custom_group", type="string",nullable=true)
     */
    private $customerGroup;

    /**
     * @var string
     * @Orm\Column(name="agentId", type="string",nullable=true)
     */

    private $agentId;

    /**
     * @var string
     * @Orm\Column(name="subagentId",type="string",nullable=true)
     */

    private $subAgentId;

    /**
     * @var string
     * @Orm\Column(name="location", type="text",nullable=true)
     */

    private $location;

    /**
     * @ORM\OneToMany(targetEntity="CrmVisitDetails", mappedBy="crmCustomer")
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getCustomerGroup()
    {
        return $this->customerGroup;
    }

    /**
     * @param string $customerGroup
     */
    public function setCustomerGroup($customerGroup)
    {
        $this->customerGroup = $customerGroup;
    }

    /**
     * @return string
     */
    public function getAgentId()
    {
        return $this->agentId;
    }

    /**
     * @param string $agentId
     */
    public function setAgentId($agentId)
    {
        $this->agentId = $agentId;
    }

    /**
     * @return string
     */
    public function getSubAgentId()
    {
        return $this->subAgentId;
    }

    /**
     * @param string $subAgentId
     */
    public function setSubAgentId($subAgentId)
    {
        $this->subAgentId = $subAgentId;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
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
     * @return mixed
     */
    public function getCrmVisitDetails()
    {
        return $this->crmVisitDetails;
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
     *@return string
     *
     */

    public function getNameAndPhone(){

        return $this->getName() .'-'.$this->getMobile();

    }


}
