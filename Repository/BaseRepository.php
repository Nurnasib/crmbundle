<?php


namespace Terminalbd\CrmBundle\Repository;


use Doctrine\ORM\EntityRepository;

class BaseRepository extends EntityRepository
{
    protected function handleSearchFilterBetween($qb,$filterBy)
    {
        if (isset($filterBy)){

            $startDate = isset($filterBy['startDate'])? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00': '';
            $endDate = isset($filterBy['endDate'])? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59': '';

            $startDateCreated = isset($filterBy['startDateCreated'])? (new \DateTime($filterBy['startDateCreated']))->format('Y-m-d') . ' 00:00:00': '' ;
            $endDateCreated = isset($filterBy['endDateCreated'])? (new \DateTime($filterBy['endDateCreated']))->format('Y-m-d') . ' 23:59:59': '';

            $reportingMonth = isset($filterBy['reportingMonth'])? $filterBy['reportingMonth']: '';

            $slug = isset($filterBy['slug'])? $filterBy['slug']: '';
            $farmer = isset($filterBy['farmer'])? $filterBy['farmer']: '';
            $employee = isset($filterBy['employee'])? $filterBy['employee']: '';
            $feedType = isset($filterBy['feedType'])? $filterBy['feedType']: '';



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

            if (!empty($reportingMonth)){
                $qb->andWhere('e.reportingMonth >= :reportingMonthStart')->setParameter('reportingMonthStart', $reportingMonth . '-' . '01');
                $qb->andWhere('e.reportingMonth <= :reportingMonthEnd')->setParameter('reportingMonthEnd', date('Y-m-t', strtotime($reportingMonth)));
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
            if (!empty($feedType)){
                $qb->andWhere('e.fcrOfFeed = :feedType')->setParameter('feedType', $feedType);
            }

        }
    }
}