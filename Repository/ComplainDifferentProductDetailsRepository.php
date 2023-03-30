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
use App\Entity\User;


/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ComplainDifferentProductDetailsRepository extends EntityRepository
{
    public function getComplainReport($filterBy, User $loggedUser, $type)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d 00:00:00') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d 23:59:59') : null;
        $employeeId=isset($filterBy['employeeId']) && $filterBy['employeeId'] !=''?$filterBy['employeeId']:null;

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.complain', 'parent');
        $qb->join('e.ComplainParameter', 'parameter');
        $qb->join('parent.employee', 'employee');
        $qb->leftJoin('employee.designation', 'designation');
        $qb->leftJoin('parent.hatchery', 'hatchery');
        $qb->leftJoin('parent.agent', 'agent');
        $qb->leftJoin('agent.agentGroup', 'agentGroup');
        $qb->leftJoin('agent.district', 'district');
        $qb->leftJoin('parent.breed', 'breed');
        $qb->leftJoin('breed.parent','breedParent');
        $qb->leftJoin('parent.feedMill', 'feedMill');
        $qb->leftJoin('parent.transport', 'transport');
        $qb->leftJoin('parent.productName', 'productName');
        $qb->leftJoin('parent.feed', 'feed');
        $qb->leftJoin('feed.parent', 'feedParent');

        $qb->select('e.id','e.quantity','e.day');
        $qb->addSelect('parent.observation', 'parent.ageDays', 'parent.createdAt as createdAt', 'parent.hatchingDate as hatchingDate', 'parent.productionDate', 'parent.complains', 'parent.diseases', 'parent.receivedDocQty');
        $qb->addSelect('parent.id as parentId','parent.boxNo', 'parent.batchNo');
        $qb->addSelect('hatchery.name as hatcheryName');
        $qb->addSelect('agent.name as agentName', 'agent.agentId', 'agent.address');
        $qb->addSelect('agentGroup.name as agentGroupName', 'agentGroup.slug as agentGroupSlug');
        $qb->addSelect('district.name as agentDistrictName');
        $qb->addSelect('breed.name as breedName');
        $qb->addSelect('breedParent.name as breedParentName');
        $qb->addSelect('feedMill.name as feedMillName');
        $qb->addSelect('transport.name as transportName');
        $qb->addSelect('productName.name as productTypeName');
        $qb->addSelect('parameter.item AS parameterName');
        $qb->addSelect('employee.name as employeeName', 'employee.id as employeeAutoId', 'employee.userId as userId');
        $qb->addSelect('designation.name as designationName');
        $qb->addSelect('feed.name as feedName');
        $qb->addSelect('feedParent.name as feedParentName');


        $qb->where('parameter.type = :type')->setParameter('type', $type);

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
        if ($employeeId){
            $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);
        }

        $qb->andWhere('parent.createdAt >= :start')->setParameter('start', $start);
        $qb->andWhere('parent.createdAt <= :end')->setParameter('end', $end);

        if($type=='COMPLAIN_DOC'){
            $qb->andWhere('e.quantity > 0 OR e.day > 0');
        }

        $qb->orderBy("DATE_FORMAT(parent.createdAt,'%Y-%m')", "ASC");
        $qb->addOrderBy("parent.createdAt", "ASC");

        $results = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($results as $result) {
            $monthYear = $result['createdAt']->format('F-Y');
            $reportingDate = $result['createdAt']->format('d-m-Y');
            $data['records'][$monthYear][$reportingDate][$result['employeeAutoId']][$result['parentId']][] = $result;
            $data['dateWiseLength'][$monthYear][$reportingDate][] = $result['id'];
            $data['employeeWiseLength'][$monthYear][$reportingDate][$result['employeeAutoId']][] = $result['id'];
        }
        

        return $data;

    }
}
