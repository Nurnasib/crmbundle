<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Repository;

//use Doctrine\ORM\EntityRepository;
use Terminalbd\CrmBundle\Repository\BaseRepository;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class LayerLifeCycleRepository extends BaseRepository
{
    public function getLayerLifeCycleReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select('e.id as lifeCycleID', 'e.totalBirds', 'e.hatcheryDate', 'e.created');
        $qb->addSelect('crmLayerLifeCycleDetails.visitingDate', 'crmLayerLifeCycleDetails.ageWeek', 'crmLayerLifeCycleDetails.deadBird', 'crmLayerLifeCycleDetails.avgWeight', 'crmLayerLifeCycleDetails.targetWeight', 'crmLayerLifeCycleDetails.uniformity', 'crmLayerLifeCycleDetails.feedPerBird', 'crmLayerLifeCycleDetails.targetFeedPerBird', 'crmLayerLifeCycleDetails.totalEggs', 'crmLayerLifeCycleDetails.targetEggProduction', 'crmLayerLifeCycleDetails.eggWeightActual', 'crmLayerLifeCycleDetails.eggWeightStandard', 'crmLayerLifeCycleDetails.productionDate', 'crmLayerLifeCycleDetails.batch_no', 'crmLayerLifeCycleDetails.medicine', 'crmLayerLifeCycleDetails.remarks');
        $qb->addSelect('feedType.name AS feedTypeName');
        $qb->addSelect('feedMill.name AS feedMillName');
        $qb->addSelect('farmer.name AS farmerName','farmer.address AS farmerAddress','farmer.mobile AS farmerMobile');
        $qb->addSelect('hatchery.name AS hatcheryName');
        $qb->addSelect('breed.name AS breedName');

        $qb->join('e.crmLayerLifeCycleDetails', 'crmLayerLifeCycleDetails');
        $qb->leftJoin('crmLayerLifeCycleDetails.feedType', 'feedType');
        $qb->leftJoin('crmLayerLifeCycleDetails.feedMill', 'feedMill');
        $qb->leftJoin('e.hatchery', 'hatchery');
        $qb->leftJoin('e.breed', 'breed');

        $this->handleSearchFilterBetween($qb, $filterBy);

        $results = $qb->getQuery()->getArrayResult();

        $returnArray = [];

        foreach ($results as $result){
//            $month = $result->getCreated()->format('F-Y') ;
            $returnArray[$result['lifeCycleID']][] = $result;
            $returnArray['farmerName'] = $result['farmerName'];
            $returnArray['farmerAddress'] = $result['farmerAddress'];
            $returnArray['farmerMobile'] = $result['farmerMobile'];
            $returnArray['totalBirds'] = $result['totalBirds'];
            $returnArray['hatcheryDate'] = $result['hatcheryDate'];
            $returnArray['hatcheryName'] = $result['hatcheryName'];
            $returnArray['breedName'] = $result['breedName'];
        }
        return $returnArray;
//        return $results;

//        dd($returnArray);
    }


}
