<?php

namespace Terminalbd\CrmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * CrmCustomer
 *
 * @ORM\Table(name="crm_api")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\ApiRepository")
 */
class Api
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $deviceId;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $employeeId;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $process;

    /**
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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getDeviceId(): int
    {
        return $this->deviceId;
    }

    /**
     * @param int $deviceId
     */
    public function setDeviceId(int $deviceId): void
    {
        $this->deviceId = $deviceId;
    }

    /**
     * @return int
     */
    public function getEmployeeId(): int
    {
        return $this->employeeId;
    }

    /**
     * @param int $employeeId
     */
    public function setEmployeeId(int $employeeId): void
    {
        $this->employeeId = $employeeId;
    }

    /**
     * @return string
     */
    public function getProcess(): string
    {
        return $this->process;
    }

    /**
     * @param string $process
     */
    public function setProcess(string $process): void
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
    public function setJsonData($jsonData): void
    {
        $this->jsonData = $jsonData;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

}
