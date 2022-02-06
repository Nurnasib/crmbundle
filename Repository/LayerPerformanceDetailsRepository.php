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

//use Doctrine\ORM\EntityRepository;
use App\Entity\Core\Agent;
use App\Entity\User;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
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
class LayerPerformanceDetailsRepository extends BaseRepository
{

    public function getLayerPerformanceReportByReportingDateAndFeedType($report, $employee)
    {
        if($report&&$employee){
            $startDate = date('Y-m-01', strtotime('now'));
            $endDate = date('Y-m-t', strtotime('now'));
            $query = $this->createQueryBuilder('lpr')
                ->where('lpr.reportingMonth >= :startDate')
                ->andWhere('lpr.reportingMonth <= :endDate')
                ->andWhere('lpr.report = :report')
//                ->andWhere('lpr.customer = :customer')
                ->andWhere('lpr.employee = :employee')
                ->setParameters(array('startDate'=>$startDate, 'endDate'=>$endDate, 'report'=>$report, 'employee'=>$employee));

            return $query->getQuery()->getResult();
        }
        return array();
    }

    public function getMonthlyLayerPerformanceTotalReport($filterBy)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select('COUNT(e) as totalReport');

        $qb->join('e.employee', 'employee');

        $qb->where('employee.id = :employeeId')->setParameter('employeeId', $filterBy['employeeId']);
        $qb->andWhere('e.reportingMonth >= :monthStart')->setParameter('monthStart', $filterBy['monthStart']);
        $qb->andWhere('e.reportingMonth <= :monthEnd')->setParameter('monthEnd', $filterBy['monthEnd']);

