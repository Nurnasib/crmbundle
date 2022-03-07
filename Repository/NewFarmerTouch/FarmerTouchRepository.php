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
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Entity\NewFarmerTouch\FarmerTouchReport;
use Terminalbd\CrmBundle\Entity\Setting;
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


    public function getMonthlyNewfarmInformationOrSurveyTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');

        $qb->join('e.employee', 'employee');
        $qb->join('e.report', 'report');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.visitingDate >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.visitingDate <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);
        $qb->andWhere('report.settingType = :settingType')->setParameter('settingType', 'FARMER_REPORT');
        $qb->andWhere('report.slug = :slug')->setParameter('slug', 'farmer-introduce-report-poultry');

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }


    public function insertFarmerTouch(CrmCustomer $customer, $user, $feed, $data)
    {
        if ($data['farmer_type']){
            $em = $this->_em;
            $entity = new FarmerTouchReport();
            $entity->setCustomer($customer);
            $entity->setAgent($customer->getAgent());
            $entity->setOtherAgent($customer->getAgent());
            $entity->setFeed($feed?$feed:null);
            $entity->setOtherFeed($feed?$feed:null);
            $entity->setCultureSpeciesItemAndQty(json_encode($data['species_type']));
            /*$entity->setPreviousAgentName($data['previous_agent_name']);
            $entity->setPreviousAgentAddress($data['previous_agent_address']);
            $entity->setPreviousFeedName($data['previous_feed_name']);*/

            $entity->setEmployee($user);

            $farmerType = $em->getRepository(Setting::class)->find($data['farmer_type']);
            $entity->setFarmerType($farmerType?$farmerType:null);
            if($farmerType->getSlug()=='fish-breed'){
                $entity->setCultureAreaDecimal(isset($data['culture_area_decimal'])?$data['culture_area_decimal']:0);
                $entity->setYearlyFeedUseTon(isset($data['yearlyFeedUseTon'])?$data['yearlyFeedUseTon']:0);
            }elseif ($farmerType->getSlug()=='cattle-breed'){
                $entity->setConventionalFeed(isset($data['conventional_feed'])?$data['conventional_feed']:'');
            }

            $em->persist($entity);
            $em->flush();
        }

    }

}
