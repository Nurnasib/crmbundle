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
use Terminalbd\CrmBundle\Entity\LayerLifeCycle;
use Terminalbd\CrmBundle\Entity\LayerLifeCycleDetails;
use Terminalbd\CrmBundle\Entity\LayerPerformance;
use Terminalbd\CrmBundle\Entity\LayerPerformanceDetails;
use Terminalbd\CrmBundle\Entity\LayerStandard;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\FcrFormType;
use Terminalbd\CrmBundle\Form\LayerLifeCycleFormType;
use Terminalbd\CrmBundle\Form\LayerPerformanceFormType;


/**
 * @Route("/crm/layer/life/cycle")
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_DEVELOPER')")
 */
class LayerLifeCycleController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="layer_life_cycle")
     * @return Response
     */
    public function index(): Response
    {
        $entities = $this->getDoctrine()->getRepository(LayerLifeCycle::class)->findAll();
        return $this->render('@TerminalbdCrm/layerLifeCycle/index.html.twig',['entities' => $entities]);
    }

    /**
     * @param Request $request
     * @param CrmCustomer $crmCustomer
     * @param Setting $report
     * @return Response
     * @throws \Exception
     * @Route("/customer/{id}/report/{report}/new/modal", methods={"GET", "POST"}, name="layer_new_modal")
     */
    public function newModal(Request $request, CrmCustomer $crmCustomer, Setting $report): Response
    {

        $data = $request->request->get('layer_life_cycle_form');
        $entity = new LayerLifeCycle();
        $existReport = $this->getDoctrine()->getRepository(LayerLifeCycle::class)->findOneBy(array('employee'=>$this->getUser(),'customer'=>$crmCustomer, 'report'=>$report, 'lifeCycleState'=>LayerLifeCycle::LIFE_CYCLE_STATE_IN_PROGRESS));
        if ($existReport){
            $entity= $existReport;
        }

        if($existReport){
            return $this->redirectToRoute('layer_life_cycle_details_modal', ['id'=>$existReport->getId()]);
        }

        $form = $this->createForm(LayerLifeCycleFormType::class, $entity, array('report' => $report))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hatingDate = isset($data['hatchery_date'])?date('Y-m-d',strtotime($data['hatchery_date'])):date('Y-m-d',strtotime('now'));

            $entity->setHatcheryDate(new \DateTime($hatingDate));
            $entity->setCustomer($crmCustomer);
            $entity->setReport($report);
            $entity->setAgent($crmCustomer->getAgent());
            $entity->setLifeCycleState(LayerLifeCycle::LIFE_CYCLE_STATE_IN_PROGRESS);
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return new Response('success');
            }
            return new Response('success');
        }

        return $this->render('@TerminalbdCrm/layerLifeCycle/new-modal.html.twig', [
            'report' => $report,
            'crmCustomer' => $crmCustomer,
            'entity' => $entity,
            'form' => $form->createView(),
            'data' => $data,
        ]);
    }


    /**
     * @param LayerLifeCycle $layerLifeCycle
     * @Route("/report/{id}/modal", methods={"GET", "POST"}, name="layer_life_cycle_details_modal")
     * @return Response
     */
    public function lifeCycleDetailsModal(LayerLifeCycle $layerLifeCycle): Response
    {
        $lifeCycleSetting = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->findOneBy(array('report'=>$layerLifeCycle->getReport()));
        $crmLayerLifeCycleDetails = $this->getDoctrine()->getRepository(LayerLifeCycleDetails::class)->findOneBy(array('crmLayerLifeCycle'=>$layerLifeCycle->getId()));
        if (!$crmLayerLifeCycleDetails){
            for($i=1; $i<=$lifeCycleSetting->getNumberOfWeek(); $i++){
                $layerLifeCycleDetails = new LayerLifeCycleDetails();
                $layerLifeCycleDetails->setAgeWeek($i);
                $layerLifeCycleDetails->setCrmLayerLifeCycle($layerLifeCycle);
                $layerLifeCycleDetails->setCreated(new \DateTime('now'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($layerLifeCycleDetails);

                $em->flush();
            }
        }

        $feedTypes = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FEED_TYPE','parent'=>$layerLifeCycle->getReport()->getParent()),['name' => 'ASC']);
        $feedMills = $this->getDoctrine()->getRepository(Setting::class)->findBy(array('settingType'=>'FEED_MILL'),['name' => 'ASC']);

//        $layerStandard = $this->getDoctrine()->getRepository(LayerStandard::class)->getLayerStandardByWeek($layerLifeCycle->getBreed());

        return $this->render('@TerminalbdCrm/layerLifeCycle/layer-details-modal.html.twig', [
            'layerLifeCycle' => $layerLifeCycle,
            'feedTypes' => $feedTypes,
            'feedMills' => $feedMills,
        ]);
    }

    /**
     * @Route("/details/{id}/edit", methods={"POST"}, name="crm_layer_life_cycle_details_edit", options={"expose"=true})
     * @param Request $request
     * @param LayerLifeCycleDetails $entity
     * @return Response
     * @throws \Exception
     */

    public function editLifeCycleDetails(Request $request, LayerLifeCycleDetails $entity): Response
    {
        $data = $request->request->all();
        $metaKey = $data['dataMetaKey'];
        $metaValue = $data['dataMetaValue'];
        $inputType = $data['dataInputType'];

        $layerStandard = $this->getDoctrine()->getRepository(LayerStandard::class)->findOneBy(array('report'=>$entity->getCrmLayerLifeCycle()->getReport(), 'age'=>$entity->getAgeWeek()));

        if($metaKey!=''&&$metaValue!=''){

            if($inputType=='datetime'){
                $metaValue= isset($metaValue)&&$metaValue!=""?date('Y-m-d',strtotime($metaValue)):date('Y-m-d',strtotime('now'));
                $metaValue = new \DateTime($metaValue);
            }

            if($inputType=='number'){
                $metaValue = $metaValue>0?$metaValue:0;
            }

            if($inputType=='select'){
                $metaValue = $this->getDoctrine()->getRepository(Setting::class)->find($metaValue);
            }

            $set = 'set'.$metaKey;

            $entity->$set($metaValue);

            $entity->setTargetWeight($layerStandard?$layerStandard->getTargetBodyWeight():0);
            $entity->setTargetFeedPerBird($layerStandard?$layerStandard->getTargetFeedConsumption():0);
            $entity->setTargetEggProduction($layerStandard?$layerStandard->getTargetEggProduction():0);
            $entity->setEggWeightStandard($layerStandard?$layerStandard->getTargetEggWeight():0);
            $entity->setIsVisited(1);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        $lifeCycleDetails = $entity->getCrmLayerLifeCycle()->getCrmLayerLifeCycleDetails();

        $returnArray = array();
        /* @var LayerLifeCycleDetails $lifeCycleDetail */
        foreach ($lifeCycleDetails as $lifeCycleDetail){
            $returnArray[]= $lifeCycleDetail->calculatePresentBird();
        }
        $returnEggProductionArray = array();
        /* @var LayerLifeCycleDetails $lifeCycleDetail */
        foreach ($lifeCycleDetails as $lifeCycleDetail){
            $returnEggProductionArray[]= $lifeCycleDetail->calculateEggProduction();
        }


        return new JsonResponse(
            array(
                'success'=>'Success',
                'presentBird'=>$returnArray,
                'eggProduction'=>$returnEggProductionArray,
                'targetWeight'=>$entity->getTargetWeight(),
                'targetFeedPerBird'=>$entity->getTargetFeedPerBird(),
                'targetEggProduction'=>$entity->getTargetEggProduction(),
                'eggWeightStandard'=>$entity->getEggWeightStandard(),
                'data'=>$data,
                'status'=>200,
            )
        );

    }

    /**
     * Deletes a LayerPerformance entity.
     * @Route("/{id}/delete", methods={"GET"}, name="layer_life_cycle_delete")
     * @param $id
     * @return Response
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
     * @param LayerLifeCycle $layerLifeCycle
     * @Route("/{id}/complete", methods={"POST"}, name="crm_layer_life_cycle_complete", options={"expose"=true})
     * @return Response
     */
    public function layerLifeCycleReportComplete(LayerLifeCycle $layerLifeCycle): Response
    {
        $layerLifeCycle->setLifeCycleState(LayerLifeCycle::LIFE_CYCLE_STATE_COMPLETE);
        $em = $this->getDoctrine()->getManager();
        $em->persist($layerLifeCycle);
        $em->flush();
        return new JsonResponse(array(
            'message'=>"Success",
            'status'=>200
        ));
    }



}
