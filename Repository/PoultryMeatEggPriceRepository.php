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

}