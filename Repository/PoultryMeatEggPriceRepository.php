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
class PoultryMeatEggPriceRepository extends EntityRepository
{
    public function processPrice($regions, $breedTypes, $employee)
    {

        foreach ($breedTypes as $type){
            $breedTypeId = $type->getId();

            foreach ($regions as $region ){
                $regionId = $region->getId();
                $exist = $this->checkExistRecord($regionId, $breedTypeId, $employee);
                if(!$exist){
                    $sql = "INSERT INTO `crm_poultry_meat_egg_price`(`region_id`, `status`, `created_at`, `breed_type_id`, `price`, `employee_id`, `reporting_date`) VALUES (:regionId , :status, :createdAt, :breedTypeId , :price,  :employeeId, :reportingDate)";

                    $qb = $this->_em->getConnection()->prepare($sql);
                    $qb->bindValue('createdAt', (new \DateTime("now"))->format('Y-m-d H:s:i'));
                    $qb->bindValue('reportingDate', (new \DateTime("now"))->format('Y-m-d'));
                    $qb->bindValue('status', 1);
                    $qb->bindValue('price', 0);
                    $qb->bindValue('regionId', $regionId);
                    $qb->bindValue('breedTypeId', $breedTypeId);
                    $qb->bindValue('employeeId', $employee->getId());
                    $qb->execute();
                }
            }
        }


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.region','region');
        $qb->join('e.breedType','breedType');
        $qb->select('e.price','e.id AS recordId');
        $qb->addSelect('region.id AS regionId');
        $qb->addSelect('breedType.id AS breedTypeId');

        $qb->where('e.employee = :employee')->setParameter('employee', $employee);
        $qb->andWhere('e.reportingDate = :reportingDate')->setParameter('reportingDate', (new \DateTime("now"))->format('Y-m-d'));

        $results = $qb->getQuery()->getArrayResult();
        $array = [];
        if($results){
            foreach ($results as $result):
                $key = "{$result['regionId']}-{$result['breedTypeId']}";
                $array[$key] = $result;
            endforeach;
        }
        return $array;
    }

    private function checkExistRecord($regionId, $breedTypeId, $employee){
        $qb = $this->createQueryBuilder('e');
        $qb->join('e.region','region');
        $qb->join('e.breedType','breedType');
        $qb->select('e.id AS recordId');
        $qb->where('e.employee = :employee')->setParameter('employee', $employee);
        $qb->andWhere('region.id = :regionId')->setParameter('regionId', $regionId);
        $qb->andWhere('breedType.id = :breedTypeId')->setParameter('breedTypeId', $breedTypeId);
        $qb->andWhere('e.reportingDate = :reportingDate')->setParameter('reportingDate', (new \DateTime("now"))->format('Y-m-d'));


        return $qb->getQuery()->getResult();
    }

    public function getMeatEggPriceReport($filterBy, User $loggedUser)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') : null;
        $employeeId = isset($filterBy['employee']) ? $filterBy['employee']->getId() : null;


        $qb = $this->createQueryBuilder('e');
        $qb->join('e.breedType', 'breed_type');
        $qb->join('e.employee', 'employee');
        $qb->join('employee.userGroup', 'user_group');


        $qb->select('AVG(e.price) AS avgPrice', 'MONTH(e.reportingDate) AS month', 'YEAR(e.reportingDate) AS year');
        $qb->addSelect('breed_type.id AS breedTypeId', 'breed_type.name AS breedTypeName');
        $qb->addSelect('employee.userId', 'employee.name');

        $qb->where('e.reportingDate >= :start')->setParameter('start', $start);
        $qb->andWhere('e.reportingDate <= :end')->setParameter('end', $end);
        $qb->andWhere('user_group.slug = :userGroupSlug')->setParameter('userGroupSlug', 'employee');

        $qb->groupBy('month');
        $qb->addGroupBy('year');

        $roleSplitArray = [];
        foreach ($loggedUser->getRoles() as $role) {
            $roleSplitArray = array_merge(explode('_', $role), $roleSplitArray);
        }
        if (!in_array('ADMIN', $roleSplitArray) && !in_array('ROLE_LINE_MANAGER', $loggedUser->getRoles())){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $loggedUser->getId());
        }
        if (isset($filterBy['employee']) && $filterBy['employee'] !== null){
            $qb->andWhere('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        }

        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result) {
            $month = $result['reportingDate']->format('m-F');
            $data['Year-' . $result['reportingDate']->format('Y')][$result['breedTypeName']][$result['userId'] . '~' . $result['name']][$month] = $result['price'];
        }
        dd($results);
        return $data;



    }

}