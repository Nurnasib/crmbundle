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
        $qb->select('e.remarks','e.cultureSpeciesItemAndQty','e.nourishItemName','e.otherCultureSpecies','e.createdAt', 'e.conventionalFeed');

        $qb->addSelect('farmer.name AS farmerName', 'farmer.address AS farmerAddress', 'farmer.mobile AS farmerMobile', 'farmerLocation.name AS farmerThana', 'farmerLocationParent.name AS farmerDistrict');
        $qb->addSelect('employee.name AS employeeName', 'employeeRegion.name AS employeeRegionName', 'employeeDesignation.name AS employeeDesignationName');

        $qb->addSelect('agent.name AS agentName', 'agent.mobile AS agentMobile', 'agentDistrict.name AS agentDistrictName', 'agentUpozila.name AS agentThana');
        $qb->addSelect('e.cultureAreaDecimal', 'e.yearlyFeedUseTon','e.visitingDate');

        $qb->leftJoin('e.agent', 'agent');
        $qb->leftJoin('agent.upozila', 'agentUpozila');
        $qb->leftJoin('agent.district', 'agentDistrict');



        $qb->leftJoin('e.customer', 'farmer');
        $qb->leftJoin('farmer.location', 'farmerLocation');
        $qb->leftJoin('farmerLocation.parent', 'farmerLocationParent');
        $this->handleSearchFilterBetween($qb, $filterBy);
        $qb->leftJoin('employee.regional', 'employeeRegion');
        $qb->leftJoin('employee.designation', 'employeeDesignation');

        $results = $qb->getQuery()->getArrayResult();
        $data = [];
        if($filterBy['slug'] == 'farmer-touch-report-poultry'){
            foreach ($results as $result){
                $agentName= $result['agentName'];
                $data['employeeName'] = $result['employeeName'];
                $data['employeeRegionName'] = $result['employeeRegionName'];
                $data[$agentName][] = $result;
            }
        }elseif($filterBy['slug'] == 'farmer-touch-report-fish'){
            foreach ($results as $result){
                $month = $result['createdAt']->format('F-Y');
                $data['employeeName'] = $result['employeeName'];
                $data['employeeRegionName'] = $result['employeeRegionName'];
                $data['employeeDesignationName'] = $result['employeeDesignationName'];
                $data[$month][] = $result;
            }
        }else{

            return $results;
        }

//        dd($data);
       return $data;
    }

}
