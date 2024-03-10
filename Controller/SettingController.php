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
use Terminalbd\CrmBundle\Entity\Setting;
use Terminalbd\CrmBundle\Entity\SettingLifeCycle;
use Terminalbd\CrmBundle\Form\SettingFormType;
use Terminalbd\CrmBundle\Form\SettingLifeCycleFormType;

/**
 * Class SettingController
 * @package Terminalbd\CrmBundle\Controller
 * @Route("/crm/setting")
 * @Security("is_granted('ROLE_CRM_POULTRY_ADMIN') or is_granted('ROLE_CRM_CATTLE_ADMIN') or is_granted('ROLE_CRM_AQUA_ADMIN') or is_granted('ROLE_DEVELOPER')")
 */
class SettingController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="crm_setting")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array(),array('settingType'=>'asc'));
        return $this->render('@TerminalbdCrm/setting/index.html.twig',['entities' => $entities]);
    }

    /**
     * @Route("/new", methods={"GET", "POST"}, name="crm_setting_new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {

        $entity = new Setting();

        $form = $this->createForm(SettingFormType::class , $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_setting_new');
            }
            return $this->redirectToRoute('crm_setting');
        }
        return $this->render('@TerminalbdCrm/setting/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_setting_edit")
     * @param Request $request
     * @param Setting $entity
     * @return Response
     */

    public function edit(Request $request, Setting $entity): Response
    {
        $form = $this->createForm(SettingFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            //$this->getDoctrine()->getRepository(ItemKeyValue::class)->insertSettingKeyValue($entity,$data);
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_setting', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('crm_setting');
        }
        return $this->render('@TerminalbdCrm/setting/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Setting entity.
     * @Route("/{id}/delete", methods={"GET"}, name="crm_setting_delete")
     * @param $id
     * @return Response
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(Setting::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $entity->setStatus(false);
        $em->persist($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/sort-order-update/{id}", name="crm_setting_sort_order_update", options={"expose"=true})
     */
    public function inlineUpdateReportMode(Request $request, Setting $setting)
    {

        $data = $request->request->all();
        
//        $reportMode = $this->getDoctrine()->getRepository(Setting::class)->find($data['value']);

        $setting->setSortOrder($data['value']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($setting);
        $em->flush();
        return new JsonResponse(['status' => 200]);

    }


    /**
     * @Route("/life-cycle", methods={"GET"}, name="crm_setting_life_cycle")
     * @return Response
     */
    public function indexLifeCycle(): Response
    {
        $entities = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->findAll();
        return $this->render('@TerminalbdCrm/setting/life-cycle/index.html.twig',['entities' => $entities]);
    }

    /**
     * @Route("/life-cycle/new", methods={"GET", "POST"}, name="crm_setting_life_cycle_new")
     * @param Request $request
     * @return Response
     */
    public function newLifeCycle(Request $request): Response
    {

        $entity = new SettingLifeCycle();
        $form = $this->createForm(SettingLifeCycleFormType::class , $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_setting_life_cycle_new');
            }
            return $this->redirectToRoute('crm_setting_life_cycle');
        }
        return $this->render('@TerminalbdCrm/setting/life-cycle/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Post entity.
     *
     * @Route("/life-cycle/{id}/edit", methods={"GET", "POST"}, name="crm_setting_life_cycle_edit")
     * @param Request $request
     * @param SettingLifeCycle $entity
     * @return Response
     */

    public function editLifeCycle(Request $request, SettingLifeCycle $entity): Response
    {
        $form = $this->createForm(SettingLifeCycleFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            //$this->getDoctrine()->getRepository(ItemKeyValue::class)->insertSettingKeyValue($entity,$data);
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_setting_life_cycle_edit', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('crm_setting_life_cycle');
        }
        return $this->render('@TerminalbdCrm/setting/life-cycle/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/working-mode-select", name="working_mode_select", options={"expose"=true})
     * @return JsonResponse
     */

    public function getWorkingModeUsingAjax()
    {
        $workingModes = $this->getDoctrine()->getRepository(Setting::class)->findBy(['status' => 1, 'settingType' => 'WORKING_MODE']);
        $returnArray=[];
        if ($workingModes){
            foreach ($workingModes as $workingMode){
                $returnArray[$workingMode->getId()]=$workingMode->getName();
            }
        }

        return new JsonResponse($returnArray);
    }



}
