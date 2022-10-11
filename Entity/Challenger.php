<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 *
 * @ORM\Table(name="crm_challenger")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ChallengerRepository")
 */
class Challenger
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User" , inversedBy="challenger")
     */
    private $employee;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Setting", inversedBy="challenger")
     * @ORM\JoinColumn(name="challenger_feed_name_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $challengerFeedName;

    /**
     * @var string
     * @Orm\Column(type="string", nullable=true)
     */
    private $name;

    /**
     * @var string
     * @Orm\Column(type="string", nullable=true)
     */
    private $challengerType;

    /**
     * @var string
     * @Orm\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

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
     * @return \Terminalbd\CrmBundle\Entity\Setting
     */
    public function getChallengerFeedName()
    {
        return $this->challengerFeedName;
    }

    /**
     * @param \Terminalbd\CrmBundle\Entity\Setting $challengerFeedName
     */
    public function setChallengerFeedName(\Terminalbd\CrmBundle\Entity\Setting $challengerFeedName): void
    {
        $this->challengerFeedName = $challengerFeedName;
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
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getChallengerType()
    {
        return $this->challengerType;
    }

    /**
     * @param string $challengerType
     */
    public function setChallengerType(string $challengerType): void
    {
        $this->challengerType = $challengerType;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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
     * @return Api
     */
    public function getAppBatch()
    {
        return $this->appBatch;
    }

    /**
     * @param Api $appBatch
     */
    public function setAppBatch($appBatch): void
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
    public function setAppId($appId): void
    {
        $this->appId = $appId;
    }


}
