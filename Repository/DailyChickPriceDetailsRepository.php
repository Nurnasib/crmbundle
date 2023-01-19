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
use Doctrine\ORM\NonUniqueResultException;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class DailyChickPriceDetailsRepository extends EntityRepository
{
    public function getDailyDocPriceReport($filterBy, User $loggedUser)
    {

        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : date('Y-m-d');
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : date('Y-m-d');
        $employeeId = isset($filterBy['employeeId']) ? $filterBy['employeeId'] : null;
        $poultryFramType = isset($filterBy['poultryFramType']) ? $filterBy['poultryFramType'] : '';

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmDailyChickPrice', 'parent');
        $qb->join('parent.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->leftJoin('employee.regional', 'regional');
        $qb->join('employee.userGroup', 'user_group');
        $qb->join('e.chickType', 'chick_type');
        $qb->join('chick_type.parent', 'chick_type_parent');
        $qb->join('e.feed', 'feed');

        $qb->select('employee.id','employee.userId', 'employee.name');
        $qb->addSelect('e.price');
        $qb->addSelect('chick_type_parent.id AS chickTypeParentId', 'chick_type_parent.name AS chickTypeParentName');
        $qb->addSelect('feed.id AS feedId', 'feed.name AS feedName');
        $qb->addSelect('parent.reportingDate', 'MONTH(parent.reportingDate) AS month', 'YEAR(parent.reportingDate) AS year');
        $qb->addSelect('designation.name as designationName');
        $qb->addSelect('regional.name as regionalName');

        $qb->where('parent.reportingDate >= :start')->setParameter('start', $start);
        $qb->andWhere('parent.reportingDate <= :end')->setParameter('end', $end);
        $qb->andWhere('user_group.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');
        $qb->andWhere('e.price > :price')->setParameter('price', 0);

        $qb->orderBy('feed.sortOrder', 'ASC');
        $qb->addOrderBy('parent.reportingDate', 'ASC');

        $rolesString = implode('_', $loggedUser->getRoles());
        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }elseif (!str_contains($rolesString, 'ADMIN') && in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){

            $employeeIdsByLineManager = $this->_em->getRepository(User::class)->getEmployeesByLineManager($loggedUser);
            $employeeIs=[];
            if($employeeIdsByLineManager){
                $employeeIs=$employeeIdsByLineManager;
            }
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIs);
        }
        if (isset($filterBy['employeeId']) && $filterBy['employeeId'] !=''){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        if($poultryFramType){
           $qb->andWhere('chick_type_parent.id =:docType')->setParameter('docType', $poultryFramType);
        }

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $reportingDate = $result['reportingDate']->format('d-m-Y');
//            $data[$result['chickTypeParentName']][$result['userId'] . '~' . $result['name']][$result['feedName']][$reportingDate] = $result['price'];
            $data['records'][$result['chickTypeParentName']][$result['userId']][$result['feedId']][$reportingDate] = $result['price'];
            $data['feedCompany'][$result['chickTypeParentName']][$result['userId']][$result['feedId']] = $result['feedName'];
            $data['employeeInfo'][$result['chickTypeParentName']][$result['userId']] = ['employeeId'=>$result['userId'], 'employeeName'=>$result['name'], 'designationName'=>$result['designationName'], 'regionalName'=>$result['regionalName']];

        }
        $data['dateRange']=$this->getBetweenDates($start, $end);
//        dd($data);
        return $data;

    }

    private function getBetweenDates($startDate, $endDate)
    {
        $rangArray = [];

        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        for ($currentDate = $startDate; $currentDate <= $endDate;
             $currentDate += (86400)) {

            $date = date('d-m-Y', $currentDate);
            $rangArray[] = $date;
        }

        return $rangArray;
    }

    public function getDocPriceReport($filterBy, User $loggedUser)
    {

        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : date('Y-01-01');
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : date('Y-12-31');
        $employeeId = isset($filterBy['employeeId']) ? $filterBy['employeeId'] : null;

//        dd($start, $end);
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmDailyChickPrice', 'parent');
        $qb->join('parent.employee', 'employee');
        $qb->join('employee.userGroup', 'user_group');
        $qb->join('e.chickType', 'chick_type');
        $qb->join('chick_type.parent', 'chick_type_parent');
        $qb->join('e.feed', 'feed');

        $qb->select('employee.id','employee.userId', 'employee.name');
        $qb->addSelect('AVG(e.price) AS avgPrice');
        $qb->addSelect('chick_type_parent.id AS chickTypeParentId', 'chick_type_parent.name AS chickTypeParentName');
        $qb->addSelect('feed.id AS feedId', 'feed.name AS feedName');
        $qb->addSelect('parent.reportingDate', 'MONTH(parent.reportingDate) AS month', 'YEAR(parent.reportingDate) AS year');

        $qb->where('parent.reportingDate >= :start')->setParameter('start', $start);
        $qb->andWhere('parent.reportingDate <= :end')->setParameter('end', $end);
        $qb->andWhere('user_group.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');

        $qb->groupBy('employee.userId');
        $qb->addGroupBy('month');
        $qb->addGroupBy('year');
        $qb->addGroupBy('chickTypeParentId');
        $qb->addGroupBy('feedId');
        $qb->orderBy('feed.name', 'ASC');

        $rolesString = implode('_', $loggedUser->getRoles());
        if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }elseif (!str_contains($rolesString, 'ADMIN') && in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){

            $employeeIdsByLineManager = $this->_em->getRepository(User::class)->getEmployeesByLineManager($loggedUser);
            $employeeIs=[];
            if($employeeIdsByLineManager){
                $employeeIs=$employeeIdsByLineManager;
            }
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIs);
        }
        if (isset($filterBy['employeeId']) && $filterBy['employeeId'] !=''){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $month = $result['reportingDate']->format('m-F');
            $data['Year-' . $result['reportingDate']->format('Y')][$result['chickTypeParentName']][$result['userId'] . '~' . $result['name']][$result['feedName']][$month] = $result['avgPrice'];

            ksort($data['Year-' . $result['reportingDate']->format('Y')][$result['chickTypeParentName']][$result['userId'] . '~' . $result['name']][$result['feedName']]);
        }
        return $data;

    }

}





























