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

        if (in_array('ROLE_CRM_POULTRY_ADMIN', $user->getRoles())){
            $qb->andWhere($qb->expr()->like('employee.roles', '%ROLE_CRM_POULTRY_USER%'));
        }
        if (in_array('ROLE_CRM_CATTLE_ADMIN', $user->getRoles())){
            $qb->andWhere($qb->expr()->like('employee.roles', '%ROLE_CRM_CATTLE_USER%'));
        }
        if (in_array('ROLE_CRM_AQUA_ADMIN', $user->getRoles())){
            $qb->andWhere($qb->expr()->like('employee.roles', '%ROLE_CRM_AQUA_USER%'));
        }
        if (!array_intersect(['ROLE_CRM_POULTRY_ADMIN','ROLE_CRM_CATTLE_ADMIN','ROLE_CRM_AQUA_ADMIN'], $user->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $user->getId());
        }

        return $qb->getQuery()->getResult();
    }

}
