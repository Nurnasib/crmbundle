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
     * @Route("/report", methods={"GET"}, name="layer_performance_report")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_AGM')")
     */
    public function indexReport(Request $request): Response
    {
        $entities = $this->getDoctrine()->getRepository(LayerPerformance::class)->findAll();
        return $this->render('@TerminalbdCrm/layerPerformance/index.html.twig',['entities' => $entities]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/new", methods={"GET", "POST"}, name="layer_performance_new")
     */
    public function new(Request $request): Response
    {
        $data = $request->request->get('layer_performance_form');
        $entity = new LayerPerformance();

        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(LayerPerformanceFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo)) ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingReport = $this->getDoctrine()->getRepository(LayerPerformance::class)->getLayerPerformanceReportByReportingDateAndFeedType($data, $this->getUser());
            if($existingReport){
                $this->addFlash('danger', 'This month report already exits');
                return $this->redirectToRoute('layer_performance_new');
            }
            $em = $this->getDoctrine()->getManager();
            $entity->setEmployee($this->getUser());
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('layer_performance_new');
            }
            return $this->redirectToRoute('layer_performance');
        }
        return $this->render('@TerminalbdCrm/layerPerformance/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing LayerPerformance entity.
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="layer_performance_edit")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function edit(Request $request, LayerPerformance $entity): Response
    {

        $form = $this->createForm(LayerPerformanceFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('layer_performance_new', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('layer_performance');
        }
        return $this->render('@TerminalbdCrm/layerPerformance/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
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
     * @param LayerPerformance $layerPerformance
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/{id}/details/modal", methods={"GET", "POST"}, name="layer_performance_details_modal")
     */
    public function newModal(Request $request, LayerPerformance $layerPerformance): Response
    {
        $data = $request->request->all();
        $agents=$this->getDoctrine()->getRepository(Agent::class)->getLocationWise($layerPerformance->getEmployee());
        $farmers =$this->getDoctrine()->getRepository(CrmCustomer::class)->getLocationWise($layerPerformance->getEmployee(),'farmer');
        $noOfWeek = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->getLifeCycleWeekByLifeCycle('layer-life-cycle');
        $breeds = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'BREED_TYPE'));

        return $this->render('@TerminalbdCrm/layerPerformance/details-modal.html.twig', [
            'layerPerformance' => $layerPerformance,
            'agents' => $agents,
            'farmers' => $farmers,
            'noOfWeeks' => $noOfWeek,
            'breeds' => $breeds,
        ]);
    }

    /**
     * @Route("/{id}/details/add", methods={"POST"}, name="crm_layer_performance_detail_report_add", options={"expose"=true})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM') or is_granted('ROLE_CSO')")
     */

    public function addFcrDetailsReport(Request $request, LayerPerformance $layerPerformance): Response
    {
        $data = $request->request->all();
        $farmer = $this->getDoctrine()->getRepository(CrmCustomer::class)->find($data['farmer']);

        $entity = new LayerPerformanceDetails();
        $entity->setTotalBirds(isset($data['totalBirds'])&&$data['totalBirds']!=""?(float)$data['totalBirds']:0);
        $entity->setAgeWeek(isset($data['ageWeek'])&&$data['ageWeek']!=""?(float)$data['ageWeek']:0);
        $entity->setBirdWeightAchieved(isset($data['bodyWeightAchieved'])&&$data['bodyWeightAchieved']!=""?(float)$data['bodyWeightAchieved']:0);
        $entity->setFeedIntakePerBird(isset($data['feedIntakePerBird'])&&$data['feedIntakePerBird']!=""?(float)$data['feedIntakePerBird']:0);
        $entity->setEggProductionAchieved(isset($data['eggProductionAchieved'])&&$data['eggProductionAchieved']!=""? (float)$data['eggProductionAchieved']:0);
        $entity->setEggWeightAchieved(isset($data['eggWeightAchieved'])&&$data['eggWeightAchieved']!=""?(float)$data['eggWeightAchieved']:0);
        $entity->setFeedType(isset($data['feedType'])?$data['feedType']:'');

        $proDate = isset($data['productionDate'])&&$data['productionDate']!=""?date('Y-m-d',strtotime($data['productionDate'])):date('Y-m-d',strtotime('now'));
        $entity->setProductionDate(new \DateTime($proDate));
        $entity->setHatchery(isset($data['hatchery'])?$data['hatchery']:'');
        $entity->setBreed($layerPerformance->getBreed());
        $entity->setColor(isset($data['color'])?$data['color']:'');
        $entity->setDisease(isset($data['disease'])?$data['disease']:'');
        $entity->setFeedMill(isset($data['feedMill'])?$data['feedMill']:'');
        $entity->setBatchNo(isset($data['batchNo'])?$data['batchNo']:'');
        $entity->setRemarks(isset($data['remarks'])?$data['remarks']:'');
        $entity->setCustomer($farmer?$farmer:null);
        $entity->setAgent($farmer?$farmer->getAgent():null);
        $entity->setCrmLayerPerformanceReport($layerPerformance);
        /* @var LayerStandard $layerPerformanceStandard*/
        $layerPerformanceStandard= $this->getDoctrine()->getRepository(LayerStandard::class)->findOneBy(array('age'=>$entity->getAgeWeek(),'breed'=>$layerPerformance->getBreed()));
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
