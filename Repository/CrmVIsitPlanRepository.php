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
class CrmVIsitPlanRepository extends EntityRepository
{

    public function getMonthlyTourPlanByEmployeeAndDate($employeeId, $visitingDate=null, $type=null){
        $qb = $this->createQueryBuilder('e');
        $qb->select('e.id', 'e.visitingArea', 'e.visitDate', 'e.agentList', 'e.areaList', 'e.createdAt');
        $qb->addSelect('employee.id as employeeId');
        $qb->addSelect('workingMode.id as workingModeId', 'workingMode.name as workingModeName');
        $qb->join('e.employee','employee');
        $qb->leftJoin('e.workingMode','workingMode');
        if($visitingDate && $visitingDate!=''){
            if($type=='monthly'){
                $qb->andWhere("DATE_FORMAT(e.visitDate,'%Y-%m') =:yearMonth")->setParameter('yearMonth', date('Y-m', strtotime($visitingDate.'-01')));
            }else {
                $qb->andWhere("DATE_FORMAT(e.visitDate,'%Y-%m-%d') =:yearMonth")->setParameter('yearMonth', date('Y-m-d', strtotime($visitingDate)));
            }
        }
        $qb->andWhere('employee.id =:employeeId')->setParameter('employeeId', $employeeId);
        $qb->orderBy("DATE_FORMAT(e.visitDate,'%Y-%m')", "DESC");
        $qb->addOrderBy('e.visitDate', 'ASC');
        $results= $qb->getQuery()->getArrayResult();
        $returnArray=[];
        if($results){
            foreach ($results as $result) {
                $visitMonth=$result['visitDate']->format('Y-m');
                $visitDate=$result['visitDate']->format('Y-m-d');
                $returnArray['employee_id'] = $result['employeeId'];
                $returnArray['data'][$visitMonth][$visitDate]=[
                    'id' => $result['id'],
                    'visitingArea'=>$result['visitingArea'],
                    'agentList' => $result['agentList'],
                    'areaList' => $result['areaList'],
                    'createdDate' => $result['createdAt']->format('Y-m-d'),
                    'workingMode'=>[
                        'id'=>$result['workingModeId'],
                        'name'=>$result['workingModeName']
                    ]
                ];
            }
        }
        return $returnArray;
    }


}
