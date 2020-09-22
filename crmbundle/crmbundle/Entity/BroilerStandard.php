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
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\BroilerStandardRepository")
 * @ORM\Table(name="crm_broiler_standard")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class BroilerStandard
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
     * @ORM\Column(name="age", type="string")
     */
    private $age;


    /**
     * @var string
     * @ORM\Column(name="target_body_weight", type="string")
     */
    private $targetBodyWeight;

    /**
     * @var string
     * @ORM\Column(name="target_feed_consumption", type="string")
     */

    private $targetFeedConsumption;

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
    public function getAge()
    {
        return $this->age;
    }

    /**
     * @param string $age
     */
    public function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getTargetBodyWeight()
    {
        return $this->targetBodyWeight;
    }

    /**
     * @param string $targetBodyWeight
     */
    public function setTargetBodyWeight($targetBodyWeight)
    {
        $this->targetBodyWeight = $targetBodyWeight;
    }

    /**
     * @return string
     */
    public function getTargetFeedConsumption()
    {
        return $this->targetFeedConsumption;
    }

    /**
     * @param string $targetFeedConsumption
     */
    public function setTargetFeedConsumption($targetFeedConsumption)
    {
        $this->targetFeedConsumption = $targetFeedConsumption;
    }









}
