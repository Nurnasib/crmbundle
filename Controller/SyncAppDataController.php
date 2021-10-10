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
use App\Entity\Core\Agent;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Terminalbd\CrmBundle\Entity\Api;
use Terminalbd\CrmBundle\Entity\ApiDetails;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\CrmVisitDetails;
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
        $records = $this->getDoctrine()->getRepository(ApiDetails::class)->getReportsList();
        $countVisit = $this->getDoctrine()->getRepository(ApiDetails::class)->findBy(['status' => 0, 'process' => 'crm_visit']);
        $countVisitDetails = $this->getDoctrine()->getRepository(ApiDetails::class)->findBy(['status' => 0, 'process' => 'crm_visit_details']);
//        dd(count($countVisit), count($countVisitDetails));
        $records = $this->pagination($request, $records);

        return $this->render('@TerminalbdCrm/api/api-response-list.html.twig',[
            'records' => $records,
            'countVisit' => count($countVisit),
            'countVisitDetails' => count($countVisitDetails),
        ]);
    }

    /**
     * @Route("/crm-visit", name="_crm_visit")
     */
    public function syncCrmVisit()
    {
        set_time_limit(0);
        ignore_user_abort(true);

        $feedback = [];
        $batches = $this->getDoctrine()->getRepository(Api::class)->findBy(['status' => 0]);
        foreach ($batches as $batch) {
//            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appBatch' => $batch]);

//            if (!$findVisit){
            if (true){

                $em = $this->getDoctrine()->getManager();
                $details = $batch->getApiDetails();
                foreach ($details as $detail) {

                    $jsonToArray = json_decode($detail->getJsonData(), true);

                    if ($detail->getProcess() == 'crm_visit'){
                        foreach ($jsonToArray as $visitKey => $visit) {
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
//                                $em->persist($newVisit);
//                                $em->flush();

                                /*                            $sql = "INSERT INTO `crm_visit`(`created`, `working_duration`, `employee_id`, `location_id`, `working_duration_to`, `app_id`) VALUES (:created_at, :duration_from, :employee_id, :location_id, :duration_to, :app_id)";

                                                            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                                                            $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                                                            $stmt->bindValue('duration_from', $visit['duration_from']);
                                                            $stmt->bindValue('employee_id', $visit['employee_id']);
                                                            $stmt->bindValue('location_id', $visit['location_id']);
                                                            $stmt->bindValue('duration_to', $visit['duration_to']);
                                                            $stmt->bindValue('app_id', $visit['id']);
                                                            $stmt->execute();*/

                                $detail->setStatus(true);
//                            $em->persist($detail);
//                            $em->flush();
                            }
//                            if ($visitKey == array_key_last($jsonToArray)){
//
//                            }
                        }
                    }elseif ($detail->getProcess() == 'crm_visit_details'){
                        foreach ($jsonToArray as $visitDetail) {
                            $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $visitDetail['crm_visit_id'], 'appBatch' => $batch]);
                            $findFarmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($visitDetail['customer_id']);
                            $findAgent = $this->getDoctrine()->getRepository(Agent::class)->find($visitDetail['agent_id']);
                            $findPurpose = $this->getDoctrine()->getRepository(Setting::class)->find($visitDetail['purpose_id']);
                            $findFirmType = $this->getDoctrine()->getRepository(Setting::class)->find($visitDetail['firm_type_id']);
                            $findReport = $this->getDoctrine()->getRepository(Setting::class)->find($visitDetail['report_id']);

                            if ($findVisit && $findFarmer && $findAgent && $findReport){
                                $createdAt = new \DateTime($visitDetail['created']);

                                $crmVisitDetails = new CrmVisitDetails();
                                $crmVisitDetails->setCrmVisit($findVisit);
                                $crmVisitDetails->setCrmCustomer($findFarmer);
                                $crmVisitDetails->setAgent($findAgent);
                                $crmVisitDetails->setPurpose($findPurpose);
                                $crmVisitDetails->setFirmType($findFirmType);
                                $crmVisitDetails->setReport($findReport);
                                $crmVisitDetails->setFarmCapacity($visitDetail['farmCapacity']);

                                $crmVisitDetails->setComments($visitDetail['comments']);
                                $crmVisitDetails->setProcess($visitDetail['process'] ?: '');
                                $crmVisitDetails->setCreated($createdAt);

//                                $em->persist($crmVisitDetails);
//                                $em->flush();
                            }
                        }
                        $detail->setStatus(true);
//                        $em->persist($detail);
//                        $em->flush();

                    }elseif ($detail->getProcess() == 'crm_layer_performance_details'){
                        foreach ($jsonToArray as $layerPerformance) {
                            $sql = "INSERT INTO 
`crm_layer_performance_details`
(`employee_id`, `report_id`, `agent_id`, `customer_id`, `hatchery_id`, `breed_id`, `feed_id`, `feed_mill_id`, `feed_type_id`, `color_id`, `repoting_month`, `total_birds`, `age_week`, `bird_weight_achieved`, `bird_weight_target`, `feed_intake_per_bird`, `feed_Target`, `egg_production_achieved`, `egg_production_target`, `egg_weight_achieved`, `egg_weight_stand`, `production_date`, `batch_no`, `disease`, `remarks`, `created`, `updated`) VALUES 
(:employee_id, :report_id, :agent_id, :customer_id, :hatchery_id, :breed_id, :feed_id, :feed_mill_id, :feed_type_id, :color_id, :repoting_month, :total_birds, :age_week, :bird_weight_achieved, :bird_weight_target, :feed_intake_per_bird, :feed_Target, :egg_production_achieved, :egg_production_target, :egg_weight_achieved, :egg_weight_stand, :production_date, :batch_no, :disease, :remarks, :created, :updated)";

                            $repotingMonth = new \DateTime($layerPerformance['repoting_month']);
                            $createdAt = new \DateTime($layerPerformance['created']);
                            $updatedAt = new \DateTime($layerPerformance['updated']);
                            $productionDate = new \DateTime($layerPerformance['production_date']);

                            $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                            $stmt->bindValue('employee_id', $layerPerformance['employee_id']);
                            $stmt->bindValue('report_id', $layerPerformance['report_id']);
                            $stmt->bindValue('agent_id', $layerPerformance['agent_id']);
                            $stmt->bindValue('customer_id', $layerPerformance['customer_id']);
                            $stmt->bindValue('hatchery_id', $layerPerformance['hatchery_id']);
                            $stmt->bindValue('breed_id', $layerPerformance['breed_id']);
                            $stmt->bindValue('feed_id', $layerPerformance['feed_id']);
                            $stmt->bindValue('feed_mill_id', $layerPerformance['feed_mill_id']);
                            $stmt->bindValue('feed_type_id', $layerPerformance['feed_type_id']);
                            $stmt->bindValue('color_id', $layerPerformance['color_id']);
                            $stmt->bindValue('repoting_month', $repotingMonth->format('Y-m-d'));
                            $stmt->bindValue('total_birds', $layerPerformance['total_birds']);
                            $stmt->bindValue('age_week', $layerPerformance['age_week']);
                            $stmt->bindValue('bird_weight_achieved', $layerPerformance['bird_weight_achieved']);
                            $stmt->bindValue('bird_weight_target', $layerPerformance['bird_weight_target']);
                            $stmt->bindValue('feed_intake_per_bird', $layerPerformance['feed_intake_per_bird']);
                            $stmt->bindValue('feed_Target', $layerPerformance['feed_Target']);
                            $stmt->bindValue('egg_production_achieved', $layerPerformance['egg_production_achieved']);
                            $stmt->bindValue('egg_production_target', $layerPerformance['egg_production_target']);
                            $stmt->bindValue('egg_weight_achieved', $layerPerformance['egg_weight_achieved']);
                            $stmt->bindValue('egg_weight_stand', $layerPerformance['egg_weight_stand']);
                            $stmt->bindValue('production_date', $productionDate->format('Y-m-d'));
                            $stmt->bindValue('batch_no', $layerPerformance['batch_no']);
                            $stmt->bindValue('disease', $layerPerformance['disease']);
                            $stmt->bindValue('remarks', $layerPerformance['remarks']);
                            $stmt->bindValue('created', $createdAt->format('Y-m-d H:i:s'));
                            $stmt->bindValue('updated', $updatedAt->format('Y-m-d H:i:s'));
                            $stmt->execute();
                        }
                        $detail->setStatus(true);
//                        $em->persist($detail);
//                        $em->flush();
                    }elseif ($detail->getProcess() == 'crm_cattle_performance_details'){
                        foreach ($jsonToArray as $cattlePerformance) {
                            dd($cattlePerformance);
                        }
                    }
                    $detail->setStatus(true);
//                $em->persist($detail);
//                $em->flush();
                }
            }

            dd('done!');
            $batch->setStatus(true);
