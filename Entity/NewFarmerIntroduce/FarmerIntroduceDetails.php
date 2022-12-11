<?php

namespace Terminalbd\CrmBundle\Entity\NewFarmerIntroduce;

use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 *
 * @ORM\Table(name="crm_customer_introduce_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\NewFarmerIntroduce\FarmerIntroduceDetailsRepository")
")
 */
class FarmerIntroduceDetails
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
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="farmerIntroduce")
     * @ORM\JoinColumn(name="farmerType", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $farmerType;

    /**
     * @var CrmCustomer
     * @ORM\OneToOne(targetEntity="Terminalbd\CrmBundle\Entity\CrmCustomer", inversedBy="farmerIntroduce")
     */
    private $customer;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="farmerIntroduce")
     */
    private $agent;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent" , inversedBy="farmerIntroduce")
     */
    private $otherAgent;

    /**
     * @var Agent
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Agent", inversedBy="farmerIntroduce")
     * @ORM\JoinColumn(referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $subAgent;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting" , inversedBy="farmerIntroduce")
     */
    private $feed;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting" , inversedBy="farmerIntroduce")
     */
    private $otherFeed;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="farmerIntroduce")
     */
    private $employee;

    /**
     * @var string
     * @Orm\Column(name="culture_species_item_and_qty", type="text", nullable=true)
     */
    private $cultureSpeciesItemAndQty;

    /**
     * @var string
     * @Orm\Column(name="remarks", type="text", nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $introduceDate;

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
     * @param $agent
     */
    public function setAgent($agent): void
    {
        $this->agent = $agent;
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
     * @return string
     */
    public function getCultureSpeciesItemAndQty()
    {
        return $this->cultureSpeciesItemAndQty;
    }

    /**
     * @param string $cultureSpeciesItemAndQty
     */
    public function setCultureSpeciesItemAndQty(string $cultureSpeciesItemAndQty): void
    {
        $this->cultureSpeciesItemAndQty = $cultureSpeciesItemAndQty;
    }

    public function getCultureSpeciesQtyByItem(){

        $returnArray = array();
        if ($this->getCultureSpeciesItemAndQty()){
            foreach (json_decode($this->getCultureSpeciesItemAndQty()) as $key=>$item){
                $returnArray[$key]=$item;
            }
        }

        return $returnArray;
    }

    /**
     * @return Setting
     */
    public function getFarmerType()
    {
        return $this->farmerType;
    }

    /**
     * @param Setting $farmerType
     */
    public function setFarmerType(Setting $farmerType): void
    {
        $this->farmerType = $farmerType;
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
     * @return Agent
     */
    public function getSubAgent()
    {
        return $this->subAgent;
    }

    /**
     * @param $subAgent
     */
    public function setSubAgent($subAgent): void
    {
        $this->subAgent = $subAgent;
    }

    /**
     * @return Setting
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param Setting $feed
     */
    public function setFeed(Setting $feed): void
    {
        $this->feed = $feed;
    }

    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param $remarks
     */
    public function setRemarks($remarks): void
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
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getIntroduceDate()
    {
        return $this->introduceDate;
    }

    /**
     * @param $introduceDate
     */
    public function setIntroduceDate($introduceDate): void
    {
        $this->introduceDate = $introduceDate;
    }


    /**
     * @return Setting
     */
    public function getOtherFeed()
    {
        return $this->otherFeed;
    }

    /**
     * @param Setting $otherFeed
     */
    public function setOtherFeed(Setting $otherFeed): void
    {
        $this->otherFeed = $otherFeed;
    }

}
