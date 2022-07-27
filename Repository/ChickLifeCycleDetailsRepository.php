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
use Terminalbd\KpiBundle\Entity\EmployeeBoard;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ChickLifeCycleDetailsRepository extends BaseRepository
{
    public function getChickLifeCycleDetails($lifeCycleSlug, $filterBy)
    {
        $data = [];
        $lifeCycleIdsMonthWise=$this->getChickLifeCycles($lifeCycleSlug, $filterBy);
        if(sizeof($lifeCycleIdsMonthWise)>0){
            foreach ($lifeCycleIdsMonthWise as $monthYear=>$lifeCycleIds) {
                foreach ($lifeCycleIds as $lifeCycleId=>$item){
                    $qb = $this->createQueryBuilder('e');
                    $qb->join('e.crmChickLifeCycle', 'crm_chick_life_cycle');
                    $qb->join('crm_chick_life_cycle.report', 'report');
                    $qb->join('crm_chick_life_cycle.agent', 'agent');
                    $qb->join('crm_chick_life_cycle.customer', 'customer');
                    $qb->leftJoin('crm_chick_life_cycle.hatchery', 'hatchery');
                    $qb->leftJoin('crm_chick_life_cycle.feed', 'feed');
                    $qb->leftJoin('crm_chick_life_cycle.breed', 'breed');
                    $qb->leftJoin('crm_chick_life_cycle.employee', 'employee');
                    $qb->leftJoin('e.feedType', 'feed_type');

                    $qb->select('e AS details');
                    $qb->addSelect('agent.name AS agentName','agent.address AS agentAddress');
                    $qb->addSelect('customer.id AS customerId','customer.name AS customerName','customer.address AS customerAddress','customer.mobile AS customerMobile');
                    $qb->addSelect('crm_chick_life_cycle.id AS lifeCycleId','crm_chick_life_cycle.lifeCycleState', 'crm_chick_life_cycle.totalBirds', 'crm_chick_life_cycle.hatchingDate');
                    $qb->addSelect('hatchery.name AS hatcheryName');
                    $qb->addSelect('feed.name AS feedName');
                    $qb->addSelect('breed.name AS breedName');
                    $qb->addSelect('feed_type.name AS feedTypeName');

                    $qb->where('report.slug = :slug')->setParameter('slug', $lifeCycleSlug);
//            $qb->andWhere('e.reportingDate >= :startDate')->setParameter('startDate', $startDate);
                    $qb->andWhere('e.visitingWeek <= :visitingWeek')->setParameter('visitingWeek', (int)$item['maxWeek']);
                    $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
                    $qb->andWhere('crm_chick_life_cycle.lifeCycleState = :reportStatus')->setParameter('reportStatus', $filterBy['reportStatus']);
                    if ($filterBy['farmerId']){
                        $qb->andWhere('customer.id = :farmerId')->setParameter('farmerId', $filterBy['farmerId']);
                    }
                    $qb->andWhere('crm_chick_life_cycle.id = :lifeCycleId')->setParameter('lifeCycleId', $lifeCycleId);

                    $results =  $qb->getQuery()->getArrayResult();
                    foreach ($results as $result) {
                        $result['details']['feedTypeName'] = $result['feedTypeName'];
                        $data[$monthYear][$lifeCycleId]['details'][] = $result['details'];
                        $data[$monthYear][$lifeCycleId]['parent'] = [
                            'customerName' => $result['customerName'],
                            'customerAddress' => $result['customerAddress'],
                            'customerMobile' => $result['customerMobile'],
                            'agentName' => $result['agentName'],
                            'agentAddress' => $result['agentAddress'],
                            'lifeCycleId' => $result['lifeCycleId'],
                            'lifeCycleState' => $result['lifeCycleState'],
                            'totalBirds' => $result['totalBirds'],
                            'hatchingDate' => $result['hatchingDate'],
                            'hatcheryName' => $result['hatcheryName'],
                            'feedName' => $result['feedName'],
                            'breedName' => $result['breedName'],
                        ];
                    }
//                    ksort($data);
                }

            }

        }

        return $data;
    }

    private function getChickLifeCycles($lifeCycleSlug, $filterBy){
        $startDate = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00' : null;
        $endDate = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59' : null;

        $months = $this->getMonthRanges($startDate, $endDate);
        $returnArray=[];
        if(sizeof($months)>0){
            foreach ($months as $month){
                $fromDate= $month['start'];
                $toDate= $month['end'];
                $qb = $this->createQueryBuilder('e');
                $qb->join('e.crmChickLifeCycle', 'crm_chick_life_cycle');
                $qb->join('crm_chick_life_cycle.report', 'report');
                $qb->join('crm_chick_life_cycle.customer', 'customer');
                $qb->leftJoin('crm_chick_life_cycle.employee', 'employee');
                $qb->select('crm_chick_life_cycle.id');
                $qb->addSelect('MAX(e.visitingWeek) as maxWeek');
                $qb->where('report.slug = :slug')->setParameter('slug', $lifeCycleSlug);
                $qb->andWhere('e.reportingDate >= :startDate')->setParameter('startDate', $fromDate);
                $qb->andWhere('e.reportingDate <= :endDate')->setParameter('endDate', $toDate);
                $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
                $qb->andWhere('crm_chick_life_cycle.lifeCycleState = :reportStatus')->setParameter('reportStatus', $filterBy['reportStatus']);
                if ($filterBy['farmerId']){
                    $qb->andWhere('customer.id = :farmerId')->setParameter('farmerId', $filterBy['farmerId']);
                }
                $qb->groupBy('crm_chick_life_cycle.id');

                $results =  $qb->getQuery()->getArrayResult();

                $monthYear= date('F-Y', strtotime($toDate));

                if($results){
                    foreach ($results as $result) {
                        $returnArray[$monthYear][$result['id']]=$result;
                    }
                }
            }
        }

        return $returnArray;
    }

   private function getMonthRanges($start, $end) {
        $timeStart = strtotime($start);
        $timeEnd   = strtotime($end);
        $out       = [];

        $milestones[] = $timeStart;
        $timeEndMonth = strtotime('first day of next month midnight', $timeStart);
        while ($timeEndMonth < $timeEnd) {
            $milestones[] = $timeEndMonth;
            $timeEndMonth = strtotime('+1 month', $timeEndMonth);
        }
        $milestones[] = $timeEnd;

        $count = count($milestones);
        for ($i = 1; $i < $count; $i++) {
            $out[] = [
                'start' => date('Y-m-d H:i:s', $milestones[$i-1]), // Here you can apply your formatting (like "date('Y-m-d H:i:s', $milestones[$i-1])") if you don't won't want just timestamp
                'end'   => date('Y-m-d H:i:s', ($milestones[$i] - 1))
            ];
        }

        return $out;
    }

    public function getNumberOfReportsForKpi($board)
    {
        /**
         * @var EmployeeBoard $board
         */
        $startDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-d');
        $endDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-t');

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.crmChickLifeCycle', 'crm_chick_life_cycle');

        $qb->where('crm_chick_life_cycle.employee = :employee')->setParameter('employee',$board->getEmployee());
        $qb->andWhere('e.reportingDate >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.reportingDate <= :endDate')->setParameter('endDate', $endDate);

        return count($qb->getQuery()->getArrayResult());
    }
}
