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


}
