<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Terminalbd\CrmBundle\Controller;

use App\Entity\Admin\Location;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\Api;
use Terminalbd\CrmBundle\Entity\CattleLifeCycle;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * Class SyncAppDataController
 * @package Terminalbd\CrmBundle\Controller
 * @Route("/crm/sync-app-data", name="crm_sync_app_data")
 */
class SyncAppDataController extends AbstractController
{
    /**
     * @param Request $request
     * @param $entities
     * @return \Knp\Component\Pager\Pagination\PaginationInterface
     */
    private function pagination(Request $request, $entities)
    {
        $paginator = $this->get('knp_paginator');

        return $paginator->paginate($entities, $request->query->get('page', 1), 25);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/", name="_index")
     */
    public function index(Request $request)
    {
        $records = $this->getDoctrine()->getRepository(Api::class)->findBy(['status' => 0]);
        $records = $this->pagination($request, $records);

        return $this->render('@TerminalbdCrm/api/api-response-list.html.twig',[
            'records' => $records,
        ]);
    }

    /**
     * @Route("/sync", name="_sync")
     */
    public function syncAppData()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();

        $batches = $this->getDoctrine()->getRepository(Api::class)->findBy(['status' => 0]);
        foreach ($batches as $batch) {
            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appBatch' => $batch]);
            if (!$findVisit){
                $details = $batch->getApiDetails();
                foreach ($details as $detail) {

                    $jsonToArray = json_decode($detail->getJsonData(), true);

                    switch ($detail->getProcess()){
                        case "crm_visit":
                            $this->processVisit($jsonToArray, $batch);
                            break;
                        case "crm_visit_details":
                            $this->processVisitDetail($jsonToArray, $batch);
                            break;
                        case "crm_layer_performance_details":
                            $this->processLayerPerformance($jsonToArray, $batch);
                            break;
                        case "crm_cattle_performance_details":
                            $this->processCattlePerformance($jsonToArray, $batch);
                            break;
                        case "crm_fcr_details":
                            $this->processFcrDetail($jsonToArray, $batch);
                            break;
                        case "crm_antibiotic_free_farm":
                            $this->processAntibioticFreeFarm($jsonToArray, $batch);
                            break;
                        case "crm_cost_benefit_analysis_for_less_costing_farm":
                            $this->processCostBenefitAnalysis($jsonToArray, $batch);
                            break;
                        case "crm_disease_mapping":
                            $this->processDiseaseMapping($jsonToArray, $batch);
                            break;
                        case "crm_complain_different_product":
                            $this->processComplain($jsonToArray, $batch);
                            break;
                        case "crm_broiler_life_cycle":
                            $this->processBroilerLifeCycle($jsonToArray, $batch);
                            break;
                        case "crm_broiler_life_cycle_details":
                            $this->processBroilerLifeCycleDetail($jsonToArray, $batch);
                            break;
                        case "crm_cattle_life_cycle":
                            $this->processCattleLifeCycle($jsonToArray, $batch);
                            break;
                        case "crm_cattle_life_cycle_details":
                            $this->processCattleLifeCycleDetail($jsonToArray, $batch);
                            break;
                        case "crm_layer_life_cycle":
                            $this->processLayerLifeCycle($jsonToArray, $batch);
                            break;
                        case "crm_layer_life_cycle_details":
                            $this->processLayerLifeCycleDetail($jsonToArray, $batch);
                            break;
                        case "crm_expense":
                            $this->processExpense($jsonToArray, $batch);
                            break;
                        case "crm_expense_purpose":
                            $this->processExpensePurpose($jsonToArray, $batch);
                            break;
                        case "crm_expense_vehicle":
                            $this->processExpenseVehicle($jsonToArray, $batch);
                            break;
                        case "crm_doc_complain_details":
                            $this->processDocComplain($jsonToArray, $batch);
                            break;
                        case "crm_feed_complain_details":
                            $this->processFeedComplain($jsonToArray, $batch);
                            break;
                        case "crm_customer":
                            $this->processFarmer($jsonToArray, $batch);
                            break;
                        case "crm_customer_introduce_details":
                            $this->processFarmerIntroduce($jsonToArray, $batch);
                            break;
                    }
                    $detail->setStatus(true);
                    $em->persist($detail);
                    $em->flush();
                }
            }
            $batch->setStatus(true);
            $em->persist($batch);
            $em->flush();
        }
        $this->addFlash('success', 'Synchronization Completed!');
        return $this->redirectToRoute('crm_sync_app_data_index');
    }


