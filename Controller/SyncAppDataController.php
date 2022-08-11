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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\AgentUpgradationReport;
use Terminalbd\CrmBundle\Entity\Api;
use Terminalbd\CrmBundle\Entity\ApiDetails;
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\CattleLifeCycle;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Entity\ChickLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\CompanyWiseFeedSale;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\DailyChickPrice;
use Terminalbd\CrmBundle\Entity\DailyChickPriceDetails;
use Terminalbd\CrmBundle\Entity\FarmerTrainingReport;
use Terminalbd\CrmBundle\Entity\FcrDifferentCompanies;
use Terminalbd\CrmBundle\Entity\FishCompanyAndSpeciesWiseAverageFcr;
use Terminalbd\CrmBundle\Entity\FishLifeCycle;
use Terminalbd\CrmBundle\Entity\FishLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\LabService;
use Terminalbd\CrmBundle\Entity\LayerLifeCycle;
use Terminalbd\CrmBundle\Entity\LayerLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\NewFarmerIntroduce\FarmerIntroduceDetails;
use Terminalbd\CrmBundle\Entity\PoultryMeatEggPrice;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Entity\SonaliStandard;


/**
 * Class SyncAppDataController
 * @package Terminalbd\CrmBundle\Controller
 * @Route("/crm/sync-app-data", name="crm_sync_app_data")
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_CRM_SALES_MARKETING_ADMIN') or is_granted('ROLE_DEVELOPER')")
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
        $records = $this->getDoctrine()->getRepository(Api::class)->getUnprocessData($this->getUser());
//        $records = $this->pagination($request, $records);

        return $this->render('@TerminalbdCrm/api/api-response-list.html.twig',[
            'records' => $records,
        ]);
    }

    /**
     * @Route("/sync/{id}", name="_sync", defaults={"id" = null})
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function syncAppData($id)
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $em = $this->getDoctrine()->getManager();

        if ($id){
            $batches = $this->getDoctrine()->getRepository(Api::class)->findBy(['id' => $id, 'status' => false]);
        }else{
            $batches = $this->getDoctrine()->getRepository(Api::class)->getUnprocessData($this->getUser());
        }

        foreach ($batches as $batch) {
            /*$findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appBatch' => $batch]);
            if (!$findVisit){*/
//                $details = $batch->getApiDetails();
                $details = $this->getDoctrine()->getRepository(ApiDetails::class)->findBy(['batch' => $batch,'status' => 0]);
                $detailsArrayLength = sizeof($details);
                $loopCount = 0;
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
                        case "crm_cattle_farm_visit_details":
                            $this->processCattleFarmVisitDetails($jsonToArray, $batch);
                            break;
                        case "crm_poultry_meat_egg_price":
                            $this->processPoultryMeatEggPrice($jsonToArray, $batch);
                            break;
                        case "crm_company_wise_feed_sale":
                            $this->processCompanyWiseFeedSale($jsonToArray, $batch);
                            break;
                        case "crm_fcr_different_companies":
                            $this->processFcrDifferentCompanies($jsonToArray, $batch);
                            break;
                        case "crm_lab_services":
                            $this->processLabServices($jsonToArray, $batch);
                            break;
                        case "crm_fish_sales_price":
                            $this->processFishSalesPrice($jsonToArray, $batch);
                            break;
                        case "crm_fish_tilapia_fry_sales":
                            $this->processFishTilapiaFrySales($jsonToArray, $batch);
                            break;
                        case "crm_fish_company_species_wise_average_fcr":
                            $this->processFishCompanySpeciesWiseAverageFcr($jsonToArray, $batch);
                            break;
                        case "crm_fish_company_species_wise_average_fcr_details":
                            $this->processFishCompanySpeciesWiseAverageFcrDetails($jsonToArray, $batch);
                            break;
                        case "crm_doc_price":
                            $this->processDocPrice($jsonToArray, $batch);
                            break;
                        case "crm_fish_life_cycle":
                            $this->processFishLifeCycle($jsonToArray, $batch);
                            break;
                        case "crm_fish_life_cycle_details":
                            $this->processFishLifeCycleDetails($jsonToArray, $batch);
                            break;
                        case "crm_fish_life_cycle_detail_species":
                            $this->processFishLifeCycleDetailsSpecies($jsonToArray, $batch);
                            break;
                        case "crm_agent_upgradation_report":
                            $this->processAgentUpgradtion($jsonToArray, $batch);
                            break;
                        case "crm_farmer_training_report":
                            $this->processFarmerTraining($jsonToArray, $batch);
                            break;
                        case "crm_farmer_training_report_details":
                            $this->processFarmerTrainingDetails($jsonToArray, $batch);
                            break;
                    }
                    $detail->setStatus(true);
                    $em->persist($detail);
                    $em->flush();

                    $loopCount+=1;
                }
