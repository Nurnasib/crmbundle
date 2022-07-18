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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Terminalbd\CrmBundle\Entity\LayerStandard;
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\BroilerStandardFormType;
use Terminalbd\CrmBundle\Form\LayerStandardFormType;

/**
 * Class LayerStandardController
 * @package Terminalbd\CrmBundle\Controller
 * @Route("/crm/layer/standard")
 * @Security("is_granted('ROLE_CRM_POULTRY_USER') or is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_DEVELOPER')")
 */
class LayerStandardController extends AbstractController
{
    /**
     * @Route("/", methods={"GET","POST"}, name="layer_standard")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {

        $entity = new LayerStandard();
        $noOfWeek = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->getLifeCycleWeekByLifeCycle('layer');
        $reports = $this->getDoctrine()->getRepository(Setting::class)->getReportByParentSlug('layer');

        $form = $this->createForm(LayerStandardFormType::class , $entity)
            ->add('age', ChoiceType::class, [
                'choices'  => array_flip($noOfWeek)])
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('layer_standard');
            }
            return $this->redirectToRoute('layer_standard');
        }
        $layerStandard = array();
        $entities = $this->getDoctrine()->getRepository(LayerStandard::class)->findBy(array(), array('age'=>'ASC'));
        foreach ($entities as $value){
            $layerStandard[$value->getReport()->getId()][]=$value;
        }
        return $this->render('@TerminalbdCrm/layerStandard/index.html.twig',[
            'entities' => $layerStandard,
            'form' => $form->createView(),
            'breeds' => $reports,
        ]);
    }

    /**
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="layer_standard_edit")
     * @param Request $request
     * @param LayerStandard $entity
     * @return Response
     */

    public function edit(Request $request, LayerStandard $entity): Response
    {
        $noOfWeek = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->getLifeCycleWeekByLifeCycle('layer');
        $reports = $this->getDoctrine()->getRepository(Setting::class)->getReportByParentSlug('layer');

        $form = $this->createForm(LayerStandardFormType::class , $entity)
            ->add('age', ChoiceType::class, [
                'choices'  => array_flip($noOfWeek)])
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('layer_standard');
            }
            return $this->redirectToRoute('layer_standard');
        }
        $layerStandard = array();
        $entities = $this->getDoctrine()->getRepository(LayerStandard::class)->findAll();
        foreach ($entities as $value){
            $layerStandard[$value->getReport()->getId()][]=$value;
        }
        return $this->render('@TerminalbdCrm/layerStandard/index.html.twig', [
            'entity' => $entity,
            'entities' => $layerStandard,
            'form' => $form->createView(),
            'breeds' => $reports,
        ]);
    }

    /**
     * @Route("/{id}/delete", methods={"GET"}, name="layer_standard_delete")
     * @param $id
     * @return Response
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(LayerStandard::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


    /**
     * @Route("/{id}/inline-age-update", name="inline_update_layer_age")
     */
    public function inlineAgeUpdate(LayerStandard $standard, Request $request)
    {
        $age = $request->request->get('value');

        $standard->setAge($age);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'status' => 200,
            'message' => 'success'
        ]);
    }

    /**
     * @Route("/{id}/inline-update-target-body-weight", name="inline_update_layer_target_body_weight")
     */
    public function inlineTargetBodyWeightUpdate(LayerStandard $standard, Request $request)
    {
        $weight = $request->request->get('value');

        $standard->setTargetBodyWeight($weight);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'status' => 200,
            'message' => 'success'
        ]);
    }

    /**
     * @Route("/{id}/inline-update-target-feed-consumption", name="inline_update_layer_target_feed_consumption")
     */
    public function inlineTargetFeedConsumptionUpdate(LayerStandard $standard, Request $request)
    {
        $consumption = $request->request->get('value');

        $standard->setTargetFeedConsumption($consumption);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'status' => 200,
            'message' => 'success'
        ]);
    }

    /**
     * @Route("/{id}/inline-update-target-egg-production", name="inline_update_layer_target_egg_production")
     */
    public function inlineTargetEggProductionUpdate(LayerStandard $standard, Request $request)
    {
        $value = $request->request->get('value');

        $standard->setTargetEggProduction($value);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'status' => 200,
            'message' => 'success'
        ]);
    }

    /**
     * @Route("/{id}/inline-update-target-egg-weight", name="inline_update_layer_target_egg_weight")
     */
    public function inlineTargetEggWeightUpdate(LayerStandard $standard, Request $request)
    {
        $value = $request->request->get('value');

        $standard->setTargetEggWeight($value);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'status' => 200,
            'message' => 'success'
        ]);
    }
    



}
