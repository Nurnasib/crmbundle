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
 */
class LayerPerformanceController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="layer_performance")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index(Request $request): Response
    {
        $entities = $this->getDoctrine()->getRepository(LayerPerformance::class)->findBy(array('employee'=>$this->getUser()));
        return $this->render('@TerminalbdCrm/layerPerformance/index.html.twig',['entities' => $entities]);
    }

    /**
     * @Route("/report", methods={"GET","POST"}, name="layer_performance_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function indexReport(Request $request): Response
    {
        $entities = [];
        $searchForm = $this->createForm(SearchFilterFormType::class)->remove('farmer')->remove('startDate')->remove('endDate');

        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()){
            $filterBy = $searchForm->getData();
//            dd($filterBy);
            $entities = $this->getDoctrine()->getRepository(LayerPerformance::class)->getLayerPerformanceReport($filterBy);
        }

        return $this->render('@TerminalbdCrm/layerPerformance/report/report.html.twig',['searchForm' => $searchForm->createView(), 'entities' => $entities]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/customer/{id}/report/{report}/new", methods={"GET", "POST"}, name="layer_performance_new")
     */
    public function new(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {
        $entity = new LayerPerformance();

        $existingReport = $this->getDoctrine()->getRepository(LayerPerformance::class)->getLayerPerformanceReportByReportingDateAndFeedType($report, $this->getUser());

        if($existingReport){
            return $this->redirectToRoute('layer_performance_details_modal', ['id'=>$existingReport->getId(), 'customer'=>$crmCustomer->getId()]);
        }

        $reportingDate = date('Y-m-d',strtotime('now'));

        $entity->setReportingMonth(new \DateTime($reportingDate));
        $entity->setReport($report);
//        $entity->setCustomer($crmCustomer);
        $entity->setEmployee($this->getUser());
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();
        $this->addFlash('success', 'post.created_successfully');

        return $this->redirectToRoute('layer_performance_details_modal', ['id'=>$entity->getId(), 'customer'=>$crmCustomer->getId()]);

    }

    /**
     * @param LayerPerformance $layerPerformance
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{id}/details/modal", methods={"GET", "POST"}, name="layer_performance_details_modal")
     */
    public function newModal(Request $request, LayerPerformance $layerPerformance): Response
    {
        $data = $request->request->all();
        $noOfWeek = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->getLifeCycleWeekByLifeCycle($layerPerformance->getReport()->getSlug());
        $breeds = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'BREED_TYPE','parent'=>$layerPerformance->getReport()->getParent()),['name' => 'ASC']);
        $hatcheries = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'HATCHERY'),['name' => 'ASC']);
        $feedTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FEED_TYPE','parent'=>$layerPerformance->getReport()->getParent()),['name' => 'ASC']);
        $feedMills = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FEED_MILL'),['name' => 'ASC']);
        $colors = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'COLOR'),['name' => 'ASC']);

        return $this->render('@TerminalbdCrm/layerPerformance/details-modal.html.twig', [
            'layerPerformance' => $layerPerformance,
            'noOfWeeks' => $noOfWeek,
            'breeds' => $breeds,
            'hatcheries' => $hatcheries,
            'feedTypes' => $feedTypes,
            'feedMills' => $feedMills,
            'colors' => $colors,
            'customer' => $request->query->get('customer'),
        ]);
    }

    /**
     * Deletes a LayerPerformance entity.
     * @Route("/{id}/delete", methods={"GET"}, name="layer_performance_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(LayerPerformance::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


    /**
     * @Route("/details/{id}/delete", methods={"POST"}, name="layer_parformance_details_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
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
     * @Route("/{id}/details/add", methods={"POST"}, name="crm_layer_performance_detail_report_add", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM') or is_granted('ROLE_CSO')")
     */

    public function addLayerPerformanceDetails(Request $request, LayerPerformance $layerPerformance): Response
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
        $entity->setCrmLayerPerformanceReport($layerPerformance);
        /* @var LayerStandard $layerPerformanceStandard*/
        $layerPerformanceStandard= $this->getDoctrine()->getRepository(LayerStandard::class)->findOneBy(array('age'=>$entity->getAgeWeek(),'report'=>$layerPerformance->getReport()));
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
     * @param LayerPerformance $layerPerformance
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{id}/details/refresh", methods={"GET", "POST"}, name="layer_performance_details_refresh", options={"expose"=true})
     */
    public function layerPerformanceDetailsRefresh(LayerPerformance $layerPerformance): Response
    {

        return $this->render('@TerminalbdCrm/layerPerformance/partial/layer-performance-details.html.twig', [
            'layerPerformance' => $layerPerformance,
        ]);
    }


}
