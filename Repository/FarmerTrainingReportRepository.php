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
class FarmerTrainingReportRepository extends BaseRepository
{
    public function getFarmerTrainingReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.trainingTopics','e.trainingDate', 'e.remarks', 'e.trainingMaterial');
        $qb->addSelect('agent.name AS agentName','agent.address AS agentAddress', 'agent.mobile AS agentMobile');
        $qb->addSelect('farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('farmerTrainingReportDetails.farmerCapacity','farmerTrainingReportDetails.trainingMaterialQty');

        $qb->where('breedName.slug = :breedTypeSlug')->setParameter('breedTypeSlug', $filterBy['breedTypeSlug']);
        $qb->andWhere('e.trainingDate >= :bOfYear')->setParameter('bOfYear', $filterBy['bOfYear']);
        $qb->andWhere('e.trainingDate <= :eOfYear')->setParameter('eOfYear', $filterBy['eOfYear']);

        $qb->leftJoin('e.agent', 'agent');
        $qb->leftJoin('e.farmerTrainingReportDetails','farmerTrainingReportDetails');
        $qb->leftJoin('farmerTrainingReportDetails.customer','farmer');
        $qb->leftJoin('e.breedName', 'breedName');
        $this->handleSearchFilterBetween($qb, $filterBy);


        /** @var TYPE_NAME $results */
        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result) {
            $month = $result['trainingDate']->format('F-Y');

            $data[$month][] = $result;
        }

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

}
