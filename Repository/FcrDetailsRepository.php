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
use Terminalbd\KpiBundle\Entity\EmployeeBoard;

/**
 * This custom Doctrine repository contains some methods which are useful when
 * querying for blog post information.
 *
 * See https://symfony.com/doc/current/doctrine/repository.html
 *
 * @author Md Shafiqul islam <shafiqabs@gmail.com>
 */
class FcrDetailsRepository extends BaseRepository
{
    public function getFcrReportByReportingDateAndFeedType($data, $report, $employee)
    {
        if(isset($data) && $report && $employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('f')
                ->where('f.reportingMonth >= :startDate')
                ->andWhere('f.reportingMonth <= :endDate')
                ->andWhere('f.fcrOfFeed = :type')
                ->andWhere('f.report = :report')
                ->andWhere('f.employee = :employee')
//                ->andWhere('f.customer = :customer')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'type'=>$data, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getResult();
        }
        return array();
    }


    public function getFcrReportByReportingDateReportAndEmployeeForAfter($data, $report, $employee)
    {
        if(isset($data) && $report && $employee){
            $startDate = date('Y-m-01', strtotime("now"));
            $endDate = date('Y-m-t', strtotime("now"));
            $query = $this->createQueryBuilder('f')
                ->where('f.reportingMonth >= :startDate')
                ->andWhere('f.reportingMonth <= :endDate')
                ->andWhere('f.fcrOfFeed = :type')
                ->andWhere('f.report = :report')
                ->andWhere('f.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'type'=>$data, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getOneOrNullResult();
        }
        return array();
    }


    public function getFcrDetailsByEmployee($report, $filterBy)
    {
        $returnArray=[];
        if(!empty($report)){
            $qb = $this->createQueryBuilder('e');

            $qb->select('e.fcrOfFeed', 'e.reportingMonth', 'e.hatchingDate', 'e.totalBirds', 'e.ageDay', 'e.mortalityPes', 'e.mortalityPercent', 'e.weight', 'e.weightStandard', 'e.feedConsumptionTotalKg', 'e.feedConsumptionPerBird', 'e.feedConsumptionStandard', 'e.fcrWithoutMortality', 'e.fcrWithMortality', 'e.proDate', 'e.batchNo', 'e.remarks', 'e.createdAt');

            $qb->addSelect('agent.name AS agentName', 'agent.address AS agentAddress');

            $qb->addSelect('employee.id AS employeeId');
            $qb->addSelect('employee.name AS employeeName');
            $qb->addSelect('customer.name AS customerName', 'customer.address AS customerAddress', 'customer.mobile AS customerMobile');
            $qb->addSelect('customer.id AS customerId');

            $qb->addSelect( 'district.name AS agentDistrictName');

            $qb->addSelect('hatchery.name AS hatcheryName');

            $qb->addSelect('breed.name AS breedBame');
            $qb->addSelect('feed.name AS feedName');
            $qb->addSelect('feed_mill.name AS feedMillName');
            $qb->addSelect('feed_type.name AS feedTypeName');
            $qb->addSelect('region.id AS regionId', 'region.name AS regionName');

            $qb->join('e.employee','employee');
            $qb->leftJoin('e.visit','visit');
            $qb->leftJoin('visit.location','location');
            $qb->leftJoin('location.parent','dist');
            $qb->leftJoin('dist.parent','region');
            $qb->leftJoin('e.customer','customer');
            $qb->leftJoin('e.agent', 'agent');
            $qb->leftJoin('agent.district', 'district');
            $qb->leftJoin('e.hatchery', 'hatchery');
            $qb->leftJoin('e.breed', 'breed');
            $qb->leftJoin('e.feed', 'feed');
            $qb->leftJoin('e.feedMill', 'feed_mill');
            $qb->leftJoin('e.feedType', 'feed_type');
            $qb->where('e.report =:report')->setParameter('report',$report);

            $startDate = isset($filterBy['startDate'])? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00': '';
            $endDate = isset($filterBy['endDate'])? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59': '';

            $employee = isset($filterBy['employeeId'])? $filterBy['employeeId']: '';
            if (!empty($employee)){
                $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee);
            }

            $feedMill = isset($filterBy['feedMill'])? $filterBy['feedMill']: '';
            if (!empty($feedMill)){
                $qb->andWhere('feed_mill.id = :feedMillId')->setParameter('feedMillId', $feedMill);
            }

            $region = isset($filterBy['region'])? $filterBy['region']: '';
            if (!empty($region)){
                $qb->andWhere('region.id = :regionId')->setParameter('regionId', $region);
            }

            if (!empty($startDate) && !empty($endDate)){
                $qb->andWhere('e.reportingMonth >= :reportingMonthStart')->setParameter('reportingMonthStart', $startDate);
                $qb->andWhere('e.reportingMonth <= :reportingMonthEnd')->setParameter('reportingMonthEnd', $endDate);
            }


            $feedCompany = isset($filterBy['feedCompany'])&& $filterBy['feedCompany']!=''? $filterBy['feedCompany']: '';
            if (!empty($feedCompany)){
                if($feedCompany=='NOURISH'){
                    $qb->andWhere('feed.name = :feed_name')->setParameter('feedId','Nourish');
                }elseif ($feedCompany=='OTHERS'){
                    $qb->andWhere('feed.name != :feed_name')->setParameter('feed_name','Nourish');
                }
            }


            $results = $qb->getQuery()->getArrayResult();
//            dd($results);
            $bodyWtGatterThanStandard=[];
            $bodyWtLessThanStandard=[];
            $fcrWithMortalitySummery=[];
            if($results){
                foreach ($results as $result){
                    $result['cfcr']=(((2-($result['weight']/1000))*0.25)+($result['fcrWithMortality']));
                    $monthYear = $result['reportingMonth']->format('F-Y');

                    $returnArray[$monthYear][$result['regionId']][$result['employeeId']]['name']=$result['employeeName'];
                    $returnArray[$monthYear][$result['regionId']][$result['employeeId']]['details'][]=$result;
                    $returnArray[$monthYear][$result['regionId']]['recordCount'][]=$result;
                    $returnArray[$monthYear]['monthRecordCount'][] = $result;
                    

                    if(in_array($report->getSlug(),['fcr-before-sale-boiler','fcr-before-sale-sonali'])){
                        if($result['weight']>=$result['weightStandard']){
                            $bodyWtGatterThanStandard[]=$result;
                        }elseif ($result['weight']<$result['weightStandard']){
                            $bodyWtLessThanStandard[]=$result;
                        }
                    }
                    if(in_array($report->getSlug(),['fcr-after-sale-sonali'])){
                        if($result['fcrWithMortality']<2.50){
                            $fcrWithMortalitySummery['excellent'][]=$result;
                        }elseif ($result['fcrWithMortality']>=2.50 && $result['fcrWithMortality']<2.60){
                            $fcrWithMortalitySummery['very_good'][]=$result;
                        }elseif ($result['fcrWithMortality']>=2.60 && $result['fcrWithMortality']<2.70){
                            $fcrWithMortalitySummery['good'][]=$result;
                        }elseif ($result['fcrWithMortality']>=2.70 && $result['fcrWithMortality']<2.80){
                            $fcrWithMortalitySummery['moderate'][]=$result;
                        }elseif ($result['fcrWithMortality']>=2.80 && $result['fcrWithMortality']<2.90){
                            $fcrWithMortalitySummery['bad'][]=$result;
                        }elseif ($result['fcrWithMortality']>=2.90){
                            $fcrWithMortalitySummery['very_bad'][]=$result;
                        }
                    }
                    if(in_array($report->getSlug(),['fcr-after-sale-boiler'])){
                        if($result['fcrWithMortality']<=1.45){
                            $fcrWithMortalitySummery['excellent'][]=$result;
                        }elseif ($result['fcrWithMortality']>=1.46 && $result['fcrWithMortality']<1.50){
                            $fcrWithMortalitySummery['very_good'][]=$result;
                        }elseif ($result['fcrWithMortality']>=1.50 && $result['fcrWithMortality']<1.53){
                            $fcrWithMortalitySummery['good'][]=$result;
                        }elseif ($result['fcrWithMortality']>=1.53 && $result['fcrWithMortality']<1.56){
                            $fcrWithMortalitySummery['moderate'][]=$result;
                        }elseif ($result['fcrWithMortality']>=1.56 && $result['fcrWithMortality']<1.61){
                            $fcrWithMortalitySummery['bad'][]=$result;
                        }elseif ($result['fcrWithMortality']>=1.61){
                            $fcrWithMortalitySummery['very_bad'][]=$result;
                        }
                    }

                }
                if(in_array($report->getSlug(),['fcr-before-sale-sonali','fcr-after-sale-sonali'])){

                    $returnArray['fcrSonaliStandard']=$this->getEntityManager()->getRepository('TerminalbdCrmBundle:SonaliStandard')->getSonaliStandard();
                }
                
                if(in_array($report->getSlug(),['fcr-before-sale-boiler','fcr-after-sale-boiler'])){

                    $returnArray['fcrBoilerStandard']=$this->getEntityManager()->getRepository('TerminalbdCrmBundle:BroilerStandard')->getBoilerStandard();
                }


                
                if(in_array($report->getSlug(),['fcr-before-sale-boiler','fcr-before-sale-sonali'])){
                    $returnArray['bodyWtGatterThanStandard']= count($bodyWtGatterThanStandard);
                    $returnArray['bodyWtLessThanStandard']= count($bodyWtLessThanStandard);
                }

                if(in_array($report->getSlug(),['fcr-after-sale-sonali','fcr-after-sale-boiler'])){
                    $returnArray['fcrWithMortalitySummery']=$fcrWithMortalitySummery;
                    $returnArray['totalRecord']= ['countRecord'=>count($results),'arrayData'=>$results];
                }else{
                    $returnArray['totalRecord']= count($results);
                }
            }
        }

        return $returnArray;


    }

    public function getMonthlyFcrAfterSaleTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');
        $qb->join('e.employee', 'employee');
        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.fcrOfFeed = :fcrFeed')->setParameter('fcrFeed', 'AFTER');
        $qb->andWhere('e.reportingMonth >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.reportingMonth <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }

    public function getMonthlyBroilerBeforeSaleTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');

        $qb->join('e.employee', 'employee');
        $qb->join('e.report', 'report');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.fcrOfFeed = :fcrFeed')->setParameter('fcrFeed', 'BEFORE');
        $qb->andWhere('e.reportingMonth >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.reportingMonth <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);
        $qb->andWhere('report.slug = :slug')->setParameter('slug', 'fcr-before-sale-boiler');

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }


    public function getNumberOfReportsForKpi($board, $type)
    {
        /**
         * @var EmployeeBoard $board
         */
        $startDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-d');
        $endDate = (new \DateTime('01-' . date('m', strtotime($board->getMonth())) . '-' . $board->getYear()))->format('Y-m-t');


        $qb = $this->createQueryBuilder('e');

        $qb->where('e.employee = :employee')->setParameter('employee', $board->getEmployee());
        $qb->andWhere('e.fcrOfFeed = :type')->setParameter('type', $type);
        $qb->andWhere('e.reportingMonth >= :startDate')->setParameter('startDate', $startDate);
        $qb->andWhere('e.reportingMonth <= :endDate')->setParameter('endDate', $endDate);

        return count($qb->getQuery()->getArrayResult());
    }

}
