<?php

namespace Terminalbd\CrmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * CrmCustomer
 *
 * @ORM\Table(name="crm_api_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ApiDetailsRepository")
 */
class ApiDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var $batch
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\Api", inversedBy="apiDetails")
     * @ORM\JoinColumn(name="batch_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $batch;

    /**
     * @var $process
     * @ORM\Column(type="string")
     */
    private $process;

    /**
     * @var $jsonData
     * @ORM\Column(type="text")
     */
    private $jsonData;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $status = false;

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
     * @return mixed
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param mixed $batch
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;
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
    public function setProcess($process)
    {
        $this->process = $process;
    }

    /**
     * @return mixed
     */
    public function getJsonData()
    {
        return $this->jsonData;
    }

    /**
     * @param mixed $jsonData
     */
    public function setJsonData($jsonData)
    {
        $this->jsonData = $jsonData;
    }

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

}
