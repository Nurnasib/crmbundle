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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\SettingLifeCycleRepository")
 * @ORM\Table(name="crm_setting_life_cycle")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class SettingLifeCycle
{

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var float
     * @Orm\Column(name="number_of_week", type="float")
     */

    private $numberOfWeek=0;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="settingLifeCycle")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $report;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status = true;

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getNumberOfWeek(): float
    {
        return $this->numberOfWeek;
    }

    /**
     * @param float $numberOfWeek
     */
    public function setNumberOfWeek(float $numberOfWeek): void
    {
        $this->numberOfWeek = $numberOfWeek;
    }

    /**
     * @return Setting
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param Setting $report
     */
    public function setReport(Setting $report): void
    {
        $this->report = $report;
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
    public function setStatus(bool $status)
    {
        $this->status = $status;
    }



}
