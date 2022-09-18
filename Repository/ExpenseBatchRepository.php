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
use Terminalbd\CrmBundle\Entity\DmsFile;
use Terminalbd\CrmBundle\Entity\Expense;
use function Doctrine\ORM\QueryBuilder;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ExpenseBatchRepository extends EntityRepository
{

    public function getExpenseBatches(User $loggedUser){

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.employee', 'employee');

        $roleSplitArray = [];

        foreach ($loggedUser->getRoles() as $role) {
            $roleSplitArray = array_merge(explode('_', $role), $roleSplitArray);
        }

        if (in_array('ADMIN', $roleSplitArray)) {
            $userRole = [];
            if (in_array('ROLE_CRM_POULTRY_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_POULTRY_USER');
            }
            if (in_array('ROLE_CRM_CATTLE_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_CATTLE_USER');
            }
            if (in_array('ROLE_CRM_AQUA_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_AQUA_USER');
            }
            if (in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $loggedUser->getRoles())) {
                array_push($userRole, 'ROLE_CRM_SALES_MARKETING_USER');
            }
            $query = '';
            if($userRole){

                foreach ($userRole as $key => $role) {
                    if ($key !== 0) {
                        $query .= " OR ";
                    }
                    $query .= "employee.roles LIKE '%" . $role . "%'";

                }
                $qb->andWhere($query);
            }

        } elseif (!in_array('ADMIN', $roleSplitArray) && in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())) {
            $employeeIdsByLineManager = $this->_em->getRepository(User::class)->getEmployeesByLineManager($loggedUser);
            $employeeIs=[];
            if($employeeIdsByLineManager){
                $employeeIs=$employeeIdsByLineManager;
            }
//            dd($employeeIs);
            $qb->andWhere('employee.id IN (:employeeIds)')->setParameter('employeeIds', $employeeIs);
        }

        $results = $qb->getQuery()->getResult();
//        dd($results);
        return $results;
    }


}
