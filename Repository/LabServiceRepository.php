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
use Terminalbd\CrmBundle\Entity\LabService;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class LabServiceRepository extends EntityRepository
{
    public function getExitingCheckLabServiceByCreatedDateEmployeeAndCompany($employee, $lab, $labService, $breed_name)
    {
        if($lab&&$labService&&$employee){
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $query = $this->createQueryBuilder('ls')
                ->select('ls.id')
                ->where('ls.createdAt >= :startDate')
                ->andWhere('ls.createdAt <= :endDate')
                ->andWhere('ls.employee = :employee')
                ->andWhere('ls.lab = :lab')
                ->andWhere('ls.service = :labService')
                ->andWhere('ls.breedName = :breed_name')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'lab'=>$lab, 'labService'=>$labService, 'employee'=>$employee, 'breed_name'=>$breed_name));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }

    public function getLabServiceByCreatedDateAndEmployee($employee, $breed_name)
    {
        if($employee){
            $startDate = date('Y-01-01');
//            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $query = $this->createQueryBuilder('ls')
                ->where('ls.createdAt >= :startDate')
                ->andWhere('ls.createdAt <= :endDate')
                ->andWhere('ls.employee = :employee')
                ->andWhere('ls.breedName = :breed_name')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'employee'=>$employee, 'breed_name'=>$breed_name));
            $returnArray = [];

            /* @var LabService $value*/
            foreach ($query->getQuery()->getResult() as $value){
                $returnArray[$value->getLab()->getId()][$value->getService()->getId()]=array(
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
//            dd($returnArray);
            return $returnArray;
        }
        return array();
    }

}
