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
use function Doctrine\ORM\QueryBuilder;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class ExpenseRepository extends EntityRepository
{
    public function getExpense(User $user)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.crmVisit', 'crm_visit');
        $qb->join('crm_visit.employee', 'employee');

        $qb->select('e');

        $loggedUserRoles = implode($user->getRoles(), '_');
        if (!str_contains($loggedUserRoles, 'ADMIN')){
            $qb->andWhere('employee.id = :id')->setParameter('id', $user->getId());
        }else{
            $userRole = [];
            if (in_array('ROLE_CRM_POULTRY_ADMIN', $user->getRoles())){
                array_push($userRole, 'ROLE_CRM_POULTRY_USER');
            }
            if (in_array('ROLE_CRM_CATTLE_ADMIN', $user->getRoles())){
                array_push($userRole, 'ROLE_CRM_CATTLE_USER');
            }
            if (in_array('ROLE_CRM_AQUA_ADMIN', $user->getRoles())){
                array_push($userRole, 'ROLE_CRM_AQUA_USER');
            }
            if (in_array('ROLE_CRM_SALES_MARKETING_ADMIN', $user->getRoles())){
                array_push($userRole, 'ROLE_CRM_SALES_MARKETING_USER');
            }
            $query = '';
            foreach ($userRole as $key => $role) {
                if ($key !== 0){
                    $query .= " OR ";
                }
                $query .= "employee.roles LIKE '%" . $role . "%'";

            }
            $qb->andWhere($query);
        }

        return $qb->getQuery()->getResult();
    }

}
