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
        $employeeId = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.complain', 'parent');
        $qb->join('e.ComplainParameter', 'parameter');
        $qb->join('parent.employee', 'employee');

        $qb->select('e.id','e.quantity');
        $qb->addSelect('parent.observation', 'parent.ageDays', 'parent.createdAt');
        $qb->addSelect('parameter.item AS parameterName');

        $rolesString = implode($loggedUser->getRoles(), '_');

        $qb->where('parameter.type = :type')->setParameter('type', $type);
        if ($employeeId){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }else{
            if (!str_contains($rolesString, 'ADMIN') && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
                $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
            }elseif (in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
                if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
                    $qb->andWhere('lineManager.id = :lineManagerId')->setParameter('lineManagerId', $loggedUser->getId());
                }
            }
        }
//        $qb->andWhere($qb->expr()->like('parent.createdAt', ':startD'))->setParameter('startD', '%'.(new \DateTime($filterBy['startDate']))->format('Y-m-d').'%');
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
