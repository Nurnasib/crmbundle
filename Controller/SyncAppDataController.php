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
use Terminalbd\CrmBundle\Entity\ApiDetails;
use Terminalbd\CrmBundle\Entity\CrmVisit;


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
//            dd((object)$batch);
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
                            $em->persist($newVisit);
                            $em->flush();

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
                        if ($visitKey == array_key_last($jsonToArray)){

//                            dump($visitKey);
                        }
                    }
                }elseif ($detail->getProcess() == 'crm_visit_details'){
                    foreach ($jsonToArray as $visitDetails) {
                        $findVisit = $this->getDoctrine()->getRepository(CrmVisit::class)->findOneBy(['appId' => $visitDetails['crm_visit_id'], 'appBatch' => $batch]);
                        dd($findVisit, $visitDetails);
                    }
                }
            }
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
