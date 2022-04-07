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
use Terminalbd\CrmBundle\Entity\FcrDifferentCompanies;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FcrDifferentCompaniesRepository extends EntityRepository
{
    public function getExitingCheckFcrDifferentCompanyByCreatedDateEmployeeAndCompany($employee, $company, $breed_name)
    {
        if($company&&$employee){
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $query = $this->createQueryBuilder('fdc')
                ->select('fdc.id')
                ->where('fdc.createdAt >= :startDate')
                ->andWhere('fdc.createdAt <= :endDate')
                ->andWhere('fdc.employee = :employee')
                ->andWhere('fdc.hatchery = :company')
                ->andWhere('fdc.breedName = :breed_name')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'company'=>$company, 'employee'=>$employee, 'breed_name'=>$breed_name));
            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getFcrDifferentCompanyByCreatedDateAndEmployee($employee, $breed_name)
    {
        if($employee){
            $startDate = date('Y-01-01');
//            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $query = $this->createQueryBuilder('fdc')
                ->where('fdc.createdAt >= :startDate')
                ->andWhere('fdc.createdAt <= :endDate')
                ->andWhere('fdc.employee = :employee')
                ->andWhere('fdc.breedName = :breed_name')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'employee'=>$employee, 'breed_name'=>$breed_name));
            $returnArray = [];

            /* @var FcrDifferentCompanies $value*/
            foreach ($query->getQuery()->getResult() as $value){
                $returnArray[$value->getHatchery()->getId()]=array(
                    'january'=>['id'=>$value->getId(),'value'=>$value->getJanuary()],
                    'february'=>['id'=>$value->getId(),'value'=>$value->getFebruary()],
                    'march'=>['id'=>$value->getId(),'value'=>$value->getMarch()],
                    'april'=>['id'=>$value->getId(),'value'=>$value->getApril()],
                    'may'=>['id'=>$value->getId(),'value'=>$value->getMay()],
                    'june'=>['id'=>$value->getId(),'value'=>$value->getJune()],
                    'july'=>['id'=>$value->getId(),'value'=>$value->getJuly()],
                    'august'=>['id'=>$value->getId(),'value'=>$value->getAugust()],
                    'september'=>['id'=>$value->getId(),'value'=>$value->getSeptember()],
                    'october'=>['id'=>$value->getId(),'value'=>$value->getOctober()],
                    'november'=>['id'=>$value->getId(),'value'=>$value->getNovember()],
                    'december'=>['id'=>$value->getId(),'value'=>$value->getDecember()],
                );
            }
            return $returnArray;
        }
        return array();
    }

    public function getFcrDifferentCompaniesReport($filterBy)
    {
        $start = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00' : null;
        $end = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59' : null;

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.hatchery', 'hatchery');

        $qb->select('e AS details');
        $qb->addSelect('hatchery.id AS companyId', 'hatchery.name AS companyName');

        $qb->where('e.employee = :employee')->setParameter('employee', $filterBy['employee']);
        $qb->andWhere('e.createdAt >= :start')->setParameter('start', $start);
        $qb->andWhere('e.createdAt <= :end')->setParameter('end', $end);

        $results = $qb->getQuery()->getArrayResult();

        $data = [];

        foreach ($results as $result) {
            $month = $result['details']['createdAt']->format('Y-m-F');

            $result['details']['companyId'] = $result['companyId'];
            $result['details']['companyName'] = $result['companyName'];
            $data[$month][] = $result['details'];
        }
        ksort($data);
        return $data;
    }

    public function getExists($employeeId, $hatcheryId, $breedName, $createdAt)
    {
        $startDate = (new \DateTime($createdAt))->format('Y-01-01 00:00:00');
        $endDate = (new \DateTime($createdAt))->format('Y-12-31 23:59:59');

        $qb = $this->createQueryBuilder('e');

        $qb->join('e.employee', 'employee');
        $qb->join('e.hatchery', 'hatchery');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $employeeId);
        $qb->andWhere('hatchery.id = :hatcheryId')->setParameter('hatcheryId', $hatcheryId);
        $qb->andWhere('e.breedName = :breedName')->setParameter('breedName', strtolower($breedName));
        $qb->andWhere('e.createdAt >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.createdAt <= :endDate')->setParameter('endDate', $endDate);

        return $qb->getQuery()->getOneOrNullResult();

    }

}
