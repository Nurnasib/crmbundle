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
use App\Entity\Admin\Location;
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
class CrmVisitRepository extends EntityRepository
{
    public function findDailyReport($filterBy)
    {
//        dd($filterBy);
//        die();
        $employeeId = $filterBy['employeeId'];
        $startDate = $filterBy['startDate'];
        $endDate = $filterBy['endDate'];

        $qb = $this->createQueryBuilder('e');

        $qb->select('e.created', 'e.workingDuration','e.workingDurationTo');
        $qb->addSelect('employee.name AS employee_name', 'employee.id AS employee_id');
        $qb->addSelect('location.name AS location_name');
        $qb->addSelect('crm_visit_details.farmCapacity AS customer_farmCapacity','crm_visit_details.comments', 'crm_visit_details.process');
        $qb->addSelect('purpose.name AS purpose_name');
        $qb->addSelect('crm_customer.name AS customer_name', 'crm_customer.address AS customer_address');
        $qb->addSelect('agent.name AS agent_name', 'agent.address AS agent_address','agent.mobile AS agent_phone');

        $qb->leftJoin('e.employee', 'employee');
        $qb->leftJoin('e.location', 'location');
        $qb->leftJoin('e.crmVisitDetails', 'crm_visit_details');
        $qb->leftJoin('crm_visit_details.crmCustomer', 'crm_customer');
        $qb->leftJoin('crm_visit_details.purpose', 'purpose');
        $qb->leftJoin('crm_visit_details.agent', 'agent');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        $qb->andwhere('crm_visit_details.created >= :startDate')->setParameter('startDate', $startDate);
        $qb->andwhere('crm_visit_details.created <= :endDate')->setParameter('endDate', $endDate);

        return $qb->getQuery()->getArrayResult();
    }

    public function insertDataFromApi(array $data)
    {
        $created = new \DateTime($data['created_at']);
        $employee = $this->getEntityManager()->getRepository(User::class)->find($data['employee_id']);
        $location = $this->getEntityManager()->getRepository(Location::class)->find($data['location_id']);

        if ($employee && $location){
            $sql = "INSERT INTO `crm_visit`(`created`, `working_duration`, `employee_id`, `location_id`, `working_duration_to`, `app_id`) VALUES (:created_at, :duration_from, :employee_id, :location_id, :duration_to, :app_id)";
            $em = $this->_em;
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue('created_at', $created->format('Y-m-d H:i:s'));
            $stmt->bindValue('duration_from', $data['duration_from']);
            $stmt->bindValue('employee_id', $data['employee_id']);
            $stmt->bindValue('location_id', $data['location_id']);
            $stmt->bindValue('duration_to', $data['duration_to']);
            $stmt->bindValue('app_id', $data['id']);
            $stmt->execute();
        }
    }
}