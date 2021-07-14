<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fish_company_species_wise_average_fcr_details")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FishCompanyAndSpeciesWiseAverageFcrDetailsRepository")
 */
class FishCompanyAndSpeciesWiseAverageFcrDetails
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var FishCompanyAndSpeciesWiseAverageFcr
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\FishCompanyAndSpeciesWiseAverageFcr", inversedBy="fishCompanyAndSpeciesWiseAverageFcrDetails")
     * @ORM\JoinColumn(name="fish_company_and_species_wise_fcr_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $fishCompanyAndSpeciesWiseAverageFcr;


    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishCompanyAndSpeciesWiseAverageFcrDetails")
     * @ORM\JoinColumn(name="species_name_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $speciesName;

    /**
     * @var float
     * @Orm\Column( type="float")
     */
    private $quantity=0;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

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
     * @return FishCompanyAndSpeciesWiseAverageFcr
     */
    public function getFishCompanyAndSpeciesWiseAverageFcr()
    {
        return $this->fishCompanyAndSpeciesWiseAverageFcr;
    }

    /**
     * @param FishCompanyAndSpeciesWiseAverageFcr $fishCompanyAndSpeciesWiseAverageFcr
     */
    public function setFishCompanyAndSpeciesWiseAverageFcr(FishCompanyAndSpeciesWiseAverageFcr $fishCompanyAndSpeciesWiseAverageFcr): void
    {
        $this->fishCompanyAndSpeciesWiseAverageFcr = $fishCompanyAndSpeciesWiseAverageFcr;
    }

    /**
     * @return Setting
     */
    public function getSpeciesName()
    {
        return $this->speciesName;
    }

    /**
     * @param Setting $speciesName
     */
    public function setSpeciesName(Setting $speciesName): void
    {
        $this->speciesName = $speciesName;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity(float $quantity)
    {
        $this->quantity = $quantity;
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


}
