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

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class LayerLifeCycleDetailsRepository extends BaseRepository
{
    public function getLayerLifeCycleDetails($lifeCycleSlug, $filterBy)
    {
//        $startDate = $filterBy['startDate'] ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : '';
//        $endDate = $filterBy['endDate'] ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : '';

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.crmLayerLifeCycle', 'crm_layer_life_cycle');
        $qb->join('crm_layer_life_cycle.report', 'report');
        $qb->join('crm_layer_life_cycle.employee', 'employee');
//        $qb->leftJoin('crm_layer_life_cycle.agent', 'agent');
        $qb->join('crm_layer_life_cycle.customer', 'customer');
        $qb->leftJoin('crm_layer_life_cycle.hatchery', 'hatchery');
        $qb->leftJoin('crm_layer_life_cycle.feed', 'feed');
        $qb->leftJoin('crm_layer_life_cycle.breed', 'breed');
        $qb->leftJoin('e.feedType', 'feed_type');
        $qb->leftJoin('e.feedMill', 'feed_mill');

        $qb->select('e AS details');
//        $qb->addSelect('agent.name AS agentName','agent.address AS agentAddress');
        $qb->addSelect('customer.name AS customerName','customer.address AS customerAddress','customer.mobile AS customerMobile');
        $qb->addSelect('crm_layer_life_cycle.id AS lifeCycleId','crm_layer_life_cycle.lifeCycleState', 'crm_layer_life_cycle.totalBirds', 'crm_layer_life_cycle.hatcheryDate');
        $qb->addSelect('hatchery.name AS hatcheryName');
        $qb->addSelect('feed.name AS feedName');
        $qb->addSelect('breed.name AS breedName');
        $qb->addSelect('feed_type.name AS feedTypeName');
        $qb->addSelect('feed_mill.name AS feedMillName');

        $qb->where('report.slug = :slug')->setParameter('slug', $lifeCycleSlug);
//        $qb->andWhere('e.visitingDate >= :startDate')->setParameter('startDate', $startDate);
//        $qb->andWhere('e.visitingDate <= :endDate')->setParameter('endDate', $endDate);
        $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('crm_layer_life_cycle.lifeCycleState = :reportStatus')->setParameter('reportStatus', $filterBy['reportStatus']);
        $qb->andWhere('customer.id = :farmerId')->setParameter('farmerId', $filterBy['farmerId']);


        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
//            $month = ($result['details']['visitingDate'])->format('m-F-Y');

            $result['details']['feedTypeName'] = $result['feedTypeName'];
            $result['details']['feedMillName'] = $result['feedMillName'];

            $data[$result['lifeCycleId']]['details'][] = $result['details'];
            $data[$result['lifeCycleId']]['parent'] = [
                'customerName' => $result['customerName'],
                'customerAddress' => $result['customerAddress'],
                'customerMobile' => $result['customerMobile'],
//                'agentName' => $result['agentName'],
//                'agentAddress' => $result['agentAddress'],
                'lifeCycleId' => $result['lifeCycleId'],
                'lifeCycleState' => $result['lifeCycleState'],
                'totalBirds' => $result['totalBirds'],
                'hatchingDate' => $result['hatcheryDate'],
                'hatcheryName' => $result['hatcheryName'],
                'feedName' => $result['feedName'],
                'breedName' => $result['breedName'],
            ];
        }

        ksort($data);
//        dd($data);
        return $data;

    }
}