        $results = $qb->getQuery()->getSingleResult();
        return $results['totalReport'];
    }

    public function getLayerPerformanceReportByEmployeeAndDate($report, $filterBy)
    {
        $returnArray=[];
        if(!empty($report)){
            $qb = $this->createQueryBuilder('e');
            $qb->select('e.id as rId', 'e.reportingMonth', 'e.totalBirds', 'e.ageWeek');
            $qb->addSelect( 'e.birdWeightAchieved','e.birdWeightTarget','e.feedIntakePerBird','e.feedTarget');
            $qb->addSelect( 'e.eggProductionAchieved','e.eggProductionTarget','e.eggWeightAchieved','e.eggWeightStand');
            $qb->addSelect( 'e.productionDate','e.batch_no','e.disease','e.remarks');

            $qb->addSelect('agent.name AS agentName', 'agent.address AS agentAddress');

            $qb->addSelect('employee.id AS employeeId');
            $qb->addSelect('employee.name AS employeeName');
            $qb->addSelect('designation.name AS employeeDesignationName');
            $qb->addSelect('customer.name AS customerName');
            $qb->addSelect('customer.id AS customerId');
            $qb->addSelect('customer.mobile AS customerMobile');

            $qb->addSelect( 'district.name AS agentDistrictName');

            $qb->addSelect('hatchery.name AS hatcheryName');

            $qb->addSelect('breed.name AS breedBame');
            $qb->addSelect('feed.name AS feedName');
            $qb->addSelect('feedMill.name AS feedMillName');
            $qb->addSelect('feedType.name AS feedTypeName');
            $qb->addSelect('color.name AS colorName');

            $qb->join('e.employee', 'employee');
            $qb->leftJoin('employee.designation', 'designation');
            $qb->leftJoin('e.customer','customer');
            $qb->leftJoin('e.agent', 'agent');
            $qb->leftJoin('agent.district', 'district');
            $qb->leftJoin('e.hatchery', 'hatchery');
            $qb->leftJoin('e.breed', 'breed');
            $qb->leftJoin('e.feed', 'feed');
            $qb->leftJoin('e.feedMill', 'feedMill');
            $qb->leftJoin('e.feedType', 'feedType');
            $qb->leftJoin('e.color', 'color');
            $qb->where('e.report =:report')->setParameter('report',$report);

            $startDate = isset($filterBy['startDate'])&&$filterBy['startDate']!=''? (new \DateTime($filterBy['startDate']))->format('Y-m-d') . ' 00:00:00': '';
            $endDate = isset($filterBy['endDate']) && $filterBy['endDate']!=''? (new \DateTime($filterBy['endDate']))->format('Y-m-d') . ' 23:59:59': '';

            $employee = isset($filterBy['employeeId'])&&$filterBy['employeeId']!=''? $filterBy['employeeId']: '';
            if (!empty($employee)){
                $qb->andWhere('employee.id = :employee')->setParameter('employee', $employee);
            }

            if (!empty($startDate) && !empty($endDate)){
                $qb->andWhere('e.reportingMonth >= :reportingMonthStart')->setParameter('reportingMonthStart', $startDate);
                $qb->andWhere('e.reportingMonth <= :reportingMonthEnd')->setParameter('reportingMonthEnd', $endDate);
            }
            $qb->orderBy('e.reportingMonth','ASC');

            $results = $qb->getQuery()->getArrayResult();
            if($results){
                foreach ($results as $result){
                    $monthYear = $result['reportingMonth']->format('F-Y');
                    $returnArray[$monthYear][$result['employeeId']]['name']=$result['employeeName'];
                    $returnArray[$monthYear][$result['employeeId']]['employeeDesignationName']=$result['employeeDesignationName'];
                    $returnArray[$monthYear][$result['employeeId']]['details'][]=$result;
                }
            }
        }
//        dd($returnArray);
        return $returnArray;
    }

    public function insertDataFromApi(array $data)
    {
        $created = new \DateTime($data['created']);
        $repotingMonth = new \DateTime($data['repoting_month']);
        $productionDate = new \DateTime($data['production_date']);

        $customer = $this->getEntityManager()->getRepository(CrmCustomer::class)->find($data['customer_id']);
        $agent = $this->getEntityManager()->getRepository(Agent::class)->findOneBy(['agentId' => $data['agent_id'], 'status' => 1]);
        $employee = $this->getEntityManager()->getRepository(User::class)->find($data['employee_id']);

        $report = $this->getEntityManager()->getRepository(Setting::class)->findOneBy(['id' => $data['report_id'], 'settingType' => 'FARMER_REPORT', 'status' => 1]);
        $hatchery = $this->getEntityManager()->getRepository(Setting::class)->findOneBy(['id' => $data['hatchery_id'], 'settingType' => 'HATCHERY', 'status' => 1]);
        $breed = $this->getEntityManager()->getRepository(Setting::class)->findOneBy(['id' => $data['breed_id'], 'settingType' => 'BREED_TYPE', 'status' => 1]);
        $feed = $this->getEntityManager()->getRepository(Setting::class)->findOneBy(['id' => $data['feed_id'], 'settingType' => 'FEED_NAME', 'status' => 1]);
        $feedMill = $this->getEntityManager()->getRepository(Setting::class)->findOneBy(['id' => $data['feed_mill_id'], 'settingType' => 'FEED_MILL', 'status' => 1]);
        $feedType = $this->getEntityManager()->getRepository(Setting::class)->findOneBy(['id' => $data['feed_type_id'], 'settingType' => 'FEED_TYPE', 'status' => 1]);
        $color = $this->getEntityManager()->getRepository(Setting::class)->findOneBy(['id' => $data['color_id'], 'settingType' => 'COLOR', 'status' => 1]);

        if ($customer && $agent && $employee){
            $sql = "INSERT INTO `crm_layer_performance_details`(`employee_id`, `report_id`, `agent_id`, `customer_id`, `hatchery_id`, `breed_id`, `feed_id`, `feed_mill_id`, `feed_type_id`, `color_id`, `repoting_month`, `total_birds`, `age_week`, `bird_weight_achieved`, `bird_weight_target`, `feed_intake_per_bird`, `feed_Target`, `egg_production_achieved`, `egg_production_target`, `egg_weight_achieved`, `egg_weight_stand`, `production_date`, `batch_no`, `disease`, `remarks`, `created`) VALUES (:employeeId, :reportId, :agentId, :customerId, :hatcheryId, :breedId, :feedId, :feedMillId, :feedTypeId, :colorId, :repotingMonth, :totalBirds, :ageWeek, :birdWeightAchieved, :birdWeightTarget, :feedIntakePerBird, :feedTarget, :eggProductionAchieved, :eggProductionTarget, :eggWeightAchieved, :eggWeightStand, :productionDate, :batchNo, :disease, :remarks, :created)";
            $em = $this->_em;
            $stmt = $em->getConnection()->prepare($sql);
            $stmt->bindValue('employeeId', $employee->getId());
            $stmt->bindValue('reportId', $report ? $report->getId() : null);
            $stmt->bindValue('agentId', $agent->getId());
            $stmt->bindValue('customerId', $customer->getId());
            $stmt->bindValue('hatcheryId', $hatchery ? $hatchery->getId() : null);
            $stmt->bindValue('breedId', $breed ? $breed->getId() : null);
            $stmt->bindValue('feedId', $feed ? $feed->getId() : null);
            $stmt->bindValue('feedMillId', $feedMill ? $feedMill->getId() : null);
            $stmt->bindValue('feedTypeId', $feedType ? $feedType->getId() : null);
            $stmt->bindValue('colorId', $color ? $color->getId() : null);
            $stmt->bindValue('repotingMonth', $repotingMonth->format('Y-m-d'));
            $stmt->bindValue('totalBirds', $data['total_birds']);
            $stmt->bindValue('ageWeek', $data['age_week']);
            $stmt->bindValue('birdWeightAchieved', $data['bird_weight_achieved']);
            $stmt->bindValue('birdWeightTarget', $data['bird_weight_target']);
            $stmt->bindValue('feedIntakePerBird', $data['feed_intake_per_bird']);
            $stmt->bindValue('feedTarget', $data['feed_Target']);
            $stmt->bindValue('eggProductionAchieved', $data['egg_production_achieved']);
            $stmt->bindValue('eggProductionTarget', $data['egg_production_target']);
            $stmt->bindValue('eggWeightAchieved', $data['egg_weight_achieved']);
            $stmt->bindValue('eggWeightStand', $data['egg_weight_stand']);
            $stmt->bindValue('productionDate', $productionDate->format('Y-m-d'));
            $stmt->bindValue('batchNo', $data['batch_no']);
            $stmt->bindValue('disease', $data['disease']);
            $stmt->bindValue('remarks', $data['remarks']);
            $stmt->bindValue('created', $created->format('Y-m-d H:i:s'));
            $stmt->execute();
        }

    }
}
