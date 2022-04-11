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
use Terminalbd\KpiBundle\Entity\EmployeeBoard;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FarmerTrainingReportRepository extends BaseRepository
{
    public function getFarmerTrainingReport($breedSlug, $filterBy)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;
        $employeeId = (isset($filterBy['employee']) && $filterBy['employee'] != null) ? $filterBy['employee']->getId() : null;

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.farmerTrainingReport', 'farmer_training_report');
        $qb->join('e.customer', 'farmer');
        $qb->join('farmer_training_report.employee', 'employee');
        $qb->join('farmer_training_report.breedName', 'breed_name');
        $qb->join('farmer_training_report.agent', 'agent');

        $qb->select('e.farmerCapacity', 'e.trainingMaterialQty');
        $qb->addSelect('farmer.id AS farmerId', 'farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('agent.id AS agentId', 'agent.name AS agentName', 'agent.address AS agentAddress', 'agent.mobile AS agentMobile');
        $qb->addSelect('farmer_training_report.id AS trainingId','farmer_training_report.trainingTopics', 'farmer_training_report.remarks', 'farmer_training_report.trainingDate');

        $qb->where('breed_name.slug = :slug')->setParameter('slug', $breedSlug);
        $qb->andWhere('farmer_training_report.trainingDate >= :start')->setParameter('start', $start);
        $qb->andWhere('farmer_training_report.trainingDate <= :end')->setParameter('end', $end);

        if ($employeeId){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        $qb->orderBy('farmer_training_report.trainingDate', 'ASC');

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $month = $result['trainingDate']->format('Y-m-F');

            $data[$month][$result['trainingId']]['trainingDetails'] = [
                'id' => $result['trainingId'],
                'trainingTopics' => $result['trainingTopics'],
                'remarks' => $result['remarks'],
                'trainingDate' => $result['trainingDate'],
                'trainingMaterialQty' => $result['trainingMaterialQty'],
            ];
            $data[$month][$result['trainingId']][$result['agentId']]['agentDetails'] = [
                'id' => $result['agentId'],
                'name' => $result['agentName'],
                'address' => $result['agentAddress'],
                'mobile' => $result['agentMobile'],
            ];
            $data[$month][$result['trainingId']][$result['agentId']]['farmerDetails'][] = [
                "id" => $result['farmerId'],
                "name" => $result['farmerName'],
                "address" => $result['farmerAddress'],
                "mobile" => $result['farmerMobile'],
                "capacity" => $result['farmerCapacity'],
                "trainingId" => $result['trainingId'],
                "agentId" => $result['agentId'],
            ];
        }
        ksort($data);
        return $data;
    }

    public function getMonthlyfarmersTrainingProgramTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');

        $qb->join('e.employee', 'employee');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.trainingDate >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.trainingDate <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }


    public function getNumberOfReportsForKpi($board, $type)
    {
        /**
         * @var EmployeeBoard $board
         */
        $startDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-d');
        $endDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-t');

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.breedName', 'breed_name');

        $qb->where('e.employee = :employee')->setParameter('employee',$board->getEmployee());
        $qb->andWhere('e.trainingDate >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.trainingDate <= :endDate')->setParameter('endDate', $endDate);
        $qb->andWhere('breed_name.slug = :slug')->setParameter('slug', $type);

        return count($qb->getQuery()->getArrayResult());
    }

}
