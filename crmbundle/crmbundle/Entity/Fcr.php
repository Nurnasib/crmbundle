<?php

namespace Terminalbd\CrmBundle\Entity;

use App\Entity\Admin\Location;
use App\Entity\Core\Agent;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 *
 * @ORM\Table(name="crm_fcr")
 * @ORM\Entity(repositoryClass="Terminalbd\CrmBundle\Repository\FcrRepository")
 */
class Fcr
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
     * @ORM\Column(name="cso", type="string",nullable=true)
     */

    private $cso;

    /**
     * @var string
     * @ORM\Column(name="fcr_of_feed", type="string",nullable=true)
     */

    private $fcrOfFeed;

    /**
     * @var string
     * @Orm\Column(name="reporting_month" ,type="string",nullable=true)
     */

    private $reportingMonth;

    /**
     * @var string
     * @Orm\Column(name="hatching_date" ,type="string",nullable=true)
     */
    private $hatchingDate;

    /**
     * @var string
     * @Orm\Column(name="totalbirds" ,type="string",nullable=true)
     */

    private $totalbirds;

    /**
     * @var string
     * @Orm\Column(name="age_day", type="string",nullable=true)
     */
    private $ageday;


    /**
     * @var string
     * @Orm\Column(name="pes", type="string",nullable=true)
     */

    private $Pes;

    /**
     * @var string
     * @Orm\Column(name="weight", type="string",nullable=true)
     */

    private $weight;


    /**
     * @var string
     * @Orm\Column(name="total_feed_consumption", type="string",nullable=true)
     */

    private $totalFeedConsumption;


    /**
     * @var string
     * @Orm\Column(name="hatchery", type="text",nullable=true)
     */

    private $hatchery;

    /**
     * @var string
     * @Orm\Column(name="breed", type="text",nullable=true)
     */

    private $breed;

    /**
     * @var string
     * @Orm\Column(name="feed", type="text",nullable=true)
     */

    private $feed;


    /**
     * @var string
     * @Orm\Column(name="remarks", type="text",nullable=true)
     */

    private $remarks;

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
    public function getCso()
    {
        return $this->cso;
    }

    /**
     * @param string $cso
     */
    public function setCso($cso)
    {
        $this->cso = $cso;
    }

    /**
     * @return string
     */
    public function getFcrOfFeed()
    {
        return $this->fcrOfFeed;
    }

    /**
     * @param string $fcrOfFeed
     */
    public function setFcrOfFeed($fcrOfFeed)
    {
        $this->fcrOfFeed = $fcrOfFeed;
    }

    /**
     * @return string
     */
    public function getReportingMonth()
    {
        return $this->reportingMonth;
    }

    /**
     * @param string $reportingMonth
     */
    public function setReportingMonth($reportingMonth)
    {
        $this->reportingMonth = $reportingMonth;
    }

    /**
     * @return string
     */
    public function getHatchingDate()
    {
        return $this->hatchingDate;
    }

    /**
     * @param string $hatchingDate
     */
    public function setHatchingDate($hatchingDate)
    {
        $this->hatchingDate = $hatchingDate;
    }

    /**
     * @return string
     */
    public function getTotalbirds()
    {
        return $this->totalbirds;
    }

    /**
     * @param string $totalbirds
     */
    public function setTotalbirds($totalbirds)
    {
        $this->totalbirds = $totalbirds;
    }

    /**
     * @return string
     */
    public function getAgeday()
    {
        return $this->ageday;
    }

    /**
     * @param string $ageday
     */
    public function setAgeday($ageday)
    {
        $this->ageday = $ageday;
    }


    /**
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param string $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function getTotalFeedConsumption()
    {
        return $this->totalFeedConsumption;
    }

    /**
     * @param string $totalFeedConsumption
     */
    public function setTotalFeedConsumption($totalFeedConsumption)
    {
        $this->totalFeedConsumption = $totalFeedConsumption;
    }



    /**
     * @return string
     */
    public function getHatchery()
    {
        return $this->hatchery;
    }

    /**
     * @param string $hatchery
     */
    public function setHatchery($hatchery)
    {
        $this->hatchery = $hatchery;
    }

    /**
     * @return string
     */
    public function getBreed()
    {
        return $this->breed;
    }

    /**
     * @param string $breed
     */
    public function setBreed($breed)
    {
        $this->breed = $breed;
    }

    /**
     * @return string
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * @param string $feed
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;
    }

    /**
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * @param string $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     * @return string
     */
    public function getPes()
    {
        return $this->Pes;
    }

    /**
     * @param string $Pes
     */
    public function setPes($Pes)
    {
        $this->Pes = $Pes;
    }





}
