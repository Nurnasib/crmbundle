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

use App\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class CattlePerformanceRepository extends EntityRepository
{

    public function getCattlePerformanceReportByReportingDateAndFeedType($report, $employee)
    {
        if($report&&$employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('cp')
                ->where('cp.reportingMonth >= :startDate')
                ->andWhere('cp.reportingMonth <= :endDate')
                ->andWhere('cp.report = :report')
//                ->andWhere('cp.customer = :customer')
                ->andWhere('cp.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getResult();
        }
        return array();
    }

    public function getPerformanceReport($report, $filterBy, User $loggedUser)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;
        $employeeId = isset($filterBy['employeeId']) ?: null;

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->join('e.report', 'report');
        $qb->leftJoin('e.agent', 'agent');
        $qb->join('e.customer', 'farmer');
        $qb->leftJoin('e.breedType', 'breed_type');
        $qb->leftJoin('e.feedType', 'feed_type');

        $qb->select('e AS details');
        $qb->addSelect('employee.userId', 'employee.name', 'designation.name AS designationName');
        $qb->addSelect('agent.id AS agentId', 'agent.name AS agentName');
        $qb->addSelect('farmer.id AS farmerId', 'farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');
        $qb->addSelect('breed_type.id AS breedTypeId', 'breed_type.name AS breedTypeName');
        $qb->addSelect('feed_type.id AS feedTypeId', 'feed_type.name AS feedTypeName');

        $qb->where('e.visitingDate >= :start')->setParameter('start', $start);
        $qb->andWhere('e.visitingDate <= :end')->setParameter('end', $end);
        $qb->andWhere('report.slug = :reportSlug')->setParameter('reportSlug', $report->getSlug());

        $rolesString = implode($loggedUser->getRoles(), '_');

        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }
        if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        $results = $qb->getQuery()->getArrayResult();
        $data = [];

        foreach ($results as $result) {
            $result['details']['agentId'] = $result['agentId'];
            $result['details']['agentName'] = $result['agentName'];
            $result['details']['farmerId'] = $result['farmerId'];
            $result['details']['farmerName'] = $result['farmerName'];
            $result['details']['farmerAddress'] = $result['farmerAddress'];
            $result['details']['farmerMobile'] = $result['farmerMobile'];
            $result['details']['breedTypeId'] = $result['breedTypeId'];
            $result['details']['breedTypeName'] = $result['breedTypeName'];
            $result['details']['feedTypeId'] = $result['feedTypeId'];
            $result['details']['feedTypeName'] = $result['feedTypeName'];


            $month = $result['details']['visitingDate']->format('m-F');
            $year = $result['details']['visitingDate']->format('Y');
            $data[$year][$result['userId']]['employeeDetails'] = [
                'userId' => $result['userId'],
                'name' => $result['name'],
                'designation' => $result['designationName'],
            ];
            $data[$year][$result['userId']]['data'][$month][] = $result['details'];

            ksort($data);
            ksort($data[$year][$result['userId']]['data']);
        }

        return $data;
    }
}
