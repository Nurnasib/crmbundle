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

use Doctrine\ORM\EntityRepository;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ComplainDifferentProductRepository extends EntityRepository
{
    public function getComplainByCreatedDateEmployeeReport($report, $employee)
    {
        if($report&&$employee){
            $startDate = date('Y-01-01');
//            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $query = $this->createQueryBuilder('cdp')
                ->where('cdp.createdAt >= :startDate')
                ->andWhere('cdp.createdAt <= :endDate')
                ->andWhere('cdp.report = :report')
                ->andWhere('cdp.employee = :employee')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'report'=>$report, 'employee'=>$employee));
            $returnArray = [];

            foreach ($query->getQuery()->getResult() as $value){
                $createdMonth = $value->getCreatedAt()->format('F');

                $returnArray[$createdMonth][$value->getProductName()->getId()][]=$value;
            }
//            dd($returnArray);
            return $returnArray;
        }
        return array();
    }


    public function getComplainBreedAndFeedByType($type)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.breed', 'breed');
        $qb->leftJoin('breed.parent','breedParent');
        //feedId
        $qb->leftJoin('e.feed', 'feed');
        $qb->leftJoin('feed.parent', 'feedParent');


        $qb->select('breed.name as breedName');
        $qb->addSelect('breedParent.name as breedParentName', 'breedParent.id as breedParentId');
        $qb->addSelect('feed.name as feedName');
        $qb->addSelect('feedParent.name as feedParentName', 'feedParent.id as feedParentId');

        if($type == 'COMPLAIN_DOC'){
            $qb->where('e.breed IS NOT NULL');
            $qb->addGroupBy('breedParentId');
        }elseif ($type == 'COMPLAIN_FEED'){
            $qb->where('e.feed IS NOT NULL');
            $qb->addGroupBy('feedParentId');
        }
        $results = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($results as $result) {
            if ($type == 'COMPLAIN_DOC') {
                $data[$result['breedParentId']]= $result['breedParentName'];
            }
            if ($type == 'COMPLAIN_FEED') {
                $data[$result['feedParentId']]= $result['feedParentName'];
            }

        }
        return $data;
    }


}