//            }
            if($detailsArrayLength==$loopCount){
                $batch->setStatus(true);
                $em->persist($batch);
                $em->flush();
            }
        }
        $this->addFlash('success', 'Synchronization Completed!');
        return $this->redirectToRoute('crm_sync_app_data_index');
    }


    private function processVisit($visits, Api $batch)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($visits as $visitKey => $visit) {

            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appBatch' => $batch, 'appId' => $visit['id']]);
            if (!$findVisit){
            
            $createdAt = new \DateTime($visit['created_at']);
            $findEmployee = $this->getDoctrine()->getRepository(User::class)->find($visit['employee_id']);
            $findLocation = $visit['location_id'] ? $this->getDoctrine()->getRepository(Location::class)->find($visit['location_id']) : null;
            $mode = $visit['modeId'] ? $this->getDoctrine()->getRepository(Setting::class)->find((int)$visit['modeId']) : null;

            if ($findEmployee){
                $newVisit = new CrmVisit();
                $newVisit->setEmployee($findEmployee);
                $newVisit->setAppId($visit['id']);
                $newVisit->setAppBatch($batch);
                if ($findLocation){
                    $newVisit->setLocation($findLocation);
                }
                $newVisit->setWorkingDuration($visit['duration_from']);
                $newVisit->setWorkingDurationTo($visit['duration_to']);
                $newVisit->setCreated($createdAt);
                $newVisit->setWorkingMode($mode);
                $newVisit->setRemarks($visit['remarks']);

                $em->persist($newVisit);
                $em->flush();
            }
            
            }
        }
    }
    private function processVisitDetail($visitDetails, Api $batch)
    {
        foreach ($visitDetails as $visitDetail) {

            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $visitDetail['crm_visit_id'], 'appBatch' => $batch]);
            if ($findVisit){

                $purposeMultipleJson= json_decode($visitDetail['purposeName']);
                $purposeMultiple=[];
                if($purposeMultipleJson){
                    foreach ($purposeMultipleJson as $value){
                        $purposeMultiple[$value->id]=$value->name;
                    }
                }

                $sql = "INSERT INTO `crm_visit_details`(`crm_visit_id`, `farmCapacity`, `updated`, `comments`, `created`, `customer_id`, `process`, `agent_id`, `purpose_id`, `firm_type_id`, `report_id`, `purpose_multiple`)
VALUES (:crm_visit_id, :farmCapacity, :updated, :comments, :created, :customer_id, :process, :agent_id, :purpose_id, :firm_type_id, :report_id, :purpose_multiple)";

                $createdAt = new \DateTime($visitDetail['created']);
                $updatedAt = new \DateTime($visitDetail['updated']);

                if ($visitDetail['agent_id'] == 0){
                    $visitDetail['agent_id'] = NULL;
                }
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
                $stmt->bindValue('purpose_multiple', json_encode($purposeMultiple));

                $stmt->execute();
            }
        }
    }
    private function processLayerPerformance($performances, Api $batch)
    {
        foreach ($performances as $performance) {
            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $performance['crm_visit_id'], 'appBatch' => $batch]);
            if ($findVisit){

                $deleteSql = "DELETE FROM `crm_layer_performance_details` WHERE `app_batch_id`= :app_batch_id AND `app_id`= :app_id";
                $stmtDelete = $this->getDoctrine()->getConnection()->prepare($deleteSql);
                $stmtDelete->bindValue('app_batch_id', $batch->getId());
                $stmtDelete->bindValue('app_id', $performance['id']);
                $stmtDelete->execute();

                $sql = "INSERT INTO 
`crm_layer_performance_details`
(`employee_id`, `report_id`, `agent_id`, `customer_id`, `hatchery_id`, `breed_id`, `feed_id`, `feed_mill_id`, `feed_type_id`, `color_id`, `repoting_month`, `total_birds`, `age_week`, `bird_weight_achieved`, `bird_weight_target`, `feed_intake_per_bird`, `feed_Target`, `egg_production_achieved`, `egg_production_target`, `egg_weight_achieved`, `egg_weight_stand`, `production_date`, `batch_no`, `disease`, `remarks`, `created`, `updated`, `visit_id`, `app_batch_id`, `app_id`) VALUES 
(:employee_id, :report_id, :agent_id, :customer_id, :hatchery_id, :breed_id, :feed_id, :feed_mill_id, :feed_type_id, :color_id, :repoting_month, :total_birds, :age_week, :bird_weight_achieved, :bird_weight_target, :feed_intake_per_bird, :feed_Target, :egg_production_achieved, :egg_production_target, :egg_weight_achieved, :egg_weight_stand, :production_date, :batch_no, :disease, :remarks, :created, :updated, :visit_id, :app_batch_id, :app_id)";

                $repotingMonth = new \DateTime($performance['repoting_month']?$performance['repoting_month']:$performance['created']);
                $createdAt = new \DateTime($performance['created']);
                $updatedAt = new \DateTime($performance['updated']);
                $productionDate = $performance['production_date']? new \DateTime($performance['production_date']):'';

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
                $stmt->bindValue('bird_weight_achieved', $performance['bird_weight_achieved']!=""?$performance['bird_weight_achieved']:0);
                $stmt->bindValue('bird_weight_target', $performance['bird_weight_target']);
                $stmt->bindValue('feed_intake_per_bird', $performance['feed_intake_per_bird']);
                $stmt->bindValue('feed_Target', $performance['feed_Target']);
                $stmt->bindValue('egg_production_achieved', $performance['egg_production_achieved']!=""?$performance['egg_production_achieved']:0);
                $stmt->bindValue('egg_production_target', $performance['egg_production_target']);
                $stmt->bindValue('egg_weight_achieved', $performance['egg_weight_achieved']!=""?$performance['egg_weight_achieved']:0);
                $stmt->bindValue('egg_weight_stand', $performance['egg_weight_stand']);
                $stmt->bindValue('production_date', $productionDate!=""?$productionDate->format('Y-m-d'):null);
                $stmt->bindValue('batch_no', $performance['batch_no']);
                $stmt->bindValue('disease', $performance['disease']);
                $stmt->bindValue('remarks', $performance['remarks']);
                $stmt->bindValue('created', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('updated', $updatedAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('visit_id', $findVisit->getId());
                $stmt->bindValue('app_batch_id', $batch->getId());
                $stmt->bindValue('app_id', $performance['id']);

                $stmt->execute();
            }

        }
    }
    private function processCattlePerformance($performances, Api $batch)
    {
        foreach ($performances as $performance) {
            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $performance['crm_visit_id'], 'appBatch' => $batch]);
            if ($findVisit){

                $deleteSql = "DELETE FROM `crm_cattle_performance_details` WHERE `app_batch_id`= :app_batch_id AND `app_id`= :app_id";
                $stmtDelete = $this->getDoctrine()->getConnection()->prepare($deleteSql);
                $stmtDelete->bindValue('app_batch_id', $batch->getId());
                $stmtDelete->bindValue('app_id', $performance['id']);
                $stmtDelete->execute();

                $sql = "INSERT INTO `crm_cattle_performance_details`(`employee_id`, `report_id`, `agent_id`, `customer_id`, `breed_type`, `feed_type`, `repoting_month`, `visiting_date`, `age_of_cattle_month`, `previous_body_weight`, `present_body_weight`, `body_weight_difference`, `duration_of_bwt_difference`, `lactation_no`, `age_of_lactation`, `average_weight_per_day`, `average_weight_per_kg_consumption_feed`, `average_weight_per_kg_dm`, `milk_fat_percentage`, `consumption_feed_intake_ready_feed`, `consumption_feed_intake_conventional`, `consumption_feed_intake_total`, `fodder_green_grass_kg`, `fodder_straw_kg`, `dm_of_fodder_green_grass_kg`, `dm_of_fodder_straw_kg`, `total_dm_kg`, `dm_requirement_by_bwt_kg`, `remarks`, `created_at`, `updated_at`, `visit_id`, `app_batch_id`, `app_id`) 
VALUES (:employee_id, :report_id, :agent_id, :customer_id, :breed_type, :feed_type, :repoting_month, :visiting_date, :age_of_cattle_month, :previous_body_weight, :present_body_weight, :body_weight_difference, :duration_of_bwt_difference, :lactation_no, :age_of_lactation, :average_weight_per_day, :average_weight_per_kg_consumption_feed, :average_weight_per_kg_dm, :milk_fat_percentage, :consumption_feed_intake_ready_feed, :consumption_feed_intake_conventional, :consumption_feed_intake_total, :fodder_green_grass_kg, :fodder_straw_kg, :dm_of_fodder_green_grass_kg, :dm_of_fodder_straw_kg, :total_dm_kg, :dm_requirement_by_bwt_kg, :remarks, :created_at, :updated_at, :visit_id, :app_batch_id, :app_id)";

                $repotingMonth = new \DateTime($performance['repoting_month']?$performance['repoting_month']:$performance['created_at']);
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
                $stmt->bindValue('app_batch_id', $batch->getId());
                $stmt->bindValue('app_id', $performance['id']);

                $stmt->execute();
            }
        }
    }
    private function processFcrDetail($frcDetails, Api $batch)
    {
        foreach ($frcDetails as $frcDetail) {
            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $frcDetail['crm_visit_id'], 'appBatch' => $batch]);
            if ($findVisit){
                $deleteSql = "DELETE FROM `crm_fcr_details` WHERE `app_batch_id`= :app_batch_id AND `app_id`= :app_id";
                $stmtDelete = $this->getDoctrine()->getConnection()->prepare($deleteSql);
                $stmtDelete->bindValue('app_batch_id', $batch->getId());
                $stmtDelete->bindValue('app_id', $frcDetail['id']);
                $stmtDelete->execute();

                $agent=null;
                $customer=null;
                $standard=null;
                $weight_standard=0;
                $feed_consumption_standard=0;
                if(isset($frcDetail['customer_id']) && !empty($frcDetail['customer_id'])){
                    $customer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($frcDetail['customer_id']);
                }
                if($frcDetail['report_id']){
                    $report= $this->getDoctrine()->getRepository(Setting::class)->find($frcDetail['report_id']);
                    if(in_array($report->getSlug(),['fcr-before-sale-sonali','fcr-after-sale-sonali'])){

                        /* @var SonaliStandard $sonaliStandard*/
                        $sonaliStandard= $this->getDoctrine()->getRepository(SonaliStandard::class)->findOneBy(array('age'=>$frcDetail['age_day']));
                        if($sonaliStandard){
                            $weight_standard=$sonaliStandard->getTargetBodyWeight();
                            $feed_consumption_standard=$sonaliStandard->getCumulativeFeedIntake();
                        }
                    }
                    if(in_array($report->getSlug(),['fcr-before-sale-boiler','fcr-after-sale-boiler'])){

                        /* @var BroilerStandard $broilerStandard*/
                        $broilerStandard= $this->getDoctrine()->getRepository(BroilerStandard::class)->findOneBy(array('age'=>$frcDetail['age_day']));
                        if($broilerStandard){
                            $weight_standard=$broilerStandard->getTargetBodyWeight();
                            $feed_consumption_standard=$broilerStandard->getTargetFeedConsumption();
                        }
                    }
                }
                $sql = "INSERT INTO `crm_fcr_details`(`report_id`, `employee_id`, `agent_id`, `customer_id`, `hatchery_id`, `breed_id`, `feed_id`, `feed_mill_id`, `feed_type_id`, `fcr_of_feed`, `reporting_month`, `hatching_date`, `total_birds`, `age_day`, `mortality_pes`, `mortality_percent`, `weight_standard`, `weight`, `feed_consumption_total_kg`, `feed_consumption_per_bird`, `feed_consumption_standard`, `fcr_without_mortality`, `fcr_with_mortality`, `pro_date`, `batch_no`, `remarks`, `created_at`, `visit_id`, `app_batch_id`, `app_id`) 
VALUES (:report_id, :employee_id, :agent_id, :customer_id, :hatchery_id, :breed_id, :feed_id, :feed_mill_id, :feed_type_id, :fcr_of_feed, :reporting_month, :hatching_date, :total_birds, :age_day, :mortality_pes, :mortality_percent, :weight_standard, :weight, :feed_consumption_total_kg, :feed_consumption_per_bird, :feed_consumption_standard, :fcr_without_mortality, :fcr_with_mortality, :pro_date, :batch_no, :remarks, :created_at, :visit_id, :app_batch_id, :app_id)";
                $repotingMonth = new \DateTime($frcDetail['reporting_month']?$frcDetail['reporting_month']:$frcDetail['created_at']);
                $hatchingDate = new \DateTime($frcDetail['hatching_date']);
                $createdAt = new \DateTime($frcDetail['created_at']);
                $proDate =$frcDetail['pro_date']? new \DateTime($frcDetail['pro_date']):'';

                if($customer && $customer->getAgent()){
                    $agent=$customer->getAgent()->getId();
                }

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('report_id', $frcDetail['report_id']);
                $stmt->bindValue('employee_id', $frcDetail['employee_id']);
                $stmt->bindValue('agent_id', $frcDetail['agent_id']>0?$frcDetail['agent_id']:$agent);
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
                $stmt->bindValue('weight_standard', $frcDetail['weight_standard']!='null'?$frcDetail['weight_standard']:$weight_standard);
                $stmt->bindValue('weight', $frcDetail['weight']);
                $stmt->bindValue('feed_consumption_total_kg', $frcDetail['feed_consumption_total_kg']);
                $stmt->bindValue('feed_consumption_per_bird', $frcDetail['feed_consumption_per_bird']);
                $stmt->bindValue('feed_consumption_standard', $frcDetail['feed_consumption_standard'] != 'null' ? $frcDetail['feed_consumption_standard'] : $feed_consumption_standard);
                $stmt->bindValue('fcr_without_mortality', $frcDetail['fcr_without_mortality']);
                $stmt->bindValue('fcr_with_mortality', $frcDetail['fcr_with_mortality']);
                $stmt->bindValue('pro_date', $proDate!=''?$proDate->format('Y-m-d'):null);
                $stmt->bindValue('batch_no', $frcDetail['batch_no']);
                $stmt->bindValue('remarks', $frcDetail['remarks']);
                $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('visit_id', $findVisit->getId());
                $stmt->bindValue('app_batch_id', $batch->getId());
                $stmt->bindValue('app_id', $frcDetail['id']);

                $stmt->execute();
            }
        }
    }
    private function processAntibioticFreeFarm($reports, Api $batch)
    {
        foreach ($reports as $report) {

            $sql = "SELECT id FROM `crm_antibiotic_free_farm` WHERE `employee_id` = :employee_id AND `customer_id` = :customer_id AND `reporting_month` = :reporting_month LIMIT 1";
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('reporting_month', $report['reporting_month']);

            $stmt->execute();
            $exist = $stmt->fetch();

            $hatchingDate = new \DateTime($report['hatching_date']);

            if ($exist){
                $sql = "UPDATE `crm_antibiotic_free_farm` SET 
                        `agent_id` = :agent_id, 
                        `hatchery_id` = :hatchery_id, 
                        `breed_id` = :breed_id, 
                        `feed_id` = :feed_id, 
                        `hatching_date` = :hatching_date, 
                        `total_stocked_chicks_pcs` = :total_stocked_chicks_pcs, 
                        `total_feed_used_kg` = :total_feed_used_kg, 
                        `age_days` = :age_days, 
                        `total_broiler_weight_kg` = :total_broiler_weight_kg, 
                        `mortality` = :mortality, 
                        `fcr` = :fcr, 
                        `remarks` = :remarks, 
                        `medicine_total_cost` = :medicine_total_cost, 
                        `vaccine_total_cost` = :vaccine_total_cost WHERE id = {$exist['id']}";

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('agent_id',$report['agent_id']);
                $stmt->bindValue('hatchery_id',$report['hatchery_id']);
                $stmt->bindValue('breed_id',$report['breed_id']);
                $stmt->bindValue('feed_id',$report['feed_id']);
                $stmt->bindValue('hatching_date',$hatchingDate->format('Y-m-d'));
                $stmt->bindValue('total_stocked_chicks_pcs',$report['total_stocked_chicks_pcs']);
                $stmt->bindValue('total_feed_used_kg',$report['total_feed_used_kg']);
                $stmt->bindValue('age_days',$report['age_days']);
                $stmt->bindValue('total_broiler_weight_kg',$report['total_broiler_weight_kg']);
                $stmt->bindValue('mortality',$report['mortality']);
                $stmt->bindValue('fcr',$report['fcr']);
                $stmt->bindValue('remarks',$report['remarks']);
                $stmt->bindValue('medicine_total_cost',$report['medicine_total_cost']);
                $stmt->bindValue('vaccine_total_cost',$report['vaccine_total_cost']);

                $stmt->execute();

            }else{
                $sql = "INSERT INTO `crm_antibiotic_free_farm`(`report_id`, `report_parent_parent_id`, `agent_id`, `customer_id`, `employee_id`, `hatchery_id`, `breed_id`, `feed_id`, `hatching_date`, `reporting_month`, `total_stocked_chicks_pcs`, `total_feed_used_kg`, `age_days`, `total_broiler_weight_kg`, `mortality`, `fcr`, `remarks`, `created_at`, `medicine_total_cost`, `vaccine_total_cost`) 
VALUES (:report_id, :report_parent_parent_id, :agent_id, :customer_id, :employee_id, :hatchery_id, :breed_id, :feed_id, :hatching_date, :reporting_month, :total_stocked_chicks_pcs, :total_feed_used_kg, :age_days, :total_broiler_weight_kg, :mortality, :fcr, :remarks, :created_at, :medicine_total_cost, :vaccine_total_cost)";

                $hatchingDate = new \DateTime($report['hatching_date']);
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
                $stmt->bindValue('reporting_month',$report['reporting_month']);
                $stmt->bindValue('total_stocked_chicks_pcs',$report['total_stocked_chicks_pcs']);
                $stmt->bindValue('total_feed_used_kg',$report['total_feed_used_kg']);
                $stmt->bindValue('age_days',$report['age_days']);
                $stmt->bindValue('total_broiler_weight_kg',$report['total_broiler_weight_kg']);
                $stmt->bindValue('mortality',$report['mortality']);
                $stmt->bindValue('fcr',$report['fcr']);
                $stmt->bindValue('remarks',$report['remarks']);
                $stmt->bindValue('created_at',$createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('medicine_total_cost',$report['medicine_total_cost']);
                $stmt->bindValue('vaccine_total_cost',$report['vaccine_total_cost']);

                $stmt->execute();
            }
        }
    }
    private function processCostBenefitAnalysis($reports, Api $batch)
    {
        foreach ($reports as $report) {

            $sql = "SELECT id FROM `crm_cost_benefit_analysis_for_less_costing_farm` WHERE `employee_id` = :employee_id AND `customer_id` = :customer_id AND `reporting_month` = :reporting_month AND `report_id` = :report_id LIMIT 1";
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('reporting_month', $report['reporting_month']);
            $stmt->bindValue('report_id', $report['report_id']);

//            $stmt->bindValue('employee_id', 255);
//            $stmt->bindValue('customer_id', 257);
//            $stmt->bindValue('reporting_month', '2022-03-01');
//            $stmt->bindValue('report_id', 242);

            $stmt->execute();
            $exist = $stmt->fetch();

            $hatchingDate = new \DateTime($report['hatching_date']);

            if ($exist){
                $sql = "UPDATE `crm_cost_benefit_analysis_for_less_costing_farm` SET 
                        `report_id` = :report_id, 
                        `report_parent_parent_id` = :report_parent_parent_id, 
                        `agent_id` = :agent_id, 
                        `hatchery_id` =  :hatchery_id, 
                        `breed_id` = :breed_id, 
                        `feed_id` = :feed_id,
                        `hatching_date` = :hatching_date,
                        `total_stocked_chicks_pcs` = :total_stocked_chicks_pcs,
                        `total_feed_used_kg` = :total_feed_used_kg,
                        `total_broiler_weight_kg` = :total_broiler_weight_kg,
                        `mortality` = :mortality,
                        `remarks` = :remarks,
                        `species_id` = :species_id,
                        `pond_size` = :pond_size,
                        `fingerling_size` = :fingerling_size,
                        `harvesting_size`= :harvesting_size,
                        `age_days`= :age_days,
                        `fcr` = :fcr,
                        `item_price_per_pcs` = :item_price_per_pcs,
                        `feed_price_per_kg` = :feed_price_per_kg,
                        `broiler_or_fish_price_per_kg` = :broiler_or_fish_price_per_kg,
                        `total_medicine_cost` = :total_medicine_cost,
                        `total_vaccine_cost` = :total_vaccine_cost,
                        `total_pond_preparation_cost` = :total_pond_preparation_cost,
                        `used_bag_price_per_pcs` = :used_bag_price_per_pcs,
                        `litter_or_pond_rent_cost` = :litter_or_pond_rent_cost,
                        `electricity_and_fuel_cost` = :electricity_and_fuel_cost,
                        `labour_cost` = :labour_cost,
                        `transport_cost` = :transport_cost,
                        `other_cost` = :other_cost WHERE id = {$exist['id']}";

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('report_id', $report['report_id']);
                $stmt->bindValue('report_parent_parent_id', $report['report_parent_parent_id']);
                $stmt->bindValue('agent_id', $report['agent_id']);
                $stmt->bindValue('hatchery_id', $report['hatchery_id']);
                $stmt->bindValue('breed_id', $report['breed_id']);
                $stmt->bindValue('feed_id', $report['feed_id']);
                $stmt->bindValue('hatching_date', $hatchingDate->format('Y-m-d'));
                $stmt->bindValue('total_stocked_chicks_pcs', $report['total_stocked_chicks_pcs'] ?: 0);
                $stmt->bindValue('total_feed_used_kg', $report['total_feed_used_kg'] ?: 0);
                $stmt->bindValue('total_broiler_weight_kg', $report['total_broiler_weight_kg'] ?: 0);
                $stmt->bindValue('mortality', $report['mortality'] ?: 0);
                $stmt->bindValue('remarks', $report['remarks']);
                $stmt->bindValue('species_id', $report['species_id']);
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
                $stmt->bindValue('age_days', $report['age_days'] ?: 0);
                $stmt->bindValue('fcr', $report['fcr'] ?: 0);
                $stmt->bindValue('item_price_per_pcs', $report['item_price_per_pcs'] ?: 0);
                $stmt->bindValue('feed_price_per_kg', $report['feed_price_per_kg'] ?: 0);
                $stmt->bindValue('broiler_or_fish_price_per_kg', $report['broiler_or_fish_price_per_kg'] ?: 0);
                $stmt->bindValue('total_medicine_cost', $report['total_medicine_cost'] ?: 0);
                $stmt->bindValue('total_vaccine_cost', $report['total_vaccine_cost'] ?: 0);
                $stmt->bindValue('total_pond_preparation_cost', $report['total_pond_preparation_cost'] ?: 0);
                $stmt->bindValue('used_bag_price_per_pcs', $report['used_bag_price_per_pcs'] ?: 0);
                $stmt->bindValue('litter_or_pond_rent_cost', $report['litter_or_pond_rent_cost'] ?: 0);
                $stmt->bindValue('electricity_and_fuel_cost', $report['electricity_and_fuel_cost'] ?: 0);
                $stmt->bindValue('labour_cost', $report['labour_cost'] ?: 0);
                $stmt->bindValue('transport_cost', $report['transport_cost'] ?: 0);
                $stmt->bindValue('other_cost', $report['other_cost'] ?: 0);

                $stmt->execute();

            }else{
                $sql = "INSERT INTO `crm_cost_benefit_analysis_for_less_costing_farm`(`report_id`, `report_parent_parent_id`, `agent_id`, `customer_id`, `employee_id`, `hatchery_id`, `breed_id`, `feed_id`, `hatching_date`, `total_stocked_chicks_pcs`, `total_feed_used_kg`, `total_broiler_weight_kg`, `mortality`, `remarks`, `created_at`, `species_id`, `reporting_month`, `pond_size`, `fingerling_size`, `harvesting_size`, `age_days`, `fcr`, `item_price_per_pcs`, `feed_price_per_kg`, `broiler_or_fish_price_per_kg`, `total_medicine_cost`, `total_vaccine_cost`, `total_pond_preparation_cost`, `used_bag_price_per_pcs`, `litter_or_pond_rent_cost`, `electricity_and_fuel_cost`, `labour_cost`, `transport_cost`, `other_cost`) 

VALUES (:report_id, :report_parent_parent_id, :agent_id, :customer_id, :employee_id, :hatchery_id, :breed_id, :feed_id, :hatching_date, :total_stocked_chicks_pcs, :total_feed_used_kg, :total_broiler_weight_kg, :mortality, :remarks, :created_at, :species_id, :reporting_month, :pond_size, :fingerling_size, :harvesting_size, :age_days, :fcr, :item_price_per_pcs, :feed_price_per_kg, :broiler_or_fish_price_per_kg, :total_medicine_cost, :total_vaccine_cost, :total_pond_preparation_cost, :used_bag_price_per_pcs, :litter_or_pond_rent_cost, :electricity_and_fuel_cost, :labour_cost, :transport_cost, :other_cost)";

                $hatchingDate = new \DateTime($report['hatching_date']);
                $createdAt = new \DateTime($report['created_at']);

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
                $stmt->bindValue('total_stocked_chicks_pcs', $report['total_stocked_chicks_pcs'] ?: 0);
                $stmt->bindValue('total_feed_used_kg', $report['total_feed_used_kg'] ?: 0);
                $stmt->bindValue('total_broiler_weight_kg', $report['total_broiler_weight_kg'] ?: 0);
                $stmt->bindValue('mortality', $report['mortality'] ?: 0);
                $stmt->bindValue('remarks', $report['remarks']);
                $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                $stmt->bindValue('species_id', $report['species_id']);
                $stmt->bindValue('reporting_month', $report['reporting_month']);
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
                $stmt->bindValue('age_days', $report['age_days'] ?: 0);
                $stmt->bindValue('fcr', $report['fcr'] ?: 0);
                $stmt->bindValue('item_price_per_pcs', $report['item_price_per_pcs'] ?: 0);
                $stmt->bindValue('feed_price_per_kg', $report['feed_price_per_kg'] ?: 0);
                $stmt->bindValue('broiler_or_fish_price_per_kg', $report['broiler_or_fish_price_per_kg'] ?: 0);
                $stmt->bindValue('total_medicine_cost', $report['total_medicine_cost'] ?: 0);
                $stmt->bindValue('total_vaccine_cost', $report['total_vaccine_cost'] ?: 0);
                $stmt->bindValue('total_pond_preparation_cost', $report['total_pond_preparation_cost'] ?: 0);
                $stmt->bindValue('used_bag_price_per_pcs', $report['used_bag_price_per_pcs'] ?: 0);
                $stmt->bindValue('litter_or_pond_rent_cost', $report['litter_or_pond_rent_cost'] ?: 0);
                $stmt->bindValue('electricity_and_fuel_cost', $report['electricity_and_fuel_cost'] ?: 0);
                $stmt->bindValue('labour_cost', $report['labour_cost'] ?: 0);
                $stmt->bindValue('transport_cost', $report['transport_cost'] ?: 0);
                $stmt->bindValue('other_cost', $report['other_cost'] ?: 0);

                $stmt->execute();
            }

        }
    }
    private function processDiseaseMapping($reports, Api $batch)
    {
        foreach ($reports as $report) {

            $sql = "INSERT INTO `crm_disease_mapping`(`report_id`, `agent_id`, `customer_id`, `employee_id`, `hatchery_id`, `farm_type_id`, `feed_id`, `disease_id`, `visiting_date`, `flock_size_or_capacity`, `age_days`, `age_unit_type`, `remarks`, `created_at`, `breed_id`, `culture_area_for_fish`, `dencity_for_fish`, `average_weight_for_fish`, `treatment`) VALUES (:report_id, :agent_id, :customer_id, :employee_id, :hatchery_id, :farm_type_id, :feed_id, :disease_id, :visiting_date, :flock_size_or_capacity, :age_days, :age_unit_type, :remarks, :created_at, :breed_id, :culture_area_for_fish, :dencity_for_fish, :average_weight_for_fish, :treatment)";

            $visitingDate = new \DateTime($report['visiting_date']);
            $createdAt = new \DateTime($report['created_at']);

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('report_id', $report['report_id']);
            $stmt->bindValue('agent_id', $report['agent_id'] > 0 ? $report['agent_id'] : NULL);
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
//            $stmt->bindValue('age_days', $report['age_days']);
            $stmt->bindValue('age_unit_type', $report['age_unit_type']);
            $stmt->bindValue('remarks', $report['remarks']);
            $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
            if(isset($report['breed_id'])&&$report['breed_id']){
                $stmt->bindValue('breed_id', $report['breed_id']);
            }else{
                $stmt->bindValue('breed_id', null);
            }

            if (isset($report['age_days'])&&$report['age_days']){
                $stmt->bindValue('age_days', $report['age_days']);
            }else{
                $stmt->bindValue('age_days', 0);
            }

            if (isset($report['culture_area'])&&$report['culture_area']){
                $stmt->bindValue('culture_area_for_fish', $report['culture_area']);
            }else{
                $stmt->bindValue('culture_area_for_fish', 0);
            }
            if (isset($report['culture_area'])&&$report['culture_area'] && $report['culture_area']>0){
                $stmt->bindValue('dencity_for_fish', number_format(($report['flock_size_or_capacity']/$report['culture_area']),2,'.',''));
            }else{
                $stmt->bindValue('dencity_for_fish', 0);
            }
            if (isset($report['avg_weight'])&&$report['avg_weight']){
                $stmt->bindValue('average_weight_for_fish', $report['avg_weight']);
            }else{
                $stmt->bindValue('average_weight_for_fish', 0);
            }

            if(isset($report['treatment'])&&$report['treatment']){
                $stmt->bindValue('treatment', $report['treatment']);
            }else{
                $stmt->bindValue('treatment', 'null');
            }

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

                $executeStatus = $stmt->execute();
                if ($executeStatus){

                    $findLifeCycle = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->find($this->getDoctrine()->getConnection()->lastInsertId());

                    $lifeCycleSetting = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->findOneBy(array('report' => $findLifeCycle->getReport()));

                    for($i = 1; $i <= $lifeCycleSetting->getNumberOfWeek(); $i++){
                        $details = new ChickLifeCycleDetails();

                        $details->setVisitingWeek($i);
                        $details->setCrmChickLifeCycle($findLifeCycle);
                        $details->setCreatedAt(new \DateTime('now'));

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($details);

                        $em->flush();
                    }
                }
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

                $proDate = new \DateTime($report['pro_date']);
                $reportingDate = new \DateTime($report['reporting_date']);
                $createdAt = new \DateTime($report['created_at']);
                $updatedAt = new \DateTime($report['updated_at']);

                /* @var ChickLifeCycleDetails $findDetails*/
                $findDetails = $this->getDoctrine()->getRepository(ChickLifeCycleDetails::class)->findOneBy(['crmChickLifeCycle' => $lifeCycleId, 'visitingWeek' =>  $report['visiting_week']]);
                if ($findDetails){
                    $feedType = $report['feed_type_id'] ? $this->getDoctrine()->getRepository(Setting::class)->find($report['feed_type_id']) : null;

                    $findDetails->setAgeDays($report['age_days']);
                    $findDetails->setMortalityPes($report['mortality_pes']);
                    $findDetails->setMortalityPercent($report['mortality_percent']);
                    $findDetails->setWeightStandard($report['weight_standard']);
                    $findDetails->setWeightAchieved($report['weight_achieved']);
                    $findDetails->setPerBird($report['per_bird']);
                    $findDetails->setFeedStandard($report['feed_standard']);
                    $findDetails->setFeedTotalKg($report['feed_total_kg']);
                    $findDetails->setWithoutMortality($report['without_mortality']);
                    $findDetails->setWithMortality($report['with_mortality']);
                    $findDetails->setProDate($proDate);
                    $findDetails->setBatchNo($report['batch_no']);
                    $findDetails->setRemarks($report['remarks']);
                    $findDetails->setCreatedAt($createdAt);
                    $findDetails->setUpdatedAt($updatedAt);
                    $findDetails->setFeedType($feedType);
                    $findDetails->setReportingDate($reportingDate);

                    $this->getDoctrine()->getManager()->persist($findDetails);
                    $this->getDoctrine()->getManager()->flush();

                }

                /*else{
                    $sql = "INSERT INTO `crm_chick_life_cycle_details`(`crm_chick_life_cycle_id`, `visiting_week`, `age_days`, `mortality_pes`, `mortality_percent`, `weight_standard`, `weight_achieved`, `feed_total_kg`, `per_bird`, `feed_standard`, `without_mortality`, `with_mortality`, `pro_date`, `batch_no`, `remarks`, `created_at`, `updated_at`, `feed_type_id`, `reporting_date`)
VALUES (:crm_chick_life_cycle_id, :visiting_week, :age_days, :mortality_pes, :mortality_percent, :weight_standard, :weight_achieved, :feed_total_kg, :per_bird, :feed_standard, :without_mortality, :with_mortality, :pro_date, :batch_no, :remarks, :created_at, :updated_at, :feed_type_id, :reporting_date)";

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
                }*/
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

            $findLifeCycle = $this->getDoctrine()->getRepository(LayerLifeCycle::class)->findOneBy(['customer' => $findFarmer, 'employee' => $findEmployee, 'report' => $findReport]);

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

                $executeStatus = $stmt->execute();

                if ($executeStatus){

                    $findLifeCycle = $this->getDoctrine()->getRepository(LayerLifeCycle::class)->find($this->getDoctrine()->getConnection()->lastInsertId());

                    $lifeCycleSetting = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->findOneBy(array('report' => $findLifeCycle->getReport()));

                    for($i = 1; $i <= $lifeCycleSetting->getNumberOfWeek(); $i++){
                        $details = new LayerLifeCycleDetails();

                        $details->setAgeWeek($i);
                        $details->setCrmLayerLifeCycle($findLifeCycle);
                        $details->setCreated(new \DateTime('now'));

                        $em = $this->getDoctrine()->getManager();
                        $em->persist($details);

                        $em->flush();
                    }
                }
            }
        }
    }
    private function processLayerLifeCycleDetail($reports, Api $batch)
    {
        foreach ($reports as $report) {

            $sql = "SELECT id FROM `crm_layer_life_cycle` WHERE `customer_id` = :customer_id AND `employee_id` = :employee_id AND `report_id` = :report_id AND `life_cycle_state` = :life_cycle_state";
            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('report_id', $report['report_id']);
            $stmt->bindValue('life_cycle_state', 'IN_PROGRESS');

            $stmt->execute();
            $lifeCycleId = $stmt->fetch()['id'];
            if ($lifeCycleId){
                $visitingDate = new \DateTime($report['visiting_date']);
                $productionDate = new \DateTime($report['production_date']);
                $createdAt = new \DateTime($report['created']);
                $updatedAt = new \DateTime($report['updated']);

                /**
                 * @var LayerLifeCycleDetails $findDetails
                 */
                $findDetails = $this->getDoctrine()->getRepository(LayerLifeCycleDetails::class)->findOneBy(['crmLayerLifeCycle' => $lifeCycleId, 'ageWeek' => $report['age_week']]);
                if ($findDetails){
                    if ($report['feed_mill_id']){
                        $findFeedMill = $this->getDoctrine()->getRepository(Setting::class)->find($report['feed_mill_id']);
                    }else{
                        $findFeedMill = null;
                    }

                    if ($report['feed_type_id']){
                        $findFeedType = $this->getDoctrine()->getRepository(Setting::class)->find($report['feed_type_id']);
                    }else{
                        $findFeedType = null;
                    }

                    $findDetails->setVisitingDate($visitingDate);
                    $findDetails->setAgeWeek($report['age_week']);
                    $findDetails->setDeadBird($report['dead_bird']);
                    $findDetails->setAvgWeight($report['avg_weight']);
                    $findDetails->setTargetWeight($report['target_weight']);
                    $findDetails->setUniformity($report['uniformity']);
                    $findDetails->setFeedPerBird($report['feed_per_bird']);
                    $findDetails->setTargetFeedPerBird($report['target_feed_per_bird']);
                    $findDetails->setTotalEggs($report['total_eggs']);
                    $findDetails->setTargetEggProduction($report['target_egg_production']);
                    $findDetails->setEggWeightActual($report['egg_weight_actual']);
                    $findDetails->setEggWeightStandard($report['egg_weight_standard']);
                    $findDetails->setProductionDate($productionDate);
                    $findDetails->setBatchNo($report['batch_no']);
                    $findDetails->setMedicine($report['medicine']);
                    $findDetails->setRemarks($report['remarks']);
                    $findDetails->setFeedMill($findFeedMill);
                    $findDetails->setFeedType($findFeedType);
                    $findDetails->setUpdated($updatedAt);
                    $this->getDoctrine()->getManager()->persist($findDetails);
                    $this->getDoctrine()->getManager()->flush();
                }
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
                $sql1 = "SELECT * FROM `crm_expence_vehicle` WHERE crm_expence_vehicle.expense_id = :expenseId AND crm_expence_vehicle.setting_id = :settingId";
                $stmt1 = $this->getDoctrine()->getConnection()->prepare($sql1);
                $stmt1->bindValue('expenseId', $expenseId);
                $stmt1->bindValue('settingId', $report['setting_id']);
                $stmt1->execute();
                $expenseVehicle = $stmt1->fetch();
                if(!$expenseVehicle){

                    $sql = "INSERT INTO `crm_expence_vehicle`(`expense_id`, `setting_id`) VALUES (:expense_id, :setting_id)";
                    $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                    $stmt->bindValue('expense_id', $expenseId);
                    $stmt->bindValue('setting_id', $report['setting_id']);

                    $stmt->execute();
                }

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
            $findFarmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($farmer['customer_id']);
            /**
             * @var FarmerIntroduceDetails $findIntroFarmer
             */
            $findIntroFarmer = $this->getDoctrine()->getRepository(FarmerIntroduceDetails::class)->findOneBy(['customer' => $findFarmer]);

            if ($findIntroFarmer && $farmer['feed_id'] == 1){
                if ($findIntroFarmer->getIntroduceDate()){ // if farmer already introduced
                    continue;
                }
                $updateFarmer = "UPDATE `crm_customers` SET `updated`= :updated,`agent_id`= :agent_id WHERE id = :id";
                $updateFarmerStmt = $this->getDoctrine()->getConnection()->prepare($updateFarmer);
                $updateFarmerStmt->bindValue('agent_id', $farmer['agent_id']);
                $updateFarmerStmt->bindValue('id', $farmer['customer_id']);

                if ($farmer['created_at']){
                    $updateFarmerStmt->bindValue('updated', (new \DateTime($farmer['created_at']))->format('Y-m-d H:i:s'));
                }else{
                    $updateFarmerStmt->bindValue('updated', null);
                }
                $updateFarmerStmt->execute();

                $sql = "UPDATE `crm_customer_introduce_details` SET `agent_id`= :agentId,`culture_species_item_and_qty`= :culture_species_item_and_qty,`remarks`= :remarks,`feed_id`= :feed_id,`introduce_date`= :introduce_date WHERE customer_id = :farmerId";  // every time exits when create new farmer
                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('farmerId', $farmer['customer_id']);
                $stmt->bindValue('agentId', $farmer['agent_id']);
                $stmt->bindValue('culture_species_item_and_qty', $farmer['culture_species_item_and_qty']);
                $stmt->bindValue('remarks', $farmer['remarks']);
                if ($farmer['created_at']){
                    $stmt->bindValue('introduce_date', (new \DateTime($farmer['created_at']))->format('Y-m-d H:i:s'));
                }else{
                    $stmt->bindValue('introduce_date', (new \DateTime('now'))->format('Y-m-d H:i:s'));
                }

                $stmt->bindValue('feed_id', 55);

                $stmt->execute();
            }
        }
    }

    public function processCattleFarmVisitDetails($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $reportingMonth = $report['reporting_month'] ? (new \DateTime($report['reporting_month']))->format('Y-m-d') : null;
            $visitingDate = $report['visiting_date'] ? (new \DateTime($report['visiting_date']))->format('Y-m-d') : null;
            $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;

            $sql = "INSERT INTO `crm_cattle_farm_visit_details`(`agent_id`, `customer_id`, `visiting_date`, `cattlePopulationOx`, `cattlePopulationCow`, `cattlePopulationCalf`, `avgMilkYieldPerDay`, `conceptionRate`, `fodder_green_grass_kg`, `fodder_straw_kg`, `typeOfConcentrateFeed`, `marketPriceMilkPerLiter`, `marketPriceMeatPerKg`, `remarks`, `created_at`, `employee_id`, `repoting_month`, `report_id`) VALUES (:agent_id, :customer_id, :visiting_date, :cattlePopulationOx, :cattlePopulationCow, :cattlePopulationCalf, :avgMilkYieldPerDay, :conceptionRate, :fodder_green_grass_kg, :fodder_straw_kg, :typeOfConcentrateFeed, :marketPriceMilkPerLiter, :marketPriceMeatPerKg, :remarks, :created_at, :employee_id, :repoting_month, :report_id)";

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            $stmt->bindValue('agent_id', $report['agent_id']);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('visiting_date', $visitingDate);
            $stmt->bindValue('cattlePopulationOx', $report['cattlePopulationOx'] ?: 0);
            $stmt->bindValue('cattlePopulationCow', $report['cattlePopulationCow'] ?: 0);
            $stmt->bindValue('cattlePopulationCalf', $report['cattlePopulationCalf'] ?: 0);
            $stmt->bindValue('avgMilkYieldPerDay', $report['avgMilkYieldPerDay']);
            $stmt->bindValue('conceptionRate', $report['conceptionRate']);
            $stmt->bindValue('fodder_green_grass_kg', $report['fodder_green_grass_kg']);
            $stmt->bindValue('fodder_straw_kg', $report['fodder_straw_kg']);
            $stmt->bindValue('typeOfConcentrateFeed', $report['typeOfConcentrateFeed']);
            $stmt->bindValue('marketPriceMilkPerLiter', $report['marketPriceMilkPerLiter']);
            $stmt->bindValue('marketPriceMeatPerKg', $report['marketPriceMeatPerKg']);
            $stmt->bindValue('remarks', $report['remarks']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('repoting_month', $reportingMonth);
            $stmt->bindValue('report_id', $report['report_id']);
            $stmt->bindValue('created_at', $createdAt);

            $stmt->execute();
        }
    }

    private function processPoultryMeatEggPrice($reports, Api $batch)
    {
        foreach ($reports as $report) {

//            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $report['crm_visit_id'], 'appBatch' => $batch]);
//            if ($findVisit){
            $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;
            $reportingDate = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d') : null;
            $items = json_decode($report['poultry_meat_egg_prices'], true);

            foreach ( $items as $item) {
                $findExist = $this->getDoctrine()->getRepository(PoultryMeatEggPrice::class)->checkExistRecord($report['region_id'],$item['id'], $report['employee_id'], $reportingDate);
                if ($findExist){
                    $sql = "UPDATE `crm_poultry_meat_egg_price` SET breed_type_id = :breed_type_id, price = :price WHERE employee_id = :employee_id AND region_id = :region_id AND breed_type_id = :breed_type_id AND reporting_date = :reporting_date";

                    $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                    $stmt->bindValue('breed_type_id', $item['id']);
                    $stmt->bindValue('price', $item['price']);
                    $stmt->bindValue('employee_id', $report['employee_id']);
                    $stmt->bindValue('region_id', $report['region_id']);
                    $stmt->bindValue('reporting_date', $reportingDate);

                    $stmt->execute();

                }else{
                    $sql = "INSERT INTO `crm_poultry_meat_egg_price` (`employee_id`,`region_id`, `status`, `created_at`, `breed_type_id`,`price`, `reporting_date`) VALUES (:employee_id, :region_id, :status, :created_at, :breed_type_id, :price, :reporting_date)";

                    $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
//                    $stmt->bindValue('crm_visit_id', $findVisit->getId());
                    $stmt->bindValue('employee_id', $report['employee_id']);
                    $stmt->bindValue('region_id', $report['region_id']);
                    $stmt->bindValue('status', 1);
                    $stmt->bindValue('created_at', $createdAt);
                    $stmt->bindValue('breed_type_id', $item['id']);
                    $stmt->bindValue('price', $item['price']);
                    $stmt->bindValue('reporting_date', $reportingDate);

                    $stmt->execute();
                }
            }

//            }
        }
    }

    private function processCompanyWiseFeedSale($reports, Api $batch){
        foreach ($reports as $report) {
            $employee = $this->getDoctrine()->getRepository(User::class)->find($report['employee_id']);
            $feedCompany = $this->getDoctrine()->getRepository(Setting::class)->find($report['feed_company_id']);

            /* @var CompanyWiseFeedSale $exist */
            $exist = $this->getDoctrine()->getRepository(CompanyWiseFeedSale::class)->findOneBy(['employee' => $employee, 'feedCompany' => $feedCompany, 'monthName' => $report['month_name'], 'year' => $report['year'], 'breedName' => strtolower($report['breed_name'])]);
            if ($exist){
                $exist->setProductWiseQty($report['product_wise_qty']);
                $exist->setTotalQty($report['total_qty']);
                $this->getDoctrine()->getManager()->persist($exist);
                $this->getDoctrine()->getManager()->flush();
            }else{
                $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;

                $sql = "INSERT INTO `crm_company_wise_feed_sale` (`employee_id`, `feed_company_id`, `month_name`, `year`, `breed_name`, `product_wise_qty`, `total_qty`, `created_at`) VALUES (:employee_id, :feed_company_id, :month_name, :year, :breed_name, :product_wise_qty, :total_qty, :created_at)";

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('employee_id', $report['employee_id']);
                $stmt->bindValue('feed_company_id', $report['feed_company_id']);
                $stmt->bindValue('month_name', $report['month_name']);
                $stmt->bindValue('year', $report['year']);
                $stmt->bindValue('breed_name', strtolower($report['breed_name']));
                $stmt->bindValue('product_wise_qty', $report['product_wise_qty']);
                $stmt->bindValue('total_qty', $report['total_qty']);
                $stmt->bindValue('created_at', $createdAt);

                $stmt->execute();
            }
        }
    }

    private function processFcrDifferentCompanies($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;

            /**
             * @var FcrDifferentCompanies $exist
             */
            $exist = $this->getDoctrine()->getRepository(FcrDifferentCompanies::class)->getExists($report['employee_id'], $report['hatchery_id'], $report['breed_name'], $createdAt);
            if ($exist){
                for($i = 1; $i <= 12; $i++){
                    $date = '01-' . $i . '-2022';
                    $month = date('F', strtotime($date));
                    $set = 'set' . $month;
                    $exist->$set($report[strtolower($month)] ?: 0);

                }
                $this->getDoctrine()->getManager()->flush();
            }else{
                $sql = "INSERT INTO `crm_fcr_different_companies` (`employee_id`, `hatchery_id`, `breed_name`, `january`, `february`, `march`, `april`, `may`, `june`, `july`, `august`, `september`, `october`, `november`, `december`, `created_at`) VALUES (:employee_id, :hatchery_id, :breed_name, :january, :february, :march, :april, :may, :june, :july, :august, :september, :october, :november, :december, :created_at)";

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('employee_id', $report['employee_id']);
                $stmt->bindValue('hatchery_id', $report['hatchery_id']);
                $stmt->bindValue('breed_name', strtolower($report['breed_name']));
                $stmt->bindValue('january', $report['january'] ?: 0);
                $stmt->bindValue('february', $report['february'] ?: 0);
                $stmt->bindValue('march', $report['march'] ?: 0);
                $stmt->bindValue('april', $report['april'] ?: 0);
                $stmt->bindValue('may', $report['may'] ?: 0);
                $stmt->bindValue('june', $report['june'] ?: 0);
                $stmt->bindValue('july', $report['july'] ?: 0);
                $stmt->bindValue('august', $report['august'] ?: 0);
                $stmt->bindValue('september', $report['september'] ?: 0);
                $stmt->bindValue('october', $report['october'] ?: 0);
                $stmt->bindValue('november', $report['november'] ?: 0);
                $stmt->bindValue('december', $report['december'] ?: 0);
                $stmt->bindValue('created_at', $createdAt);

                $stmt->execute();
            }

        }
    }

    private function processLabServices($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;

            $year = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y') : null;

            $exitingLabService = $this->getDoctrine()->getRepository(LabService::class)->getExitingLabService($report['employee_id'], $report['lab_id'], $report['service_id'], $report['breed_name'], $year);

            if($exitingLabService){
                $sql = "UPDATE `crm_lab_services` SET `january`= :january, `february`= :february, `march`= :march, `april`= :april, `may`= :may, `june`= :june, `july`= :july, `august`= :august, `september`= :september, `october`= :october, `november`= :november, `december`= :december, `created_at`= :created_at WHERE id = :id";

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('id', $exitingLabService['id']);

                $stmt->bindValue('january', $report['january'] && $report['january']!=""?$report['january']: $exitingLabService['january']);
                $stmt->bindValue('february',$report['february'] && $report['february']!=""?$report['february']: $exitingLabService['february']);
                $stmt->bindValue('march', $report['march'] && $report['march']!=""?$report['march']: $exitingLabService['march']);
                $stmt->bindValue('april', $report['april'] && $report['april']!=""?$report['april']: $exitingLabService['april']);
                $stmt->bindValue('may', $report['may'] && $report['may']!=""?$report['may']: $exitingLabService['may']);
                $stmt->bindValue('june', $report['june'] && $report['june']!=""?$report['june']: $exitingLabService['june']);
                $stmt->bindValue('july', $report['july'] && $report['july']!=""?$report['july']: $exitingLabService['july']);
                $stmt->bindValue('august', $report['august']&&$report['august']!=""?$report['august']: $exitingLabService['august']);
                $stmt->bindValue('september', $report['september']&&$report['september']!=""?$report['september']: $exitingLabService['september']);
                $stmt->bindValue('october', $report['october'] && $report['october']!=""?$report['october']: $exitingLabService['october']);
                $stmt->bindValue('november', $report['november'] && $report['november']!=""?$report['november']: $exitingLabService['november']);
                $stmt->bindValue('december', $report['december']&&$report['december']!=""?$report['december']: $exitingLabService['december']);
                $stmt->bindValue('created_at', $createdAt);
                $stmt->execute();
            }else{
                $sql = "INSERT INTO `crm_lab_services` (`employee_id`, `lab_id`, `service_id`, `breed_name`, `january`, `february`, `march`, `april`, `may`, `june`, `july`, `august`, `september`, `october`, `november`, `december`, `created_at`, `reporting_year`) VALUES (:employee_id, :lab_id, :service_id, :breed_name, :january, :february, :march, :april, :may, :june, :july, :august, :september, :october, :november, :december, :created_at, :reporting_year)";

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('employee_id', $report['employee_id']);
                $stmt->bindValue('lab_id', $report['lab_id']);
                $stmt->bindValue('service_id', $report['service_id']);
                $stmt->bindValue('breed_name', $report['breed_name']);
                $stmt->bindValue('january', $report['january'] ?: 0);
                $stmt->bindValue('february', $report['february'] ?: 0);
                $stmt->bindValue('march', $report['march'] ?: 0);
                $stmt->bindValue('april', $report['april'] ?: 0);
                $stmt->bindValue('may', $report['may'] ?: 0);
                $stmt->bindValue('june', $report['june'] ?: 0);
                $stmt->bindValue('july', $report['july'] ?: 0);
                $stmt->bindValue('august', $report['august'] ?: 0);
                $stmt->bindValue('september', $report['september'] ?: 0);
                $stmt->bindValue('october', $report['october'] ?: 0);
                $stmt->bindValue('november', $report['november'] ?: 0);
                $stmt->bindValue('december', $report['december'] ?: 0);
                $stmt->bindValue('created_at', $createdAt);
                $stmt->bindValue('reporting_year', $year);

                $stmt->execute();
            }

        }
    }

    private function processFishSalesPrice($reports, Api $batch)
    {
        foreach ($reports as $report) {

            $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;

            $sql = "INSERT INTO `crm_fish_sales_price` (`employee_id`, `species_type_id`, `fish_size_id`, `month_name`, `year`, `price`, `created_at`) VALUES (:employee_id, :species_type_id, :fish_size_id, :month_name, :year, :price, :created_at)";

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('species_type_id', $report['species_type_id']);
            $stmt->bindValue('fish_size_id', $report['fish_size_id']);
            $stmt->bindValue('month_name', $report['month_name']);
            $stmt->bindValue('year', $report['year']);
            $stmt->bindValue('price', $report['price']);
            $stmt->bindValue('created_at', $createdAt);

            $stmt->execute();
        }
    }

    private function processFishTilapiaFrySales($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;

            $sql = "INSERT INTO `crm_fish_tilapia_fry_sales` (`employee_id`, `feed_id`, `agent_id`, `other_competitor_agent_name`, `type`, `month_name`, `year`, `quantity`, `created_at`) VALUES (:employee_id, :feed_id, :agent_id, :other_competitor_agent_name, :type, :month_name, :year, :quantity, :created_at)";

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('feed_id', $report['feed_id']);
            $stmt->bindValue('agent_id', $report['agent_id']);
            $stmt->bindValue('other_competitor_agent_name', $report['other_competitor_agent_name']);
            $stmt->bindValue('type', $report['type']);
            $stmt->bindValue('month_name', $report['month_name']);
            $stmt->bindValue('year', $report['year']);
            $stmt->bindValue('quantity', $report['quantity']);
            $stmt->bindValue('created_at', $createdAt);

            $stmt->execute();
        }
    }

    private function processFishCompanySpeciesWiseAverageFcr($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;
            $reportingMonth = $report['reporting_month'] ? (new \DateTime($report['reporting_month']))->format('Y-m-d') : null;

            $sql = "INSERT INTO `crm_fish_company_species_wise_average_fcr` (`report_id`, `employee_id`, `agent_id`, `customer_id`, `feed_id`, `feed_type_id`, `fcr_of_feed`, `reporting_month`, `created_at`, `app_id`, `app_batch_id`) VALUES (:report_id, :employee_id, :agent_id, :customer_id, :feed_id, :feed_type_id, :fcr_of_feed, :reporting_month, :created_at, :app_id, :app_batch_id)";

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

            $stmt->bindValue('report_id', $report['report_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('agent_id', $report['agent_id']);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('feed_id', $report['feed_id']);
            $stmt->bindValue('feed_type_id', $report['feed_type_id']);
            $stmt->bindValue('fcr_of_feed', $report['fcr_of_feed']);
            $stmt->bindValue('reporting_month', $reportingMonth);
            $stmt->bindValue('created_at', $createdAt);
            $stmt->bindValue('app_id', $report['id']);
            $stmt->bindValue('app_batch_id', $batch->getId());

            $stmt->execute();
        }
    }

    private function processFishCompanySpeciesWiseAverageFcrDetails($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $findParent = $this->getDoctrine()->getRepository(FishCompanyAndSpeciesWiseAverageFcr::class)->findOneBy(['appId' => $report['fish_company_and_species_wise_fcr_id'], 'appBatch' => $batch]);
            if ($findParent){
                $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;

                $sql = "INSERT INTO `crm_fish_company_species_wise_average_fcr_details` (`fish_company_and_species_wise_fcr_id`, `species_name_id`, `quantity`,`created_at`) VALUES (:fish_company_and_species_wise_fcr_id, :species_name_id, :quantity, :created_at)";

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('fish_company_and_species_wise_fcr_id', $findParent->getId());
                $stmt->bindValue('species_name_id', $report['species_name_id']);
                $stmt->bindValue('quantity', $report['quantity'] ?: 0);
                $stmt->bindValue('created_at', $createdAt);

                $stmt->execute();

            }

        }
    }

    private function processDocPrice($reports, Api $batch)
    {
        foreach ($reports as $report) {

            $employee = $this->getDoctrine()->getRepository(User::class)->find($report['employee_id']);

            $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;
            $reportingDate = $report['reporting_date'] ? (new \DateTime($report['reporting_date']))->format('Y-m-d') : null;

            $existDailyChickPrice = $this->getDoctrine()->getRepository(DailyChickPrice::class)->findOneBy(['employee'=>$employee,'reportingDate'=>new \DateTime($reportingDate)]);
            $parentId=null;
            if($existDailyChickPrice){
                $parentId=$existDailyChickPrice->getId();
                $sql = "UPDATE crm_daily_chick_price SET employee_id={$report['employee_id']}, reporting_date='{$reportingDate}' WHERE id = {$existDailyChickPrice->getId()}";
                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->execute();
            }else{

                $sql = "INSERT INTO `crm_daily_chick_price` (`employee_id`, `reporting_date`,`created_at`) VALUES (:employee_id, :reporting_date, :created_at)";
                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('employee_id', $report['employee_id']);
//            $stmt->bindValue('location_id', $report['location_id']);
                $stmt->bindValue('reporting_date', $reportingDate);
                $stmt->bindValue('created_at', $createdAt);
                $stmt->execute();
                $parentId = $this->getDoctrine()->getConnection()->lastInsertId();
            }

            if ($parentId){
                $parent = $this->getDoctrine()->getRepository(DailyChickPrice::class)->find($parentId);
                foreach (json_decode($report['chick_prices'], true) as $item) {
                    $chickType = $this->getDoctrine()->getRepository(Setting::class)->find($item['id']);
                    $feed = $this->getDoctrine()->getRepository(Setting::class)->find($report['feed_id']);

                    $exist = $this->getDoctrine()->getRepository(DailyChickPriceDetails::class)->findOneBy(['crmDailyChickPrice' => $parent, 'chickType' => $chickType, 'feed' => $feed]);
                    if($exist){
                        /* @var DailyChickPriceDetails $exist*/
                        $exist->setPrice($item['price'] ?: 0);
//                        $exist->setUpdatedAt($createdAt);
                        $this->getDoctrine()->getManager()->persist($exist);
                        $this->getDoctrine()->getManager()->flush();
                    }else{
                        $sql = "INSERT INTO `crm_daily_chick_price_details`(`crm_daily_chick_price_id`, `chick_type_id`, `feed_id`, `price`, `created_at`) VALUES (:crm_daily_chick_price_id, :chick_type_id, :feed_id, :price, :created_at)";

                        $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                        $stmt->bindValue('crm_daily_chick_price_id', $parentId);
                        $stmt->bindValue('chick_type_id', $item['id']);
                        $stmt->bindValue('feed_id', $report['feed_id']);
                        $stmt->bindValue('price', (float)$item['price']);
                        $stmt->bindValue('created_at', $createdAt);

                        $stmt->execute();
                    }

                }
            }

        }
    }
    private function processFishLifeCycle($reports, Api $batch)
    {
        foreach ($reports as $report) {

            $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;
            $reportingMonth = $report['reporting_month'] ? (new \DateTime($report['reporting_month']))->format('Y-m-d') : null;

            $sql = "INSERT INTO `crm_fish_life_cycle`(`report_id`, `employee_id`, `customer_id`, `report_type`, `reporting_month`, `created_at`, `app_id`, `app_batch_id`) VALUES (:report_id, :employee_id, :customer_id, :report_type, :reporting_month, :created_at, :app_id, :app_batch_id)";

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

            $stmt->bindValue('report_id', $report['report_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('customer_id', $report['customer_id']);
            $stmt->bindValue('report_type', $report['report_type']);
            $stmt->bindValue('reporting_month', $reportingMonth);
            $stmt->bindValue('created_at', $createdAt);
            $stmt->bindValue('app_id', $report['id']);
            $stmt->bindValue('app_batch_id', $batch->getId());

            $stmt->execute();
        }
    }

    private function processFishLifeCycleDetails($reports, Api $batch)
    {
        foreach ($reports as $report) {
            /* @var FishLifeCycle $findParent */
            $findParent = $this->getDoctrine()->getRepository(FishLifeCycle::class)->findOneBy(['appId' => $report['fish_life_cycle_id'],'appBatch' => $batch]);
            if ($findParent)
            {
                $createdAt = (new \DateTime($report['created_at']))->format('Y-m-d H:i:s');
                $reportingDate = $report['reporting_date'] ? (new \DateTime($report['reporting_date']))->format('Y-m-d') : null;
                $stockingDate = $report['stocking_date'] ? (new \DateTime($report['stocking_date']))->format('Y-m-d') : null;
                $harvestDate = $report['harvest_date'] ? (new \DateTime($report['harvest_date']))->format('Y-m-d') : null;
                $previousSamplingDate = $report['previous_sampling_date'] ? (new \DateTime($report['previous_sampling_date']))->format('Y-m-d') : null;
                $presentSamplingDate = $report['present_sampling_date'] ? (new \DateTime($report['present_sampling_date']))->format('Y-m-d') : null;

                $sql = "INSERT INTO `crm_fish_life_cycle_details`(`fish_life_cycle_id`, `agent_id`, `customer_id`, `hatchery_id`, `feed_id`, `reporting_date`, `feed_item_name`, `other_culture_species`, `culture_area_decimal`, `no_of_initial_fish`, `no_of_final_fish`, `stocking_density`, `average_initial_weight`, `total_initial_weight`, `current_culture_days`, `total_day_of_culture`, `average_present_weight`, `weightGainGm`, `weightGainKg`, `previous_final_weight_gm`, `final_weight_gm`, `final_weight_kg`, `current_feed_consumption_kg`, `previous_total_feed_consumption_kg`, `total_feed_consumption_kg`, `current_fcr`, `current_adg`, `final_fcr`, `final_adg`, `sr_percentage`, `per_pcs_seed_cost`, `total_seed_cost`, `per_kg_feed_rate`, `total_feed_cost`, `feed_cost_per_kg_fish`, `total_other_cost`, `total_cost`, `production_cost_per_kg_fish`, `sales_price_per_kg`, `total_income`, `net_profit_or_loss`, `retune_over_investment`, `stocking_date`, `harvest_date`, `previous_sampling_date`, `present_sampling_date`, `farmer_remarks`, `employee_remarks`, `created_at`, `app_id`, `app_batch_id`) 
VALUES (
:fish_life_cycle_id, :agent_id, :customer_id, :hatchery_id, :feed_id, :reporting_date, :feed_item_name, :other_culture_species, :culture_area_decimal, :no_of_initial_fish, :no_of_final_fish, :stocking_density, :average_initial_weight, :total_initial_weight, :current_culture_days, :total_day_of_culture, :average_present_weight, :weightGainGm, :weightGainKg, :previous_final_weight_gm, :final_weight_gm, :final_weight_kg, :current_feed_consumption_kg, :previous_total_feed_consumption_kg, :total_feed_consumption_kg, :current_fcr, :current_adg, :final_fcr, :final_adg, :sr_percentage, :per_pcs_seed_cost, :total_seed_cost, :per_kg_feed_rate, :total_feed_cost, :feed_cost_per_kg_fish, :total_other_cost, :total_cost, :production_cost_per_kg_fish, :sales_price_per_kg, :total_income, :net_profit_or_loss, :retune_over_investment, :stocking_date, :harvest_date, :previous_sampling_date, :present_sampling_date, :farmer_remarks, :employee_remarks, :created_at, :app_id, :app_batch_id
)";
                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('fish_life_cycle_id', $findParent->getId());
                $stmt->bindValue('agent_id', $findParent->getCustomer()->getAgent()->getId());
                $stmt->bindValue('customer_id', $findParent->getCustomer()->getId());
                $stmt->bindValue('hatchery_id', $report['hatchery_id']);
                $stmt->bindValue('feed_id', $report['feed_id']);
                $stmt->bindValue('reporting_date', $reportingDate);
                $stmt->bindValue('feed_item_name', $report['feed_item_name']);
                $stmt->bindValue('other_culture_species', $report['other_culture_species']);
                $stmt->bindValue('culture_area_decimal', $report['culture_area_decimal']);
                $stmt->bindValue('no_of_initial_fish', (double)$report['no_of_initial_fish'] ?: 0);
                $stmt->bindValue('no_of_final_fish', (double)$report['no_of_final_fish'] ?: 0);
                $stmt->bindValue('stocking_density', (double)$report['stocking_density'] ?: 0);
                $stmt->bindValue('average_initial_weight', (double)$report['average_initial_weight'] ?: 0);
                $stmt->bindValue('total_initial_weight', (double)$report['total_initial_weight'] ?: 0);
                $stmt->bindValue('current_culture_days', (double)$report['current_culture_days'] ?: 0);
                $stmt->bindValue('total_day_of_culture', (double)$report['total_day_of_culture'] ?: 0);
                $stmt->bindValue('average_present_weight', (double)$report['average_present_weight'] ?: 0);
                $stmt->bindValue('weightGainGm', (double)$report['weightGainGm'] ?: 0);
                $stmt->bindValue('weightGainKg', (double)$report['weightGainKg'] ?: 0);
                $stmt->bindValue('previous_final_weight_gm', (double)$report['previous_final_weight_gm'] ?: 0);
                $stmt->bindValue('final_weight_gm', (double)$report['final_weight_gm'] ?: 0);
                $stmt->bindValue('final_weight_kg', (double)$report['final_weight_kg'] ?: 0);
                $stmt->bindValue('current_feed_consumption_kg', (double)$report['current_feed_consumption_kg'] ?: 0);
                $stmt->bindValue('previous_total_feed_consumption_kg', (double)$report['previous_total_feed_consumption_kg'] ?: 0);
                $stmt->bindValue('total_feed_consumption_kg', (double)$report['total_feed_consumption_kg'] ?: 0);
                $stmt->bindValue('current_fcr', (double)$report['current_fcr'] ?: 0);
                $stmt->bindValue('current_adg', (double)$report['current_adg'] ?: 0);
                $stmt->bindValue('final_fcr', (double)$report['final_fcr'] ?: 0);
                $stmt->bindValue('final_adg', (double)$report['final_adg'] ?: 0);
                $stmt->bindValue('sr_percentage', (double)$report['sr_percentage'] ?: 0);
                $stmt->bindValue('per_pcs_seed_cost', (double)$report['per_pcs_seed_cost'] ?: 0);
                $stmt->bindValue('total_seed_cost', (double)$report['total_seed_cost'] ?: 0);
                $stmt->bindValue('per_kg_feed_rate', (double)$report['per_kg_feed_rate'] ?: 0);
                $stmt->bindValue('total_feed_cost', (double)$report['total_feed_cost'] ?: 0);
                $stmt->bindValue('feed_cost_per_kg_fish',(double)$report['feed_cost_per_kg_fish'] ?: 0);
                $stmt->bindValue('total_other_cost', (double)$report['total_other_cost'] ?: 0);
                $stmt->bindValue('total_cost', (double)$report['total_cost'] ?: 0);
                $stmt->bindValue('production_cost_per_kg_fish', (double)$report['production_cost_per_kg_fish'] ?: 0);
                $stmt->bindValue('sales_price_per_kg', (double)$report['sales_price_per_kg'] ?: 0);
                $stmt->bindValue('total_income', (double)$report['total_income'] ?: 0);
                $stmt->bindValue('net_profit_or_loss', (double)$report['net_profit_or_loss'] ?: 0);
                $stmt->bindValue('retune_over_investment', (double)$report['retune_over_investment'] ?: 0);
                $stmt->bindValue('stocking_date', $stockingDate);
                $stmt->bindValue('harvest_date', $harvestDate);
                $stmt->bindValue('previous_sampling_date', $previousSamplingDate);
                $stmt->bindValue('present_sampling_date', $presentSamplingDate);
                $stmt->bindValue('farmer_remarks', $report['farmer_remarks']);
                $stmt->bindValue('employee_remarks', $report['employee_remarks']);
                $stmt->bindValue('created_at',$createdAt);
                $stmt->bindValue('app_id', $report['id']);
                $stmt->bindValue('app_batch_id', $batch->getId());

                $stmt->execute();

            }

        }
    }

    private function processFishLifeCycleDetailsSpecies($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $findParent = $this->getDoctrine()->getRepository(FishLifeCycleDetails::class)->findOneBy(['appId' => $report['fish_life_cycle_details_id'], 'appBatch' => $batch]);
            if($findParent){
                $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;


                $sql = "INSERT INTO `crm_fish_life_cycle_detail_species`(`fish_life_cycle_details_id`, `feed_type_id`, `feed_consumption_kg`, `created_at`, `mainCultureSpecies`) 
VALUES (
:fish_life_cycle_details_id, :feed_type_id, :feed_consumption_kg, :created_at, :mainCultureSpecies
)";
                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('fish_life_cycle_details_id', $findParent->getId());
                $stmt->bindValue('feed_type_id', $report['feed_type_id']);
                $stmt->bindValue('feed_consumption_kg', $report['feed_consumption_kg']);
                $stmt->bindValue('mainCultureSpecies', $report['mainCultureSpecies']);
                $stmt->bindValue('created_at', $createdAt);

                $stmt->execute();

            }

        }
    }

    private function processAgentUpgradtion($reports, Api $batch)
    {
        foreach ($reports as $report) {
            $createdAt = (new \DateTime($report['created_at']))->format('Y-m-d H:i:s');
            $reportingMonth = (new \DateTime($report['reporting_month']))->format('Y-m-d');
            $breed=$report['breed_name'];
            $employee=$report['employee_id'];
            $agent= $report['agent_id'];

            $existingReport= $this->getDoctrine()->getRepository(AgentUpgradationReport::class)->duplicateCheckSyncAgentUpgradationReport($reportingMonth, $breed, $employee, $agent);

            if($existingReport){
                $sql = "UPDATE `crm_agent_upgradation_report` SET `agent_status`= :agent_status,`previous_sale_ton`= :previous_sale_ton,`present_sale_ton`= :present_sale_ton,`remarks`= :remarks WHERE id = :id";  // every time exits when create new farmer
                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                $stmt->bindValue('id', $existingReport->getId());
                $stmt->bindValue('agent_status', $report['agent_status']);
                $stmt->bindValue('previous_sale_ton', $report['previous_sale_ton']);
                $stmt->bindValue('present_sale_ton', $report['present_sale_ton']);
                $stmt->bindValue('remarks', $report['remarks']);
                $stmt->execute();
            }else{
                $sql = "INSERT INTO `crm_agent_upgradation_report`(`agent_purpose_id`, `agent_id`, `employee_id`, `breed_name`, `agent_status`, `previous_sale_ton`, `present_sale_ton`, `remarks`, `created_at`, `reporting_month`) VALUES (:agent_purpose_id, :agent_id, :employee_id, :breed_name, :agent_status, :previous_sale_ton, :present_sale_ton, :remarks, :created_at, :reporting_month)";
                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('agent_purpose_id', $report['agent_purpose_id']);
                $stmt->bindValue('agent_id', $report['agent_id']);
                $stmt->bindValue('employee_id', $report['employee_id']);
                $stmt->bindValue('breed_name', $report['breed_name']);
                $stmt->bindValue('agent_status', $report['agent_status']);
                $stmt->bindValue('previous_sale_ton', $report['previous_sale_ton']);
                $stmt->bindValue('present_sale_ton', $report['present_sale_ton']);
                $stmt->bindValue('remarks', $report['remarks']);
                $stmt->bindValue('created_at',$createdAt);
                $stmt->bindValue('reporting_month',$reportingMonth);
                $stmt->execute();

            }

        }
    }

    private function processFarmerTraining($reports, Api $batch)
    {
        foreach ($reports as $report) {

            $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;
            $trainingDate = $report['training_date'] ? (new \DateTime($report['training_date']))->format('Y-m-d') : null;

            $sql = "INSERT INTO `crm_farmer_training_report`(`agent_purpose_id`, `agent_id`, `employee_id`, `breed_name`, `training_date`, `training_material`, `training_topics`, `remarks`, `created_at`, `app_batch_id`, `app_id`) VALUES (:agent_purpose_id, :agent_id, :employee_id, :breed_name, :training_date, :training_material, :training_topics, :remarks, :created_at, :app_batch_id, :app_id)";

            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

            $stmt->bindValue('agent_purpose_id', $report['agent_purpose_id']);
            $stmt->bindValue('agent_id', $report['agent_id']);
            $stmt->bindValue('employee_id', $report['employee_id']);
            $stmt->bindValue('breed_name', $report['breed_name']);
            $stmt->bindValue('training_date', $trainingDate);
            $stmt->bindValue('training_material', $report['training_material']);
            $stmt->bindValue('training_topics', $report['training_topics']);
            $stmt->bindValue('remarks', $report['remarks']);
            $stmt->bindValue('created_at', $createdAt);
            $stmt->bindValue('app_batch_id', $batch->getId());
            $stmt->bindValue('app_id', $report['id']);

            $stmt->execute();
        }
    }
    private function processFarmerTrainingDetails($reports, Api $batch)
    {
        foreach ($reports as $report) {
            /**
             * @var FarmerTrainingReport $farmerTraining
             */
            $farmerTraining = $this->getDoctrine()->getRepository(FarmerTrainingReport::class)->findOneBy(['appBatch' => $batch, 'appId' => $report['farmer_training_report_id']]);

            if ($farmerTraining){
                $createdAt = $report['created_at'] ? (new \DateTime($report['created_at']))->format('Y-m-d H:i:s') : null;

                $sql = "INSERT INTO `crm_farmer_training_report_details`(`farmer_training_report_id`, `customer_id`, `training_material_qty`, `farmer_capacity`, `created_at`) VALUES (:farmer_training_report_id, :customer_id, :training_material_qty, :farmer_capacity, :created_at)";

                $stmt = $this->getDoctrine()->getConnection()->prepare($sql);

                $stmt->bindValue('farmer_training_report_id', $farmerTraining->getId());
                $stmt->bindValue('customer_id', $report['customer_id']);
                $stmt->bindValue('training_material_qty', $report['training_material_qty']);
                $stmt->bindValue('farmer_capacity', $report['farmer_capacity']);
                $stmt->bindValue('created_at', $createdAt);

                $stmt->execute();
            }

        }
    }

}
