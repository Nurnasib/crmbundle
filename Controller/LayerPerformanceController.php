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

use App\Entity\Core\Agent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\CrmCustomer;
use Terminalbd\CrmBundle\Entity\CrmVisit;
use Terminalbd\CrmBundle\Entity\Fcr;
use Terminalbd\CrmBundle\Entity\LayerPerformance;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Entity\LayerStandard;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\FcrFormType;
use Terminalbd\CrmBundle\Form\LayerPerformanceFormType;
use Terminalbd\CrmBundle\Form\SearchFilterFormType;


/**
 * @Route("/crm/layer/performance")
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_DEVELOPER')")
 */
class LayerPerformanceController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="layer_performance")
     * @return Response
     */
    public function index(): Response
    {
        $entities = $this->getDoctrine()->getRepository(LayerPerformance::class)->findBy(array('employee'=>$this->getUser()));
        return $this->render('@TerminalbdCrm/layerPerformance/index.html.twig',['entities' => $entities]);
    }

    /**
     * @Route("/visit/{visit}/customer/{id}/report/{report}/new", methods={"GET", "POST"}, name="layer_performance_new")
     * @param Request $request
     * @param CrmCustomer $crmCustomer
     * @param Setting $report
     * @param CrmVisit $visit
     * @return Response
     */
    public function new(Request $request, CrmCustomer $crmCustomer, Setting $report, CrmVisit $visit): Response
    {
        $data = $request->request->all();
        $noOfWeek = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->getLifeCycleWeekByLifeCycle($report->getSlug());
        $breeds = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'BREED_TYPE','parent'=>$report->getParent()),['name' => 'ASC']);
        $hatcheries = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'HATCHERY'),['name' => 'ASC']);
        $feedTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FEED_TYPE','parent'=>$report->getParent()),['name' => 'ASC']);
        $feedMills = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FEED_MILL'),['name' => 'ASC']);
        $colors = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'COLOR'),['name' => 'ASC']);

        $layerPerformanceDetails = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->getLayerPerformanceReportByReportingDateAndFeedType( $report, $this->getUser());

        return $this->render('@TerminalbdCrm/layerPerformance/details-modal.html.twig', [
            'noOfWeeks' => $noOfWeek,
            'breeds' => $breeds,
            'hatcheries' => $hatcheries,
            'feedTypes' => $feedTypes,
            'feedMills' => $feedMills,
            'colors' => $colors,
            'customer' => $crmCustomer,
            'report' => $report,
            'employee' => $this->getUser(),
            'crmLayerPerformanceDetails' => $layerPerformanceDetails,
            'visit' => $visit,
        ]);
    }

    /**
     * @Route("/details/{id}/delete", methods={"POST"}, name="layer_parformance_details_delete")
     * @param $id
     * @return Response
     */
    public function deleteDetails($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


    /**
     * Deletes a Fcr entity.
     * @Route("/soft/delete/{id}/by/admin/user", methods={"POST","GET"}, name="layer_parformance_details_soft_delete_by_admin_user", options={"expose"=true})
     * @param $id
     * @return Response
     * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_DEVELOPER')")
     */
    public function softDeleteLayerParformanceDetailsByAdminUser($id): Response
    {

        $entity = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $entity->setDeletedBy($this->getUser());
        $entity->setDeletedAt(new \DateTime('now'));
        $em->persist($entity);
        $em->flush();

        $requestAllData = $_REQUEST;
        $data = isset($requestAllData['search_filter_form'])&&$requestAllData['search_filter_form']?$requestAllData['search_filter_form']:[];
        /*return new JsonResponse($data['search_filter_form']);*/
        $reportId = isset( $data['monthlyReport'])&&$data['monthlyReport']? $data['monthlyReport']:'';
        $report=$entity->getReport();

        $filterBy['startDate'] = isset( $data['startDate'])&&$data['startDate']? date('Y-m-d', strtotime($data['startDate'])):'';
        $filterBy['endDate'] = isset( $data['endDate'])&&$data['endDate']? date('Y-m-d', strtotime($data['endDate'])):'';
        $filterBy['employeeId'] = isset( $data['employee'])&&$data['employee']?$data['employee']:'';
        $filterBy['feedMill'] = isset( $data['feedMill'])&&$data['feedMill']?$data['feedMill']:'';
        $filterBy['region'] = isset( $data['region'])&&$data['region']?$data['region']:'';
        $filterBy['feedCompany'] = isset($data['feedCompany'])&&$data['feedCompany']? $data['feedCompany'] : '';
        $employee=null;
//        return new JsonResponse($requestAllData['search_filter_form']);

        $entities = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->getLayerPerformanceReportByEmployeeAndDate($report, $filterBy, $this->getUser());
//        return new JsonResponse($entities);
        $htmlProcess='';
        if($report&&($report->getSlug()=='layer-performance-brown'||$report->getSlug()=='layer-performance-white')){
            $htmlProcess = $this->renderView(
                '@TerminalbdCrm/report/monthlyReport/inc/poultry/_layer_performance.html.twig', array(
                    'entities' => $entities,
                    'filterBy' => $filterBy,
                    'reportSlug' => $report ? $report->getSlug() : null,
                    'employee' => $employee,
                    'report' => $report,
                )
            );
        }

        return new Response($htmlProcess);
    }


    /**
     * @Route("/visit/{visit}/{id}/details/add", methods={"POST"}, name="crm_layer_performance_detail_report_add", options={"expose"=true})
     * @param Request $request
     * @param Setting $report
     * @param CrmVisit $visit
     * @return Response
     * @throws \Exception
     */

    public function addLayerPerformanceDetails(Request $request, CrmVisit $visit, Setting $report): Response
    {
        $data = $request->request->all();

        $hatchery = null;
        $customer = null;
        $breed = null;
        $color =null;
        $feedType= null;
        $feedMill = null;

        if(isset($data['hatchery'])&&$data['hatchery']!=''){
            $hatchery = $this->getDoctrine()->getRepository(Setting::class)->find($data['hatchery']);
        }

        if(isset($data['customerId'])&&$data['customerId']!=''){
            $customer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($data['customerId']);
        }

        if(isset($data['breed'])&&$data['breed']!=''){
            $breed = $this->getDoctrine()->getRepository(Setting::class)->find($data['breed']);
        }
        if(isset($data['color'])&&$data['color']!=''){
            $color = $this->getDoctrine()->getRepository(Setting::class)->find($data['color']);
        }
        if(isset($data['feedMill'])&&$data['feedMill']!=''){
            $feedMill = $this->getDoctrine()->getRepository(Setting::class)->find($data['feedMill']);
        }
        if(isset($data['feedType'])&&$data['feedType']!=''){
            $feedType = $this->getDoctrine()->getRepository(Setting::class)->find($data['feedType']);
        }
        $entity = new LayerPerformanceDetails();
        $entity->setTotalBirds(isset($data['totalBirds'])&&$data['totalBirds']!=""?(float)$data['totalBirds']:0);
        $entity->setAgeWeek(isset($data['ageWeek'])&&$data['ageWeek']!=""?(float)$data['ageWeek']:0);
        $entity->setBirdWeightAchieved(isset($data['bodyWeightAchieved'])&&$data['bodyWeightAchieved']!=""?(float)$data['bodyWeightAchieved']:0);
        $entity->setFeedIntakePerBird(isset($data['feedIntakePerBird'])&&$data['feedIntakePerBird']!=""?(float)$data['feedIntakePerBird']:0);
        $entity->setEggProductionAchieved(isset($data['eggProductionAchieved'])&&$data['eggProductionAchieved']!=""? (float)$data['eggProductionAchieved']:0);
        $entity->setEggWeightAchieved(isset($data['eggWeightAchieved'])&&$data['eggWeightAchieved']!=""?(float)$data['eggWeightAchieved']:0);

        $proDate = isset($data['productionDate'])&&$data['productionDate']!=""?date('Y-m-d',strtotime($data['productionDate'])):date('Y-m-d',strtotime('now'));
        $entity->setProductionDate(new \DateTime($proDate));
        $entity->setFeedType($feedType);
        $entity->setHatchery($hatchery);
        $entity->setBreed($breed);
        $entity->setColor($color);
        $entity->setFeedMill($feedMill);
        $entity->setDisease(isset($data['disease'])?$data['disease']:'');
        $entity->setBatchNo(isset($data['batchNo'])?$data['batchNo']:'');
        $entity->setRemarks(isset($data['remarks'])?$data['remarks']:'');
        $entity->setCustomer($customer);
        $entity->setAgent($customer?$customer->getAgent():null);

        $entity->setEmployee($this->getUser());
        $entity->setReport($report);
        $entity->setVisit($visit);

        $reportingDate = date('Y-m-d',strtotime('now'));
        $entity->setReportingMonth(new \DateTime($reportingDate));

        /* @var LayerStandard $layerPerformanceStandard*/
        $layerPerformanceStandard= $this->getDoctrine()->getRepository(LayerStandard::class)->findOneBy(array('age'=>$entity->getAgeWeek(),'report'=>$report));
        if($layerPerformanceStandard){
            $entity->setBirdWeightTarget($layerPerformanceStandard->getTargetBodyWeight());
            $entity->setFeedTarget($layerPerformanceStandard->getTargetFeedConsumption());
            $entity->setEggProductionTarget($layerPerformanceStandard->getTargetEggProduction());
            $entity->setEggWeightStand($layerPerformanceStandard->getTargetEggWeight());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new JsonResponse(
            array(
                'success'=>'Success',
                'data'=>$data,
                'status'=>200,
            )
        );

    }

    /**
     * @param Setting $report
     * @Route("/{id}/details/refresh", methods={"GET", "POST"}, name="layer_performance_details_refresh", options={"expose"=true})
     * @return Response
     */
    public function layerPerformanceDetailsRefresh(Setting $report): Response
    {
        $layerPerformanceDetails = $this->getDoctrine()->getRepository(LayerPerformanceDetails::class)->getLayerPerformanceReportByReportingDateAndFeedType( $report, $this->getUser());

        return $this->render('@TerminalbdCrm/layerPerformance/partial/layer-performance-details.html.twig', [
            'crmLayerPerformanceDetails' => $layerPerformanceDetails,
        ]);
    }


}