//            $em->persist($batch);
//            $em->flush();
        }
        $records = $this->getDoctrine()->getRepository(ApiDetails::class)->findBy(['status' => 0, 'process' => 'crm_visit']);
//        $records = $this->getDoctrine()->getRepository(Api::class)->findBy(['status' => 0]);
/*        foreach ($records as $record) {
            foreach ($record->getApiDetails() as $child){
                dump($child->getId());
            }
        }
        dd('done');*/

        foreach ($records as $record) {
            dd($record);
            if ($record->getJsonData()){
                $jsonToArray = json_decode($record->getJsonData(), true);
                foreach ($jsonToArray as $visit) {
                    $createdAt = new \DateTime($visit['created_at']);
                    $findEmployee = $this->getDoctrine()->getRepository(User::class)->find($visit['employee_id']);
                    $findLocation = $this->getDoctrine()->getRepository(Location::class)->find($visit['location_id']);

                    if ($findEmployee && $findLocation){
                        $sql = "INSERT INTO `crm_visit`(`created`, `working_duration`, `employee_id`, `location_id`, `working_duration_to`, `app_id`) VALUES (:created_at, :duration_from, :employee_id, :location_id, :duration_to, :app_id)";

                        $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
                        $stmt->bindValue('created_at', $createdAt->format('Y-m-d H:i:s'));
                        $stmt->bindValue('duration_from', $visit['duration_from']);
                        $stmt->bindValue('employee_id', $visit['employee_id']);
                        $stmt->bindValue('location_id', $visit['location_id']);
                        $stmt->bindValue('duration_to', $visit['duration_to']);
                        $stmt->bindValue('app_id', $visit['id']);
                        $stmt->execute();

                        $record->setStatus(true);
                        $this->getDoctrine()->getManager()->persist($record);
                        $this->getDoctrine()->getManager()->flush();

                        $feedback[] = $record->getId();
                }else{
                        $feedback = [];
                        $this->addFlash('error', 'Something Wrong!');
                    }
                }
            }else{
                $feedback = [];
                $this->addFlash('error', 'Data not found!');
            }

        }
        if ($feedback){
            $this->addFlash('success', 'Synchronised successfully!');
        }

        return $this->redirectToRoute('crm_sync_app_data_index');
        
    }

    /**
     * @Route("/crm-visit-details", name="_crm_visit_details")
     */
    public function syncCrmVisitDetails()
    {
        return $this->redirectToRoute('crm_sync_app_data_index');

        set_time_limit(0);
        ignore_user_abort(true);

        $records = $this->getDoctrine()->getRepository(ApiDetails::class)->findBy(['status' => 0, 'process' => 'crm_visit_details']);
        foreach ($records as $record) {
            if ($record->getJsonData()){
                $jsonToArray = json_decode($record->getJsonData(), true);
            }
        }
    }
}
