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

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.complain', 'parent');
        $qb->join('e.ComplainParameter', 'parameter');
        $qb->join('parent.employee', 'employee');

        $qb->select('e.id','e.quantity');
        $qb->addSelect('parent.observation', 'parent.ageDays', 'parent.createdAt');
        $qb->addSelect('parameter.item AS parameterName');


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
        if (isset($filterBy['employeeId']) && $filterBy['employeeId'] !=''){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        $qb->andWhere('parent.createdAt >= :start')->setParameter('start', $start);
        $qb->andWhere('parent.createdAt <= :end')->setParameter('end', $end);

        $results = $qb->getQuery()->getArrayResult();

        $data = [];
        foreach ($results as $result) {
            $monthYear = $result['createdAt']->format('Y-m-F');
            $data[$monthYear][] = $result;
        }

        ksort($data);
        return $data;

    }
}
