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
use Terminalbd\CrmBundle\Entity\AgentUpgradationReport;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class AgentUpgradationReportRepository extends BaseRepository
{
    public function getAgentUpgradationReportByCreatedDateEmployeeReport($purpose, $employee){
        if($purpose&&$employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('aur')
                ->where('aur.createdAt >= :startDate')
                ->andWhere('aur.createdAt <= :endDate')
                ->andWhere('aur.agentPurpose = :purpose')
                ->andWhere('aur.employee = :employee')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'purpose'=>$purpose, 'employee'=>$employee));

            return $query->getQuery()->getResult();
        }
        return array();
    }

    public function getAgentUpgradation($filterBy){
            $startDate = isset($filterBy['startDate']) ? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00' : null;
            $endDate = isset($filterBy['endDate']) ? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59' : null;

            $query = $this->createQueryBuilder('aur');
            $query->join('aur.agent','agent');
            $query->join('aur.employee','employee');
            $query->where('aur.createdAt IS NOT NULL');

            if($startDate&&$endDate){
                $query->andWhere('aur.createdAt >= :startDate')->setParameter('startDate',$startDate);
                $query->andWhere('aur.createdAt <= :endDate')->setParameter('endDate', $endDate);
            }

            if(isset($filterBy['employee'])&&$filterBy['employee']!=''){
                $query->andWhere('employee.id = :employee')->setParameter('employee',$filterBy['employee']);
            }

        $results=$query->getQuery()->getResult();
        $returnArray=[];
        if($results){
            /* @var AgentUpgradationReport $result*/
            foreach ($results as $result){
                $monthYear = $result->getCreatedAt()->format('F-Y');
                $returnArray[$result->getEmployee()->getId()]['name']=$result->getEmployee()->getName();
                $returnArray[$result->getEmployee()->getId()]['employeeDesignationName']=$result->getEmployee()->getDesignation()->getName();
                $returnArray[$result->getEmployee()->getId()]['details'][$monthYear][]=$result;
//                $returnArray[$result['employeeId']]['details'][$monthYear][]=$result;
            }
        }

        return $returnArray;

    }


}
