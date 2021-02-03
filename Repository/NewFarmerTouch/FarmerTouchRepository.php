<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Repository\NewFarmerTouch;

use Doctrine\ORM\EntityRepository;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Repository\BaseRepository;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FarmerTouchRepository extends BaseRepository
{

    public function getFishFarmerTouchReportByDateAndEmployeeAndReport($report, $employee)
    {
        if($report&&$employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('fft')
                ->where('fft.createdAt >= :startDate')
                ->andWhere('fft.createdAt <= :endDate')
                ->andWhere('fft.reportParentParent = :report')
                ->andWhere('fft.employee = :employee')
                ->setParameters(array('startDate'=>$startDate.' 00:00:00', 'endDate'=>$endDate.' 23:59:59', 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getResult();
        }
        return array();
    }

    public function getFarmerTouchReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');

        $qb->select('e.remarks','e.cultureSpeciesItemAndQty','e.nourishItemName','e.otherCultureSpecies');
        $qb->addSelect('agent.name AS agentName', 'agent.mobile AS agentMobile', 'agentDistrict.name AS agentDistrictName', 'agentUpozila.name AS agentThana');
        $qb->addSelect('farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile');

        $qb->addSelect('employee.name AS employeeName', 'employeeRegion.name AS employeeRegionName');

        $qb->leftJoin('e.agent', 'agent');
        $qb->leftJoin('agent.upozila', 'agentUpozila');
        $qb->leftJoin('agent.district', 'agentDistrict');
        $qb->leftJoin('e.customer', 'farmer');
//        $qb->leftJoin('e.employee', 'employee');
        $this->handleSearchFilterBetween($qb, $filterBy);
        $qb->leftJoin('employee.regional', 'employeeRegion');

        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        foreach ($results as $result){
            $agentName= $result['agentName'];
//            $data[$agentName]['agentDistrict'] = $result['agentDistrictName'];
//            $data[$agentName]['agentThana'] = $result['agentThana'];
//            $data[$agentName]['agentMobile'] = $result['agentMobile'];
            $data['employeeName'] = $result['employeeName'];
            $data['employeeRegionName'] = $result['employeeRegionName'];
//            $data[$agentName]['cultureSpeciesItemAndQty'] = json_decode($result['cultureSpeciesItemAndQty'], true);
            $data[$agentName][] = $result;
        }

       return $data;
    }

}
