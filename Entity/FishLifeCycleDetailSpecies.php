<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fish_life_cycle_detail_species")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FishLifeCycleDetailSpeciesRepository")
 */
class FishLifeCycleDetailSpecies
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */

    private $id;

    /**
     * @var FishLifeCycleDetails
     * @ORM\ManyToOne(targetEntity="Terminalbd\CrmBundle\Entity\FishLifeCycleDetails", inversedBy="fishLifeCycleDetailSpecies")
     * @ORM\JoinColumn(name="fish_life_cycle_details_id", referencedColumnName="id")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */

    private $fishLifeCycleDetails;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetailSpecies")
     * @ORM\JoinColumn(name="mainCultureSpecies", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $mainCultureSpecies;

    /**
     * @var Setting
     * @ORM\ManyToOne(targetEntity="Setting", inversedBy="fishLifeCycleDetailSpecies")
     * @ORM\JoinColumn(name="feed_type_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $feedType;

    /**
     * @var float
     * @Orm\Column(name="feed_consumption_kg", type="float")
     */
    private $feedConsumptionKg=0;

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
     * @return FishLifeCycleDetails
     */
    public function getFishLifeCycleDetails()
    {
        return $this->fishLifeCycleDetails;
    }

    /**
     * @param FishLifeCycleDetails $fishLifeCycleDetails
     */
    public function setFishLifeCycleDetails(FishLifeCycleDetails $fishLifeCycleDetails): void
    {
        $this->fishLifeCycleDetails = $fishLifeCycleDetails;
    }

    /**
     * @return Setting
     */
    public function getMainCultureSpecies()
    {
        return $this->mainCultureSpecies;
    }

    /**
     * @param Setting $mainCultureSpecies
     */
    public function setMainCultureSpecies(Setting $mainCultureSpecies): void
    {
        $this->mainCultureSpecies = $mainCultureSpecies;
    }

    /**
     * @return Setting
     */
    public function getFeedType()
    {
        return $this->feedType;
    }

    /**
     * @param Setting $feedType
     */
    public function setFeedType(Setting $feedType): void
    {
        $this->feedType = $feedType;
    }

    /**
     * @return float
     */
    public function getFeedConsumptionKg()
    {
        return $this->feedConsumptionKg;
    }

    /**
     * @param float $feedConsumptionKg
     */
    public function setFeedConsumptionKg(float $feedConsumptionKg): void
    {
        $this->feedConsumptionKg = $feedConsumptionKg;
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
