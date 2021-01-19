<?php


namespace Terminalbd\CrmBundle\Repository;


use Doctrine\ORM\EntityRepository;

class BaseRepository extends EntityRepository
{
    protected function handleSearchFilterBetween($qb,$filterBy)
    {
        if (isset($filterBy)){
            $startDate = isset($filterBy['startDate'])? $filterBy['startDate']: '';
            $endDate = isset($filterBy['endDate'])? $filterBy['endDate']: '';

            $startDateCreated = isset($filterBy['startDateCreated'])? $filterBy['startDateCreated']: '';
            $endDateCreated = isset($filterBy['endDateCreated'])? $filterBy['endDateCreated']: '';

            $slug = isset($filterBy['slug'])? $filterBy['slug']: '';
            $farmer = isset($filterBy['farmer'])? $filterBy['farmer']: '';
            $employee = isset($filterBy['employee'])? $filterBy['employee']: '';



//            if (!empty($startDate)){
//                $qb->andWhere($qb->expr()->orX(
//                    $qb->expr()->gte('e.createdAt', ':startDate'),
//                    $qb->expr()->gte('e.created', ':startDate')
//                ))->setParameter('startDate', $startDate);
//            }


            if (!empty($startDate)){
                $qb->andWhere('e.createdAt >= :startDate')->setParameter('startDate', $startDate);
            }
            if (!empty($endDate)){
                $qb->andWhere('e.createdAt <= :endDate')->setParameter('endDate', $endDate);
            }


            if (!empty($startDateCreated)){
                $qb->andWhere('e.created >= :startDate')->setParameter('startDate', $startDateCreated);
            }
            if (!empty($endDateCreated)){
                $qb->andWhere('e.created <= :endDate')->setParameter('endDate', $endDateCreated);
            }


            if (!empty($slug)){
                $qb->join('e.report','report');
                $qb->andWhere('report.slug = :slug')->setParameter('slug', $slug);
            }
            if (!empty($farmer)){
                $qb->leftJoin('e.customer','farmer');
                $qb->andWhere('farmer.id = :farmer')->setParameter('farmer', $farmer->getId());
            }
            if (!empty($employee)){
                $qb->leftJoin('e.employee','employee');
                $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee->getId());
            }

        }
    }
}