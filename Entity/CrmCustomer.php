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
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="crmVisits")
     * @ORM\JoinColumn(name="custom_group_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $customerGroup;


    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent", inversedBy="crmVisits")
     * @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $agent;


    /**
     * @var Location
     * @ORM\ManyToOne(targetEntity="App\Entity\Admin\Location", inversedBy="crmVisits")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
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

    /**
     * @return Setting
     */
    public function getCustomerGroup()
    {
        return $this->customerGroup;
    }

    /**
     * @param Setting $customerGroup
     */
    public function setCustomerGroup(Setting $customerGroup)
    {
        $this->customerGroup = $customerGroup;
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
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param Location $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }


}
