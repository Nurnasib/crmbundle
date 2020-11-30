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
 * @Route("/crm/setting")
 */
class SettingController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="crm_setting")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function index(Request $request): Response
    {
        $entities = $this->getDoctrine()->getRepository(Setting::class)->findBy(array(),array('settingType'=>'asc'));
        return $this->render('@TerminalbdCrm/setting/index.html.twig',['entities' => $entities]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     * @Route("/new", methods={"GET", "POST"}, name="crm_setting_new")
     */
    public function new(Request $request): Response
    {

        $entity = new Setting();
        $data = $request->request->all();

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
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */

    public function edit(Request $request, Setting $entity): Response
    {
        $data = $request->request->all();
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
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(Setting::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }


    /**
     * @Route("/life-cycle", methods={"GET"}, name="crm_setting_life_cycle")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function indexLifeCycle(Request $request): Response
    {
        $entities = $this->getDoctrine()->getRepository(SettingLifeCycle::class)->findAll();
        return $this->render('@TerminalbdCrm/setting/life-cycle/index.html.twig',['entities' => $entities]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     * @Route("/life-cycle/new", methods={"GET", "POST"}, name="crm_setting_life_cycle_new")
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
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */

    public function editLifeCycle(Request $request, SettingLifeCycle $entity): Response
    {
        $data = $request->request->all();
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



}
