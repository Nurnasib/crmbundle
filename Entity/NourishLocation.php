<?php

namespace Terminalbd\CrmBundle\Entity;


use App\Entity\Admin\Terminal;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Terminalbd\KpiBundle\Entity\LocationSalesTarget;

/**
 * Category
 *
 * @Gedmo\Tree(type="materializedPath")
 * @ORM\Table(name="nourish_location")
 * @ORM\Entity(repositoryClass="App\Repository\Admin\NourishLocationRepository")
 */
class NourishLocation
{
    /**
     * @var integer
     *
     * @Gedmo\TreePathSource
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;


    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Terminal
     * @ORM\OneToMany(targetEntity="Terminal", mappedBy="location")
     **/
    protected $terminals;


    /**
     * @var User
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="upozila")
     **/
    protected $user;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Location", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $parent;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    private $level;


    /**
     * @ORM\Column(name="oldId", type="integer", nullable=true)
     */
    private $oldId;

    /**
     * @ORM\OneToMany(targetEntity="Location" , mappedBy="parent")
     * @ORM\OrderBy({"name" = "ASC"})
     **/
    private $children;

    /**
     * @Gedmo\TreePath(separator="/")
     * @ORM\Column(name="path", type="string", length=3000, nullable=true)
     */
    private $path;

    /**
     * @var LocationSalesTarget
     *
     * @ORM\OneToMany(targetEntity="Terminalbd\KpiBundle\Entity\LocationSalesTarget" , mappedBy="upozila")
     */
    private $salesTarget;



    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Location
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Location
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getNestedLabel()
    {
        if ($this->getLevel() > 1) {
            return $this->formatLabel($this->getLevel() - 1, $this->getName());
        } else {
            return $this->getName();
        }
    }

    public function getParentIdByLevel($level = 1)
    {
        $parentsIds = explode("/", $this->getPath());

        return isset($parentsIds[$level - 1]) ? $parentsIds[$level - 1] : null;

    }

    private function formatLabel($level, $value)
    {
        return str_repeat("-", $level * 3) . str_repeat(">", $level) . $value;
    }

    /**
     * @return mixed
     */
    public function getOldId()
    {
        return $this->oldId;
    }

    /**
     * @param mixed $oldId
     */
    public function setOldId($oldId)
    {
        $this->oldId = $oldId;
    }

    /**
     * @return LocationSalesTarget
     */
    public function getSalesTarget()
    {
        return $this->salesTarget;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


}
