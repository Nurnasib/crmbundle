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
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent", inversedBy="crmVisits")
     * @ORM\JoinColumn(name="other_agent_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $otherAgent;

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
     * @ORM\OneToOne(targetEntity="Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails", mappedBy="customer")
     */
    private $farmerIntroduce;

    /**
     * @ORM\OneToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomerStatusLog", mappedBy="customer")
     */
    private $statusLog;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status = 'active';

    /**
     * @var float
     * @ORM\Column(name="approximate_feed_consume", type="float", nullable=true)
     */
    private $approximateFeedConsume;

    /**
     * @var \DateTime
     * @ORM\Column(name="enlisted_on", type="date", nullable=true)
     */
    private $enlistedOn;

    /**
     * @var \DateTime
     * @ORM\Column(name="business_starts_from", type="date", nullable=true)
     */
    private $businessStartsFrom;

    /**
     * @var string
     * @ORM\Column(name="business_type", type="string", nullable=true)
     */
    private $businessType; //e.g. cash, credit, all etc.

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
     * @var \DateTime
     * @ORM\Column(name="deleted_at", type="datetime", nullable = true)
     */

    private $deletedAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="crmCustomer")
     * @ORM\JoinColumn(name="deleted_by", referencedColumnName="id")
     */
    private $deletedBy;


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
     * @return Agent
     */
    public function getOtherAgent()
    {
        return $this->otherAgent;
    }

    /**
     * @param $otherAgent
     */
    public function setOtherAgent($otherAgent): void
    {
        $this->otherAgent = $otherAgent;
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

    /**
     * @return mixed
     */
    public function getFarmerIntroduce()
    {
        return $this->farmerIntroduce;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime $deletedAt
     */
    public function setDeletedAt(\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return User
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * @param User $deletedBy
     */
    public function setDeletedBy(User $deletedBy): void
    {
        $this->deletedBy = $deletedBy;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getApproximateFeedConsume()
    {
        return $this->approximateFeedConsume;
    }

    public function setApproximateFeedConsume( $approximateFeedConsume): void
    {
        $this->approximateFeedConsume = $approximateFeedConsume;
    }

    public function getEnlistedOn()
    {
        return $this->enlistedOn;
    }

    public function setEnlistedOn(?\DateTime $enlistedOn): void
    {
        $this->enlistedOn = $enlistedOn;
    }

    public function getBusinessStartsFrom()
    {
        return $this->businessStartsFrom;
    }

    public function setBusinessStartsFrom(?\DateTime $businessStartsFrom): void
    {
        $this->businessStartsFrom = $businessStartsFrom;
    }

    public function getBusinessType(): string
    {
        return $this->businessType;
    }

    public function setBusinessType(?string $businessType): void
    {
        $this->businessType = $businessType;
    }
    
    //generated customer code using id and strto_pad
    public static function getCustomerCode( $id ): string
    {
        return 'F-' . str_pad($id, 6, '0', STR_PAD_LEFT);
    }


}
