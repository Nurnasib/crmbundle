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

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\SettingRepository")
 * @ORM\Table(name="crm_employee")
 * @author Md Shafiqul Islam <shafiqabs@gmail.com>
 */
class Employee
{
    private $rootDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->rootDir = $kernel->getProjectDir();
    }

    public function getRootDir(){
        return $this->rootDir;
    }

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;


    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="App\Entity\User")
     */
    private $user;


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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }


}