    private function processVisit($visits, Api $batch)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($visits as $visitKey => $visit) {
            $createdAt = new \DateTime($visit['created_at']);
            $findEmployee = $this->getDoctrine()->getRepository(User::class)->find($visit['employee_id']);
            $findLocation = $this->getDoctrine()->getRepository(Location::class)->find($visit['location_id']);

            if ($findEmployee && $findLocation){
                $newVisit = new CrmVisit();
                $newVisit->setEmployee($findEmployee);
                $newVisit->setAppId($visit['id']);
                $newVisit->setAppBatch($batch);
                $newVisit->setLocation($findLocation);
                $newVisit->setWorkingDuration($visit['duration_from']);
                $newVisit->setWorkingDurationTo($visit['duration_to']);
                $newVisit->setCreated($createdAt);

                $em->persist($newVisit);
                $em->flush();
            }
        }
    }
    private function processVisitDetail($visitDetails, Api $batch)
    {
        foreach ($visitDetails as $visitDetail) {
            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $visitDetail['crm_visit_id'], 'appBatch' => $batch]);
            if ($findVisit){
                $sql = "INSERT INTO `crm_visit_details`(`crm_visit_id`, `farmCapacity`, `updated`, `comments`, `created`, `customer_id`, `process`, `agent_id`, `purpose_id`, `firm_type_id`, `report_id`)
VALUES (:crm_visit_id, :farmCapacity, :updated, :comments, :created, :customer_id, :process, :agent_id, :purpose_id, :firm_type_id, :report_id)";

                $createdAt = new \DateTime($visitDetail['created']);
                $updatedAt = new \DateTime($visitDetail['updated']);

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('crm_visit_id', $findVisit->getId());
                $stmt->bindValue('farmCapacity', $visitDetail['farmCapacity']);
                $stmt->bindValue('updated', $updatedAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('comments', $visitDetail['comments']);
                $stmt->bindValue('created', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('customer_id', $visitDetail['customer_id']);
                $stmt->bindValue('agent_id', $visitDetail['agent_id']);
                $stmt->bindValue('process', $visitDetail['process']);
                $stmt->bindValue('purpose_id', $visitDetail['purpose_id']);
                $stmt->bindValue('firm_type_id', $visitDetail['firm_type_id']);
                $stmt->bindValue('report_id', $visitDetail['report_id']);

                $stmt->execute();
            }
        }
    }
    private function processLayerPerformance($performances, Api $batch)
    {
        foreach ($performances as $performance) {
            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $performance['crm_visit_id'], 'appBatch' => $batch]);
            if ($findVisit){
                $sql = "INSERT INTO 
`crm_layer_performance_details`
(`employee_id`, `report_id`, `agent_id`, `customer_id`, `hatchery_id`, `breed_id`, `feed_id`, `feed_mill_id`, `feed_type_id`, `color_id`, `repoting_month`, `total_birds`, `age_week`, `bird_weight_achieved`, `bird_weight_target`, `feed_intake_per_bird`, `feed_Target`, `egg_production_achieved`, `egg_production_target`, `egg_weight_achieved`, `egg_weight_stand`, `production_date`, `batch_no`, `disease`, `remarks`, `created`, `updated`, `visit_id`) VALUES 
(:employee_id, :report_id, :agent_id, :customer_id, :hatchery_id, :breed_id, :feed_id, :feed_mill_id, :feed_type_id, :color_id, :repoting_month, :total_birds, :age_week, :bird_weight_achieved, :bird_weight_target, :feed_intake_per_bird, :feed_Target, :egg_production_achieved, :egg_production_target, :egg_weight_achieved, :egg_weight_stand, :production_date, :batch_no, :disease, :remarks, :created, :updated, :visit_id)";

                $repotingMonth = new \DateTime($performance['repoting_month']);
                $createdAt = new \DateTime($performance['created']);
                $updatedAt = new \DateTime($performance['updated']);
                $productionDate = new \DateTime($performance['production_date']);

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('employee_id', $performance['employee_id']);
                $stmt->bindValue('report_id', $performance['report_id']);
                $stmt->bindValue('agent_id', $performance['agent_id']);
                $stmt->bindValue('customer_id', $performance['customer_id']);
                $stmt->bindValue('hatchery_id', $performance['hatchery_id']);
                $stmt->bindValue('breed_id', $performance['breed_id']);
                $stmt->bindValue('feed_id', $performance['feed_id']);
                $stmt->bindValue('feed_mill_id', $performance['feed_mill_id']);
                $stmt->bindValue('feed_type_id', $performance['feed_type_id']);
                $stmt->bindValue('color_id', $performance['color_id']);
                $stmt->bindValue('repoting_month', $repotingMonth->format('Y-m-d'));
                $stmt->bindValue('total_birds', $performance['total_birds']);
                $stmt->bindValue('age_week', $performance['age_week']);
                $stmt->bindValue('bird_weight_achieved', $performance['bird_weight_achieved']);
                $stmt->bindValue('bird_weight_target', $performance['bird_weight_target']);
                $stmt->bindValue('feed_intake_per_bird', $performance['feed_intake_per_bird']);
                $stmt->bindValue('feed_Target', $performance['feed_Target']);
                $stmt->bindValue('egg_production_achieved', $performance['egg_production_achieved']);
                $stmt->bindValue('egg_production_target', $performance['egg_production_target']);
                $stmt->bindValue('egg_weight_achieved', $performance['egg_weight_achieved']);
                $stmt->bindValue('egg_weight_stand', $performance['egg_weight_stand']);
                $stmt->bindValue('production_date', $productionDate->format('Y-m-d'));
                $stmt->bindValue('batch_no', $performance['batch_no']);
                $stmt->bindValue('disease', $performance['disease']);
                $stmt->bindValue('remarks', $performance['remarks']);
                $stmt->bindValue('created', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('updated', $updatedAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('visit_id', $findVisit->getId());

                $stmt->execute();
            }

        }
    }
    private function processCattlePerformance($performances, Api $batch)
    {
        foreach ($performances as $performance) {
            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $performance['crm_visit_id'], 'appBatch' => $batch]);
            if ($findVisit){
                $sql = "INSERT INTO `crm_cattle_performance_details`(`employee_id`, `report_id`, `agent_id`, `customer_id`, `breed_type`, `feed_type`, `repoting_month`, `visiting_date`, `age_of_cattle_month`, `previous_body_weight`, `present_body_weight`, `body_weight_difference`, `duration_of_bwt_difference`, `lactation_no`, `age_of_lactation`, `average_weight_per_day`, `average_weight_per_kg_consumption_feed`, `average_weight_per_kg_dm`, `milk_fat_percentage`, `consumption_feed_intake_ready_feed`, `consumption_feed_intake_conventional`, `consumption_feed_intake_total`, `fodder_green_grass_kg`, `fodder_straw_kg`, `dm_of_fodder_green_grass_kg`, `dm_of_fodder_straw_kg`, `total_dm_kg`, `dm_requirement_by_bwt_kg`, `remarks`, `created_at`, `updated_at`, `visit_id`) 
VALUES (:employee_id, :report_id, :agent_id, :customer_id, :breed_type, :feed_type, :repoting_month, :visiting_date, :age_of_cattle_month, :previous_body_weight, :present_body_weight, :body_weight_difference, :duration_of_bwt_difference, :lactation_no, :age_of_lactation, :average_weight_per_day, :average_weight_per_kg_consumption_feed, :average_weight_per_kg_dm, :milk_fat_percentage, :consumption_feed_intake_ready_feed, :consumption_feed_intake_conventional, :consumption_feed_intake_total, :fodder_green_grass_kg, :fodder_straw_kg, :dm_of_fodder_green_grass_kg, :dm_of_fodder_straw_kg, :total_dm_kg, :dm_requirement_by_bwt_kg, :remarks, :created_at, :updated_at, :visit_id)";

                $repotingMonth = new \DateTime($performance['repoting_month']);
                $visitingDate = new \DateTime($performance['visiting_date']);
                $createdAt = new \DateTime($performance['created_at']);
                $updatedAt = new \DateTime($performance['updated_at']);

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('employee_id', $performance['employee_id']);
                $stmt->bindValue('report_id', $performance['report_id']);
                $stmt->bindValue('agent_id', $performance['agent_id']);
                $stmt->bindValue('customer_id', $performance['customer_id']);
                $stmt->bindValue('breed_type', $performance['breed_type']);
                $stmt->bindValue('feed_type', $performance['feed_type']);
                $stmt->bindValue('repoting_month', $repotingMonth->format('Y-m-d'));
                $stmt->bindValue('visiting_date', $visitingDate->format('Y-m-d'));
                $stmt->bindValue('age_of_cattle_month', $performance['age_of_cattle_month']);
                $stmt->bindValue('present_body_weight', $performance['present_body_weight']);
                if ($performance['previous_body_weight'] === null){
                    $stmt->bindValue('previous_body_weight', 0);
                }else{
                    $stmt->bindValue('previous_body_weight', $performance['previous_body_weight']);
                }
                if ($performance['body_weight_difference'] === null){
                    $stmt->bindValue('body_weight_difference', 0);
                }else{
                    $stmt->bindValue('body_weight_difference', $performance['body_weight_difference']);
                }
                if ($performance['duration_of_bwt_difference'] === null){
                    $stmt->bindValue('duration_of_bwt_difference', 0);
                }else{
                    $stmt->bindValue('duration_of_bwt_difference', $performance['duration_of_bwt_difference']);

                }
                if ($performance['lactation_no'] === null){
                    $stmt->bindValue('lactation_no', 0);
                }else{
                    $stmt->bindValue('lactation_no', $performance['lactation_no']);
                }
                if ($performance['age_of_lactation'] === null){
                    $stmt->bindValue('age_of_lactation', 0);
                }else{
                    $stmt->bindValue('age_of_lactation', $performance['age_of_lactation']);
                }
                if ($performance['milk_fat_percentage'] === null){
                    $stmt->bindValue('milk_fat_percentage', 0);
                }else{
                    $stmt->bindValue('milk_fat_percentage', $performance['milk_fat_percentage']);
                }
                $stmt->bindValue('average_weight_per_day', $performance['average_weight_per_day']);
                $stmt->bindValue('average_weight_per_kg_consumption_feed', $performance['average_weight_per_kg_consumption_feed']);
                $stmt->bindValue('average_weight_per_kg_dm', $performance['average_weight_per_kg_dm']);
                $stmt->bindValue('consumption_feed_intake_ready_feed', $performance['consumption_feed_intake_ready_feed']);
                $stmt->bindValue('consumption_feed_intake_conventional', $performance['consumption_feed_intake_conventional']);
                $stmt->bindValue('consumption_feed_intake_total', $performance['consumption_feed_intake_total']);
                $stmt->bindValue('fodder_green_grass_kg', $performance['fodder_green_grass_kg']);
                $stmt->bindValue('fodder_straw_kg', $performance['fodder_straw_kg']);
                $stmt->bindValue('dm_of_fodder_green_grass_kg', $performance['dm_of_fodder_green_grass_kg']);
                $stmt->bindValue('dm_of_fodder_straw_kg', $performance['dm_of_fodder_straw_kg']);
                $stmt->bindValue('total_dm_kg', $performance['total_dm_kg']);
                $stmt->bindValue('dm_requirement_by_bwt_kg', $performance['dm_requirement_by_bwt_kg']);
                $stmt->bindValue('remarks', $performance['remarks']);
                $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('updated_at', $updatedAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('visit_id', $findVisit->getId());

                $stmt->execute();
            }
        }
    }
    private function processFcrDetail($frcDetails, Api $batch)
    {
        foreach ($frcDetails as $frcDetail) {
            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $frcDetail['crm_visit_id'], 'appBatch' => $batch]);
            if ($findVisit){
                $sql = "INSERT INTO `crm_fcr_details`(`report_id`, `employee_id`, `agent_id`, `customer_id`, `hatchery_id`, `breed_id`, `feed_id`, `feed_mill_id`, `feed_type_id`, `fcr_of_feed`, `reporting_month`, `hatching_date`, `total_birds`, `age_day`, `mortality_pes`, `mortality_percent`, `weight_standard`, `weight`, `feed_consumption_total_kg`, `feed_consumption_per_bird`, `feed_consumption_standard`, `fcr_without_mortality`, `fcr_with_mortality`, `pro_date`, `batch_no`, `remarks`, `created_at`, `visit_id`) 
VALUES (:report_id, :employee_id, :agent_id, :customer_id, :hatchery_id, :breed_id, :feed_id, :feed_mill_id, :feed_type_id, :fcr_of_feed, :reporting_month, :hatching_date, :total_birds, :age_day, :mortality_pes, :mortality_percent, :weight_standard, :weight, :feed_consumption_total_kg, :feed_consumption_per_bird, :feed_consumption_standard, :fcr_without_mortality, :fcr_with_mortality, :pro_date, :batch_no, :remarks, :created_at, :visit_id)";
                $repotingMonth = new \DateTime($frcDetail['reporting_month']);
                $hatchingDate = new \DateTime($frcDetail['hatching_date']);
                $createdAt = new \DateTime($frcDetail['created_at']);
                $proDate = new \DateTime($frcDetail['pro_date']);

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('report_id', $frcDetail['report_id']);
                $stmt->bindValue('employee_id', $frcDetail['employee_id']);
                $stmt->bindValue('agent_id', $frcDetail['agent_id']);
                $stmt->bindValue('customer_id', $frcDetail['customer_id']);
                $stmt->bindValue('hatchery_id', $frcDetail['hatchery_id']);
                $stmt->bindValue('breed_id', $frcDetail['breed_id']);
                $stmt->bindValue('feed_id', $frcDetail['feed_id']);
                $stmt->bindValue('feed_mill_id', $frcDetail['feed_mill_id']);
                $stmt->bindValue('feed_type_id', $frcDetail['feed_type_id']);
                $stmt->bindValue('fcr_of_feed', $frcDetail['fcr_of_feed']);
                $stmt->bindValue('reporting_month', $repotingMonth->format('Y-m-d'));
                $stmt->bindValue('hatching_date', $hatchingDate->format('Y-m-d'));
                $stmt->bindValue('total_birds', $frcDetail['total_birds']);
                $stmt->bindValue('age_day', $frcDetail['age_day']);
                $stmt->bindValue('mortality_pes', $frcDetail['mortality_pes']);
                $stmt->bindValue('mortality_percent', $frcDetail['mortality_percent']);
                $stmt->bindValue('weight_standard', $frcDetail['weight_standard']);
                $stmt->bindValue('weight', $frcDetail['weight']);
                $stmt->bindValue('feed_consumption_total_kg', $frcDetail['feed_consumption_total_kg']);
                $stmt->bindValue('feed_consumption_per_bird', $frcDetail['feed_consumption_per_bird']);
                $stmt->bindValue('feed_consumption_standard', $frcDetail['feed_consumption_standard']);
                $stmt->bindValue('fcr_without_mortality', $frcDetail['fcr_without_mortality']);
                $stmt->bindValue('fcr_with_mortality', $frcDetail['fcr_with_mortality']);
                $stmt->bindValue('pro_date', $proDate->format('Y-m-d'));
                $stmt->bindValue('batch_no', $frcDetail['batch_no']);
                $stmt->bindValue('remarks', $frcDetail['remarks']);
                $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('visit_id', $findVisit->getId());

                $stmt->execute();
            }
        }
    }
    private function processAntibioticFreeFarm($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $sql = "INSERT INTO `crm_antibiotic_free_farm`(`report_id`, `report_parent_parent_id`, `agent_id`, `customer_id`, `employee_id`, `hatchery_id`, `breed_id`, `feed_id`, `hatching_date`, `reporting_month`, `total_stocked_chicks_pcs`, `total_feed_used_kg`, `age_days`, `total_broiler_weight_kg`, `mortality`, `fcr`, `remarks`, `created_at`, `medicine_total_cost`, `vaccine_total_cost`) 
VALUES (:report_id, :report_parent_parent_id, :agent_id, :customer_id, :employee_id, :hatchery_id, :breed_id, :feed_id, :hatching_date, :reporting_month, :total_stocked_chicks_pcs, :total_feed_used_kg, :age_days, :total_broiler_weight_kg, :mortality, :fcr, :remarks, :created_at, :medicine_total_cost, :vaccine_total_cost)";

            $hatchingDate = new \DateTime($report['hatching_date']);
            $reportingMonth = new \DateTime($report['reporting_month']);
            $createdAt = new \DateTime($report['created_at']);

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('report_id',$report['report_id']);
            $stmt->bindValue('report_parent_parent_id',$report['report_parent_parent_id']);
            $stmt->bindValue('agent_id',$report['agent_id']);
            $stmt->bindValue('customer_id',$report['customer_id']);
            $stmt->bindValue('employee_id',$report['employee_id']);
            $stmt->bindValue('hatchery_id',$report['hatchery_id']);
            $stmt->bindValue('breed_id',$report['breed_id']);
            $stmt->bindValue('feed_id',$report['feed_id']);
            $stmt->bindValue('hatching_date',$hatchingDate->format('Y-m-d'));
            $stmt->bindValue('reporting_month',$reportingMonth->format('Y-m-d'));
//            $stmt->bindValue('reporting_month',(new \DateTime('now'))->format('Y-m-d'));
            $stmt->bindValue('total_stocked_chicks_pcs',$report['total_stocked_chicks_pcs']);
            $stmt->bindValue('total_feed_used_kg',$report['total_feed_used_kg']);
            $stmt->bindValue('age_days',$report['age_days']);
            $stmt->bindValue('total_broiler_weight_kg',$report['total_broiler_weight_kg']);
            $stmt->bindValue('mortality',$report['mortality']);
            $stmt->bindValue('fcr',$report['fcr']);
            $stmt->bindValue('remarks',$report['remarks']);
            $stmt->bindValue('created_at',$createdAt->format('Y-m-d H:i:s'));
            $stmt->bindValue('medicine_total_cost',0.00);
            $stmt->bindValue('vaccine_total_cost',0.00);

            $stmt->execute();
        }
    }
    private function processCostBenefitAnalysis($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $sql = "INSERT INTO `crm_cost_benefit_analysis_for_less_costing_farm`(`report_id`, `report_parent_parent_id`, `agent_id`, `customer_id`, `employee_id`, `hatchery_id`, `breed_id`, `feed_id`, `hatching_date`, `total_stocked_chicks_pcs`, `total_feed_used_kg`, `total_broiler_weight_kg`, `mortality`, `remarks`, `created_at`, `species_id`, `reporting_month`, `pond_size`, `fingerling_size`, `harvesting_size`, `age_days`, `fcr`, `item_price_per_pcs`, `feed_price_per_kg`, `broiler_or_fish_price_per_kg`, `total_medicine_cost`, `total_vaccine_cost`, `total_pond_preparation_cost`, `used_bag_price_per_pcs`, `litter_or_pond_rent_cost`, `electricity_and_fuel_cost`, `labour_cost`, `transport_cost`, `other_cost`) 

VALUES (:report_id, :report_parent_parent_id, :agent_id, :customer_id, :employee_id, :hatchery_id, :breed_id, :feed_id, :hatching_date, :total_stocked_chicks_pcs, :total_feed_used_kg, :total_broiler_weight_kg, :mortality, :remarks, :created_at, :species_id, :reporting_month, :pond_size, :fingerling_size, :harvesting_size, :age_days, :fcr, :item_price_per_pcs, :feed_price_per_kg, :broiler_or_fish_price_per_kg, :total_medicine_cost, :total_vaccine_cost, :total_pond_preparation_cost, :used_bag_price_per_pcs, :litter_or_pond_rent_cost, :electricity_and_fuel_cost, :labour_cost, :transport_cost, :other_cost)";

            $hatchingDate = new \DateTime($report['hatching_date']);
            $createdAt = new \DateTime($report['created_at']);
            $reportingMonth = new \DateTime($report['reporting_month']);

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('report_id', $report['report_id']);
            $stmt->bindValue('report_parent_parent_id', $report['report_parent_parent_id']);
            $stmt->bindValue('agent_id', $report['agent_id']);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('hatchery_id', $report['hatchery_id']);
            $stmt->bindValue('breed_id', $report['breed_id']);
            $stmt->bindValue('feed_id', $report['feed_id']);
            $stmt->bindValue('hatching_date', $hatchingDate->format('Y-m-d'));
            $stmt->bindValue('total_stocked_chicks_pcs', $report['total_stocked_chicks_pcs']);
            $stmt->bindValue('total_feed_used_kg', $report['total_feed_used_kg']);
            $stmt->bindValue('total_broiler_weight_kg', $report['total_broiler_weight_kg']);
            $stmt->bindValue('mortality', $report['mortality']);
            $stmt->bindValue('remarks', $report['remarks']);
            $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
            $stmt->bindValue('species_id', $report['species_id']);
            $stmt->bindValue('reporting_month', $reportingMonth->format('Y-m-d'));
            $stmt->bindValue('pond_size', $report['pond_size']);
            if ($report['fingerling_size'] === null){
                $stmt->bindValue('fingerling_size', 0);
            }else{
                $stmt->bindValue('fingerling_size', $report['fingerling_size']);
            }
            if ($report['harvesting_size'] === null){
                $stmt->bindValue('harvesting_size', 0);
            }else{
                $stmt->bindValue('harvesting_size', $report['harvesting_size']);

            }
            $stmt->bindValue('age_days', $report['age_days']);
            $stmt->bindValue('fcr', $report['fcr']);
            $stmt->bindValue('item_price_per_pcs', $report['item_price_per_pcs']);
            $stmt->bindValue('feed_price_per_kg', $report['feed_price_per_kg']);
            $stmt->bindValue('broiler_or_fish_price_per_kg', $report['broiler_or_fish_price_per_kg']);
            $stmt->bindValue('total_medicine_cost', $report['total_medicine_cost']);
            $stmt->bindValue('total_vaccine_cost', $report['total_vaccine_cost']);
            if ($report['total_pond_preparation_cost'] === null){
                $stmt->bindValue('total_pond_preparation_cost', 0);
            }else{
                $stmt->bindValue('total_pond_preparation_cost', $report['total_pond_preparation_cost']);
            }
            if ($report['used_bag_price_per_pcs']){
                $stmt->bindValue('used_bag_price_per_pcs', $report['used_bag_price_per_pcs']);
            }else{
                $stmt->bindValue('used_bag_price_per_pcs', 0);
            }
            $stmt->bindValue('litter_or_pond_rent_cost', $report['litter_or_pond_rent_cost']);
            $stmt->bindValue('electricity_and_fuel_cost', $report['electricity_and_fuel_cost']);
            $stmt->bindValue('labour_cost', $report['labour_cost']);
            $stmt->bindValue('transport_cost', $report['transport_cost']);
            $stmt->bindValue('other_cost', $report['other_cost']);

            $stmt->execute();
        }
    }
    private function processDiseaseMapping($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $sql = "INSERT INTO `crm_disease_mapping`(`report_id`, `agent_id`, `customer_id`, `employee_id`, `hatchery_id`, `farm_type_id`, `feed_id`, `disease_id`, `visiting_date`, `flock_size_or_capacity`, `age_days`, `remarks`, `created_at`) VALUES (:report_id, :agent_id, :customer_id, :employee_id, :hatchery_id, :farm_type_id, :feed_id, :disease_id, :visiting_date, :flock_size_or_capacity, :age_days, :remarks, :created_at)";

            $visitingDate = new \DateTime($report['visiting_date']);
            $createdAt = new \DateTime($report['created_at']);

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('report_id', $report['report_id']);
            $stmt->bindValue('agent_id', $report['agent_id']);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('hatchery_id', $report['hatchery_id']);
            $stmt->bindValue('farm_type_id', $report['farm_type_id']);
            $stmt->bindValue('feed_id', $report['feed_id']);
            $stmt->bindValue('disease_id', $report['disease_id']);
            $stmt->bindValue('visiting_date', $visitingDate->format('Y-m-d'));
            if ($report['flock_size_or_capacity']){
                $stmt->bindValue('flock_size_or_capacity', $report['flock_size_or_capacity']);
            }else{
                $stmt->bindValue('flock_size_or_capacity', 0);
            }
            $stmt->bindValue('age_days', $report['age_days']);
            $stmt->bindValue('remarks', $report['remarks']);
            $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));

            $stmt->execute();
        }
    }
    private function processComplain($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $sql = "INSERT INTO `crm_complain_different_product`(`report_id`, `agent_id`, `customer_id`, `employee_id`, `product_name_id`, `complains`, `created_at`) VALUES (:report_id, :agent_id, :customer_id, :employee_id, :product_name_id, :complains, :created_at)";

//            $createdAt = new \DateTime($report['created_at']);
            $createdAt = new \DateTime('01-' . $report['created_at']);

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('report_id', $report['report_id']);
            $stmt->bindValue('agent_id', $report['agent_id']);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('product_name_id', $report['product_name_id']);
            $stmt->bindValue('complains', $report['complains']);
            $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
            $stmt->bindValue('report_id', $report['report_id']);

            $stmt->execute();
        }
    }
    private function processBroilerLifeCycle($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $findFarmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($report['customer_id']);
            $findEmployee = $this->getDoctrine()->getRepository(User::class)->find($report['employee_id']);
            $findReport = $this->getDoctrine()->getRepository(Setting::class)->find($report['report_id']);

            $findLifeCycle = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->findOneBy(['customer' => $findFarmer, 'employee' => $findEmployee, 'report' => $findReport]);

            if ($findLifeCycle){
                $findLifeCycle->setLifeCycleState($report['life_cycle_state']);
                $this->getDoctrine()->getManager()->persist($findLifeCycle);
                $this->getDoctrine()->getManager()->flush();
            }else{
                $sql = "INSERT INTO `crm_chick_life_cycle`(`hatching_date`, `remarks`, `reporting_date`, `customer_id`, `agent_id`, `employee_id`, `report_id`, `life_cycle_state`, `created_at`, `hatchery_id`, `breed_id`, `feed_id`, `total_birds`, `app_batch_id`) 
VALUES (:hatching_date, :remarks, :reporting_date, :customer_id, :agent_id, :employee_id, :report_id, :life_cycle_state, :created_at, :hatchery_id, :breed_id, :feed_id, :total_birds, :app_batch_id)";

                $hatchingDate = new \DateTime($report['hatching_date']);
                $reportingDate = new \DateTime($report['reporting_date']);
                $createdAt = new \DateTime($report['created_at']);

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('hatching_date', $hatchingDate->format('Y-m-d'));
                $stmt->bindValue('remarks', $report['remarks']);
                $stmt->bindValue('reporting_date', $reportingDate->format('Y-m-d'));
                $stmt->bindValue('customer_id', $report['customer_id']);
                $stmt->bindValue('agent_id', $report['agent_id']);
                $stmt->bindValue('employee_id', $report['employee_id']);
                $stmt->bindValue('report_id', $report['report_id']);
                $stmt->bindValue('life_cycle_state', $report['life_cycle_state']);
//                $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('created_at', (new \DateTime('now'))->format('Y-m-d H:i:s'));
                $stmt->bindValue('hatchery_id', $report['hatchery_id']);
                $stmt->bindValue('breed_id', $report['breed_id']);
                $stmt->bindValue('feed_id', $report['feed_id']);
                $stmt->bindValue('total_birds', $report['total_birds']);
                $stmt->bindValue('app_batch_id', $batch->getId());

                $stmt->execute();
            }
        }
    }
    private function processBroilerLifeCycleDetail($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $sql = "SELECT id
FROM `crm_chick_life_cycle`
WHERE `customer_id` = :customer_id AND `employee_id` = :employee_id AND `report_id` = :report_id";
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('report_id', $report['report_id']);

//            $stmt->bindValue('customer_id', 44);
//            $stmt->bindValue('employee_id', 23);
//            $stmt->bindValue('report_id', 39);
            $stmt->execute();
            $lifeCycleId = $stmt->fetch()['id'];
            if ($lifeCycleId) {
                $sql = "INSERT INTO `crm_chick_life_cycle_details`(`crm_chick_life_cycle_id`, `visiting_week`, `age_days`, `mortality_pes`, `mortality_percent`, `weight_standard`, `weight_achieved`, `feed_total_kg`, `per_bird`, `feed_standard`, `without_mortality`, `with_mortality`, `pro_date`, `batch_no`, `remarks`, `created_at`, `updated_at`, `feed_type_id`, `reporting_date`) 
VALUES (:crm_chick_life_cycle_id, :visiting_week, :age_days, :mortality_pes, :mortality_percent, :weight_standard, :weight_achieved, :feed_total_kg, :per_bird, :feed_standard, :without_mortality, :with_mortality, :pro_date, :batch_no, :remarks, :created_at, :updated_at, :feed_type_id, :reporting_date)";

                $proDate = new \DateTime($report['pro_date']);
                $reportingDate = new \DateTime($report['reporting_date']);
                $createdAt = new \DateTime($report['created_at']);
                $updatedAt = new \DateTime($report['updated_at']);

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('crm_chick_life_cycle_id', $lifeCycleId);
                $stmt->bindValue('visiting_week', $report['visiting_week']);
                $stmt->bindValue('age_days', $report['age_days']);
                $stmt->bindValue('mortality_pes', $report['mortality_pes']);
                $stmt->bindValue('mortality_percent', $report['mortality_percent']);
                $stmt->bindValue('weight_standard', $report['weight_standard']);
                $stmt->bindValue('weight_achieved', $report['weight_achieved']);
                $stmt->bindValue('feed_total_kg', $report['feed_total_kg']);
                $stmt->bindValue('per_bird', $report['per_bird']);
                $stmt->bindValue('feed_standard', $report['feed_standard']);
                $stmt->bindValue('without_mortality', $report['without_mortality']);
                $stmt->bindValue('with_mortality', $report['with_mortality']);
                $stmt->bindValue('pro_date', $proDate->format('Y-m-d H:i:s'));
                $stmt->bindValue('batch_no', $report['batch_no']);
                $stmt->bindValue('remarks', $report['remarks']);
                $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('updated_at', $updatedAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('feed_type_id', $report['feed_type_id']);
                $stmt->bindValue('reporting_date', $reportingDate->format('Y-m-d'));

                $stmt->execute();
            }
        }
    }
    private function processCattleLifeCycle($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $findFarmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($report['customer_id']);
            $findEmployee = $this->getDoctrine()->getRepository(User::class)->find($report['employee_id']);
            $findReport = $this->getDoctrine()->getRepository(Setting::class)->find($report['report_id']);

            $findLifeCycle = $this->getDoctrine()->getRepository(CattleLifeCycle::class)->findOneBy(['customer' => $findFarmer, 'employee' => $findEmployee, 'report' => $findReport]);

            if ($findLifeCycle){
                $findLifeCycle->setLifeCycleState($report['life_cycle_state']);
                $this->getDoctrine()->getManager()->persist($findLifeCycle);
                $this->getDoctrine()->getManager()->flush();
            }else{
                $sql = "INSERT INTO `crm_cattle_life_cycle`(`customer_id`, `report_id`, `agent_id`, `employee_id`, `reporting_date`, `breed_type`, `life_cycle_state`, `remarks`, `created_at`, `feed_type`, `app_batch_id`) 
VALUES (:customer_id, :report_id, :agent_id, :employee_id, :reporting_date, :breed_type, :life_cycle_state, :remarks, :created_at, :feed_type, :app_batch_id)";
                $reportingDate = new \DateTime($report['reporting_date']);
                $createdAt = new \DateTime($report['created_at']);

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('customer_id', $report['customer_id']);
                $stmt->bindValue('report_id', $report['report_id']);
                $stmt->bindValue('agent_id', $report['agent_id']);
                $stmt->bindValue('employee_id', $report['employee_id']);
                $stmt->bindValue('reporting_date', $reportingDate->format('Y-m-d'));
                $stmt->bindValue('breed_type', $report['breed_type']);
                $stmt->bindValue('life_cycle_state', $report['life_cycle_state']);
                $stmt->bindValue('remarks', $report['remarks']);
                $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('feed_type', $report['feed_type']);
                $stmt->bindValue('app_batch_id', $batch->getId());

                $stmt->execute();
            }
        }
    }
    private function processCattleLifeCycleDetail($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $sql = "SELECT id
FROM `crm_cattle_life_cycle`
WHERE `customer_id` = :customer_id AND `employee_id` = :employee_id AND `report_id` = :report_id";
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('report_id', $report['report_id']);

//            $stmt->bindValue('customer_id', 44);
//            $stmt->bindValue('employee_id', 23);
//            $stmt->bindValue('report_id', 39);
            $stmt->execute();
            $lifeCycleId = $stmt->fetch()['id'];

            if ($lifeCycleId){
                $sql = "INSERT INTO `crm_cattle_life_cycle_details`(`crm_cattle_life_cycle_id`, `visiting_date`, `age_of_cattle_month`, `previous_body_weight`, `present_body_weight`, `body_weight_difference`, `duration_of_bwt_difference`, `lactation_no`, `age_of_lactation`, `average_weight_per_day`, `average_weight_per_kg_consumption_feed`, `average_weight_per_kg_dm`, `milk_fat_percentage`, `consumption_feed_intake_ready_feed`, `consumption_feed_intake_conventional`, `consumption_feed_intake_total`, `fodder_green_grass_kg`, `fodder_straw_kg`, `dm_of_fodder_green_grass_kg`, `dm_of_fodder_straw_kg`, `total_dm_kg`, `dm_requirement_by_bwt_kg`, `remarks`, `created_at`, `updated_at`) 
VALUES (:crm_cattle_life_cycle_id, :visiting_date, :age_of_cattle_month, :previous_body_weight, :present_body_weight, :body_weight_difference, :duration_of_bwt_difference, :lactation_no, :age_of_lactation, :average_weight_per_day, :average_weight_per_kg_consumption_feed, :average_weight_per_kg_dm, :milk_fat_percentage, :consumption_feed_intake_ready_feed, :consumption_feed_intake_conventional, :consumption_feed_intake_total, :fodder_green_grass_kg, :fodder_straw_kg, :dm_of_fodder_green_grass_kg, :dm_of_fodder_straw_kg, :total_dm_kg, :dm_requirement_by_bwt_kg, :remarks, :created_at, :updated_at)";

                $visitingDate = new \DateTime($report['visiting_date']);
                $createdAt = new \DateTime($report['created_at']);
                $updatedAt = new \DateTime($report['updated_at']);

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('crm_cattle_life_cycle_id', $lifeCycleId);
                $stmt->bindValue('visiting_date', $visitingDate->format('Y-m-d'));
                $stmt->bindValue('age_of_cattle_month', $report['age_of_cattle_month']);
                if ($report['previous_body_weight'] === null){
                    $stmt->bindValue('previous_body_weight', 0);
                }else{
                    $stmt->bindValue('previous_body_weight', $report['previous_body_weight']);
                }
                $stmt->bindValue('present_body_weight', $report['present_body_weight']);
                if ($report['body_weight_difference'] === null){
                    $stmt->bindValue('body_weight_difference', 0);
                }else{
                    $stmt->bindValue('body_weight_difference', $report['body_weight_difference']);
                }
                if ($report['duration_of_bwt_difference'] === null){
                    $stmt->bindValue('duration_of_bwt_difference', 0);
                }else{
                    $stmt->bindValue('duration_of_bwt_difference', $report['duration_of_bwt_difference']);
                }
                if ($report['lactation_no'] === null){
                    $stmt->bindValue('lactation_no', 0);
                }else{
                    $stmt->bindValue('lactation_no', $report['lactation_no']);
                }
                if ($report['age_of_lactation'] === null){
                    $stmt->bindValue('age_of_lactation', 0);
                }else{
                    $stmt->bindValue('age_of_lactation', $report['age_of_lactation']);
                }
                $stmt->bindValue('average_weight_per_day', $report['average_weight_per_day']);
                $stmt->bindValue('average_weight_per_kg_consumption_feed', $report['average_weight_per_kg_consumption_feed']);
                $stmt->bindValue('average_weight_per_kg_dm', $report['average_weight_per_kg_dm']);
                if ($report['milk_fat_percentage'] === null){
                    $stmt->bindValue('milk_fat_percentage', 0);
                }else{
                    $stmt->bindValue('milk_fat_percentage', $report['milk_fat_percentage']);
                }
                $stmt->bindValue('consumption_feed_intake_ready_feed', $report['consumption_feed_intake_ready_feed']);
                $stmt->bindValue('consumption_feed_intake_conventional', $report['consumption_feed_intake_conventional']);
                $stmt->bindValue('consumption_feed_intake_total', $report['consumption_feed_intake_total']);
                $stmt->bindValue('fodder_green_grass_kg', $report['fodder_green_grass_kg']);
                $stmt->bindValue('fodder_straw_kg', $report['fodder_straw_kg']);
                $stmt->bindValue('dm_of_fodder_green_grass_kg', $report['dm_of_fodder_green_grass_kg']);
                $stmt->bindValue('dm_of_fodder_straw_kg', $report['dm_of_fodder_straw_kg']);
                $stmt->bindValue('total_dm_kg', $report['total_dm_kg']);
                $stmt->bindValue('dm_requirement_by_bwt_kg', $report['dm_requirement_by_bwt_kg']);
                $stmt->bindValue('remarks', $report['remarks']);
                $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('updated_at', $updatedAt->format('Y-m-d H:i:s'));

                $stmt->execute();
            }
        }
    }
    private function processLayerLifeCycle($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $findFarmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($report['customer_id']);
            $findEmployee = $this->getDoctrine()->getRepository(User::class)->find($report['employee_id']);
            $findReport = $this->getDoctrine()->getRepository(Setting::class)->find($report['report_id']);

            $findLifeCycle = $this->getDoctrine()->getRepository(CattleLifeCycle::class)->findOneBy(['customer' => $findFarmer, 'employee' => $findEmployee, 'report' => $findReport]);

            if ($findLifeCycle){
                $findLifeCycle->setLifeCycleState($report['life_cycle_state']);
                $this->getDoctrine()->getManager()->persist($findLifeCycle);
                $this->getDoctrine()->getManager()->flush();
            }else{
                $sql = "INSERT INTO `crm_layer_life_cycle`(`total_birds`, `hatchery_date`, `created`, `updated`, `customer_id`, `employee_id`, `report_id`, `agent_id`, `life_cycle_state`, `hatchery_id`, `breed_id`, `feed_id`, `app_batch_id`) 
VALUES (:total_birds, :hatchery_date, :created, :updated, :customer_id, :employee_id, :report_id, :agent_id, :life_cycle_state, :hatchery_id, :breed_id, :feed_id, :app_batch_id)";

                $hatchingDate = new \DateTime($report['hatchery_date']);
                $createdAt = new \DateTime($report['created']);
                $updatedAt = new \DateTime($report['updated']);

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('total_birds', $report['total_birds']);
                $stmt->bindValue('hatchery_date', $hatchingDate->format('Y-m-d'));
                $stmt->bindValue('created', $createdAt->format('Y-m-d'));
                $stmt->bindValue('updated', $updatedAt->format('Y-m-d'));
                $stmt->bindValue('customer_id', $report['customer_id']);
                $stmt->bindValue('employee_id', $report['employee_id']);
                $stmt->bindValue('report_id', $report['report_id']);
                $stmt->bindValue('agent_id', $report['agent_id']);
                $stmt->bindValue('life_cycle_state', $report['life_cycle_state']);
                $stmt->bindValue('hatchery_id', $report['hatchery_id']);
                $stmt->bindValue('breed_id', $report['breed_id']);
                $stmt->bindValue('feed_id', $report['feed_id']);
                $stmt->bindValue('app_batch_id', $batch->getId());

                $stmt->execute();
            }
        }
    }
    private function processLayerLifeCycleDetail($reports, Api $batch)
    {
        foreach ($reports as $report) {

            $sql = "SELECT id
FROM `crm_layer_life_cycle`
WHERE `customer_id` = :customer_id AND `employee_id` = :employee_id AND `report_id` = :report_id";
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('report_id', $report['report_id']);

//            $stmt->bindValue('customer_id', 44);
//            $stmt->bindValue('employee_id', 23);
//            $stmt->bindValue('report_id', 47);
            $stmt->execute();
            $lifeCycleId = $stmt->fetch()['id'];
            if ($lifeCycleId){
                $sql = "INSERT INTO `crm_layer_life_cycle_details`(`crm_layer_life_cycle_id`, `visiting_date`, `age_week`, `dead_bird`, `avg_weight`, `target_weight`, `uniformity`, `feed_per_bird`, `target_feed_per_bird`, `total_eggs`, `target_egg_production`, `egg_weight_actual`, `egg_weight_standard`, `production_date`, `batch_no`, `medicine`, `remarks`, `created`, `updated`, `feed_mill_id`, `feed_type_id`) 
VALUES (:crm_layer_life_cycle_id, :visiting_date, :age_week, :dead_bird, :avg_weight, :target_weight, :uniformity, :feed_per_bird, :target_feed_per_bird, :total_eggs, :target_egg_production, :egg_weight_actual, :egg_weight_standard, :production_date, :batch_no, :medicine, :remarks, :created, :updated, :feed_mill_id, :feed_type_id)";

                $visitingDate = new \DateTime($report['visiting_date']);
                $productionDate = new \DateTime($report['production_date']);
                $createdAt = new \DateTime($report['created']);
                $updatedAt = new \DateTime($report['updated']);

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('crm_layer_life_cycle_id', $lifeCycleId);
                $stmt->bindValue('visiting_date', $visitingDate->format('Y-m-d'));
                $stmt->bindValue('age_week', $report['age_week']);
                $stmt->bindValue('dead_bird', $report['dead_bird']);
                $stmt->bindValue('avg_weight', $report['avg_weight']);
                $stmt->bindValue('target_weight', $report['target_weight']);
                $stmt->bindValue('uniformity', $report['uniformity']);
                $stmt->bindValue('feed_per_bird', $report['feed_per_bird']);
                $stmt->bindValue('target_feed_per_bird', $report['target_feed_per_bird']);
                $stmt->bindValue('total_eggs', $report['total_eggs']);
                $stmt->bindValue('target_egg_production', $report['target_egg_production']);
                $stmt->bindValue('egg_weight_actual', $report['egg_weight_actual']);
                $stmt->bindValue('egg_weight_standard', $report['egg_weight_standard']);
                $stmt->bindValue('production_date', $productionDate->format('Y-m-d'));
                $stmt->bindValue('batch_no', $report['batch_no']);
                $stmt->bindValue('medicine', $report['medicine']);
                $stmt->bindValue('remarks', $report['remarks']);
                $stmt->bindValue('created', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('updated', $updatedAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('feed_mill_id', $report['feed_mill_id']);
                $stmt->bindValue('feed_type_id', $report['feed_type_id']);

                $stmt->execute();
            }
        }
    }
    private function processExpense($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appBatch' => $batch, 'appId' => $report['crm_visit_id']]);
            if ($findVisit){
                $sql = "INSERT INTO `crm_expense`(`schedule_visit`, `conveyance`, `daily_allowance`, `hotel_rent`, `photostate`, `courier`, `food`, `mobile`, `maintenace`, `toll_bill`, `service_charge`, `others`, `visiting_area_id`, `crm_visit_id`, `status`, `app_id`) 
VALUES (:schedule_visit, :conveyance, :daily_allowance, :hotel_rent, :photostate, :courier, :food, :mobile, :maintenace, :toll_bill, :service_charge, :others, :visiting_area_id, :crm_visit_id, :status, :app_id)";

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('schedule_visit', $report['schedule_visit']);
                $stmt->bindValue('conveyance', $report['conveyance']);
                $stmt->bindValue('daily_allowance', $report['daily_allowance']);
                $stmt->bindValue('hotel_rent', $report['hotel_rent']);
                $stmt->bindValue('photostate', $report['photostate']);
                $stmt->bindValue('courier', $report['courier']);
                $stmt->bindValue('food', $report['food']);
                $stmt->bindValue('mobile', $report['mobile']);
                $stmt->bindValue('maintenace', $report['maintenace']);
                $stmt->bindValue('toll_bill', $report['toll_bill']);
                $stmt->bindValue('service_charge', $report['service_charge']);
                $stmt->bindValue('others', $report['others']);
                $stmt->bindValue('visiting_area_id', $report['visiting_area_id']);
                $stmt->bindValue('crm_visit_id', $findVisit->getId());
                $stmt->bindValue('status', $report['status']);
                $stmt->bindValue('app_id', $report['id']);

                $stmt->execute();
            }
        }
    }
    private function processExpensePurpose($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $sql = "SELECT crm_expense.id FROM `crm_expense` JOIN `crm_visit` ON crm_visit.id = crm_expense.crm_visit_id WHERE crm_visit.app_batch_id = :batchId AND crm_expense.app_id = :appId";
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('batchId', $batch->getId());
            $stmt->bindValue('appId', $report['expense_id']);
            $stmt->execute();
            $expenseId = $stmt->fetch()['id'];
            if ($expenseId){
                $sql = "INSERT INTO `crm_expence_purpose`(`expense_id`, `setting_id`) VALUES (:expense_id, :setting_id)";
                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('expense_id', $expenseId);
                $stmt->bindValue('setting_id', $report['setting_id']);

                $stmt->execute();
            }
        }
    }
    private function processExpenseVehicle($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $sql = "SELECT crm_expense.id FROM `crm_expense` JOIN `crm_visit` ON crm_visit.id = crm_expense.crm_visit_id WHERE crm_visit.app_batch_id = :batchId AND crm_expense.app_id = :appId";
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('batchId', $batch->getId());
            $stmt->bindValue('appId', $report['expense_id']);
            $stmt->execute();
            $expenseId = $stmt->fetch()['id'];
            if ($expenseId){
                $sql = "INSERT INTO `crm_expence_vehicle`(`expense_id`, `setting_id`) VALUES (:expense_id, :setting_id)";
                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('expense_id', $expenseId);
                $stmt->bindValue('setting_id', $report['setting_id']);

                $stmt->execute();
            }
        }
    }
    private function processDocComplain($reports, Api $batch)
    {
        foreach ($reports as $report) {

            $sql = "INSERT INTO `crm_complain_different_product`(`agent_id`, `employee_id`, `complains`, `created_at`, `transport_id`, `breed_id`, `hatchery_id`, `age_days`, `box_no`, `received_doc_qty`, `observation`) VALUES (:agent_id, :employee_id, :complains, :created_at, :transport_id, :breed_id, :hatchery_id, :age_days, :box_no, :received_doc_qty, :observation)";

            $createdAt = new \DateTime($report['created_at']);

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('agent_id', $report['agent_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('complains', $report['comments']);
            $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
            $stmt->bindValue('transport_id', $report['transport_id']);
            $stmt->bindValue('breed_id', $report['breed_id']);
            $stmt->bindValue('hatchery_id', $report['hatchery_id']);
            $stmt->bindValue('age_days', $report['age_days']);
            $stmt->bindValue('box_no', $report['box_no']);
            $stmt->bindValue('received_doc_qty', $report['received_doc_qty']);
            $stmt->bindValue('observation', $report['observation']);

            if ($stmt->execute()){
                $compailId = $this->getDoctrine()->getConnection()->lastInsertId();
                if ($report['complains']){
                    $complains = json_decode($report['complains'], true);

                    foreach ($complains as $complain) {
                        $sql = "INSERT INTO `crm_complain_different_product_details`(`complain_id`, `complain_parameter_id`, `day`, `quantity`, `created_at`) VALUES (:complain_id, :complain_parameter_id, :day, :quantity, :created_at)";

                        $createdAt = new \DateTime($report['created_at']);

                        $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                        $stmt->bindValue('complain_id', $compailId);
                        $stmt->bindValue('complain_parameter_id', $complain['complain_id']);
                        $stmt->bindValue('day', $complain['days']);
                        $stmt->bindValue('quantity', $complain['qty']);
                        $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                        $stmt->execute();
                    }
                }

            }

        }
    }
    private function processFeedComplain($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $sql = "INSERT INTO `crm_complain_different_product`(`agent_id`,`employee_id`, `complains`, `created_at`, `observation`, `serial_no`, `batch_no`, `feed_mill_id`, `feed_id`, `production_date`) VALUES (:agent_id, :employee_id, :complains, :created_at, :observation, :serial_no, :batch_no, :feed_mill_id, :feed_id, :production_date)";


            $createdAt = new \DateTime($report['created_at']);
            $productionDate = new \DateTime($report['production_date']);

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('agent_id', $report['agent_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('complains', $report['comments']);
            $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
            $stmt->bindValue('observation', $report['observation']);
            $stmt->bindValue('serial_no', $report['serial_no']);
            $stmt->bindValue('batch_no', $report['batch_no']);
            $stmt->bindValue('feed_mill_id', $report['feed_mill_id']);
            $stmt->bindValue('feed_id', $report['feed_id']);
            $stmt->bindValue('production_date', $productionDate->format('Y-m-d'));

            if ($stmt->execute()){
                $compailId = $this->getDoctrine()->getConnection()->lastInsertId();
                if ($report['abnormalities']){
                    $complains = json_decode($report['abnormalities'], true);

                    foreach ($complains as $complain) {
                        $sql = "INSERT INTO `crm_complain_different_product_details`(`complain_id`, `complain_parameter_id`) VALUES (:complain_id, :complain_parameter_id)";

                        $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                        $stmt->bindValue('complain_id', $compailId);
                        $stmt->bindValue('complain_parameter_id', $complain['id']);

                        $stmt->execute();
                    }
                }

            }
        }
    }
    private function processFarmer($farmers, Api $batch)
    {
        foreach ($farmers as $farmer) {
            $sql = "INSERT INTO `crm_customers`(`name`, `mobile`, `address`, `agent_id`, `custom_group_id`, `location_id`, `created`) VALUES (:name, :mobile, :address, :agent_id, :custom_group_id, :location_id, :created)";
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('name', $farmer['name']);
            $stmt->bindValue('mobile', $farmer['mobile']);
            $stmt->bindValue('address', $farmer['address']);
            $stmt->bindValue('custom_group_id', 8);
            $stmt->bindValue('location_id', $farmer['location_id']);
            if ($farmer['agentId'] == null) {
                if ($farmer['subAgentId'] != null) {
                    $stmt->bindValue('agent_id', $farmer['subAgentId']);
                }elseif ($farmer['otherAgentId'] != null) {
                    $stmt->bindValue('agent_id', $farmer['otherAgentId']);
                }
            }else{
                $stmt->bindValue('agent_id', $farmer['agentId']);
            }
            $stmt->bindValue('created', ($batch->getCreatedAt())->format('Y-m-d H:i:s'));

            if ($stmt->execute()) {
                $farmerId = $this->getDoctrine()->getConnection()->lastInsertId();
                $sql = "INSERT INTO `crm_customer_introduce_details`(`agent_id`, `customer_id`, `employee_id`, `culture_species_item_and_qty`, `created_at`, `farmerType`, `other_agent_id`, `other_feed_id`, `sub_agent_id`, `introduce_date`) VALUES (:agent_id, :customer_id, :employee_id, :culture_species_item_and_qty, :created_at, :farmerType, :other_agent_id, :other_feed_id, :sub_agent_id, :introduce_date)";

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('customer_id', $farmerId);
                $stmt->bindValue('employee_id', $batch->getEmployee()->getId());
                $stmt->bindValue('culture_species_item_and_qty', $farmer['culture_species_item_and_qty']);
                $stmt->bindValue('created_at', ($batch->getCreatedAt())->format('Y-m-d H:i:s'));
                $stmt->bindValue('farmerType', $farmer['farmerType']);
                $stmt->bindValue('other_agent_id', $farmer['otherAgentId']);
                $stmt->bindValue('other_feed_id', $farmer['other_feed_id']);
                $stmt->bindValue('sub_agent_id', $farmer['subAgentId']);
                if ($farmer['agentId'] == null){
                    if ($farmer['subAgentId'] != null) {
                        $stmt->bindValue('agent_id', $farmer['subAgentId']);
                    }elseif ($farmer['otherAgentId'] != null) {
                        $stmt->bindValue('agent_id', $farmer['otherAgentId']);
                    }
                    $stmt->bindValue('introduce_date', null);
                }else{
                    $stmt->bindValue('agent_id', $farmer['agentId']);
                    $stmt->bindValue('introduce_date', ($batch->getCreatedAt())->format('Y-m-d H:i:s'));
                }
                $stmt->execute();
            }
        }
    }
    private function processFarmerIntroduce($farmers, Api $batch)
    {
        foreach ($farmers as $farmer) {
            $updateFarmer = "UPDATE `crm_customers` SET `updated`= :updated,`agent_id`= :agent_id WHERE id = :id";
            $updateFarmerStmt = $this->getDoctrine()->getConnection()->prepare($updateFarmer);
            $updateFarmerStmt->bindValue('agent_id', $farmer['agent_id']);
            $updateFarmerStmt->bindValue('id', $farmer['customer_id']);

            if ($farmer['created_at']){
                $updateFarmerStmt->bindValue('updated', (new \DateTime($farmer['created_at']))->format('Y-m-d H:i:s'));
            }
            $updateFarmerStmt->execute();

            $sql = "UPDATE `crm_customer_introduce_details` SET `agent_id`= :agentId,`culture_species_item_and_qty`=:culture_species_item_and_qty,`remarks`=:remarks,`feed_id`= :feed_id,`introduce_date`= :introduce_date WHERE customer_id = :farmerId";
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('farmerId', $farmer['customer_id']);
            $stmt->bindValue('agentId', $farmer['agent_id']);
            $stmt->bindValue('culture_species_item_and_qty', $farmer['culture_species_item_and_qty']);
            $stmt->bindValue('remarks', $farmer['remarks']);
            if ($farmer['created_at']){
                $stmt->bindValue('introduce_date', (new \DateTime($farmer['created_at']))->format('Y-m-d H:i:s'));
            }
            if ($farmer['feed_id'] == 1){
                $stmt->bindValue('feed_id', 55);
            }
            $stmt->execute();
        }
    }
}
