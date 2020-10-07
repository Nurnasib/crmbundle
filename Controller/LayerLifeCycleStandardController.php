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
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\LayerLifecycleStandard;
use Terminalbd\CrmBundle\Form\BroilerStandardFormType;
use Terminalbd\CrmBundle\Form\LayerLifeCycleStandardFormType;

/**
 * @Route("/crm/layer/standard")
 */
class LayerLifeCycleStandardController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="layer_life_cycle_standard")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function index(Request $request): Response
    {
        $entitys = $this->getDoctrine()->getRepository(LayerLifecycleStandard::class)->findBy(
            ['birdType'=>'ShaverStarCross579']

        );
        return $this->render('@TerminalbdCrm/LayerLifeCycleStandard/index.html.twig',['entities' => $entitys]);
    }


    /**
     * @Route("/bovanas/white", methods={"GET"}, name="layer_life_cycle_standard_bovanas")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function index_bovans(Request $request): Response
    {
        $entitys = $this->getDoctrine()->getRepository(LayerLifecycleStandard::class)->findBy(
            ['birdType'=>'BovansWhite']);

        return $this->render('@TerminalbdCrm/LayerLifeCycleStandard/index_bovans.html.twig',['entities' => $entitys]);
    }



    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     * @Route("/new", methods={"GET", "POST"}, name="layer_life_cycle_standard_new")
     */
    public function new(Request $request): Response
    {

        $entity = new LayerLifecycleStandard();
        $data = $request->request->all();
        $form = $this->createForm(LayerLifeCycleStandardFormType::class , $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('layer_life_cycle_standard_new');
            }
            return $this->redirectToRoute('layer_life_cycle_standard_new');
        }
        return $this->render('@TerminalbdCrm/LayerLifeCycleStandard/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing LayerLifecycleStandard entity.
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="layer_life_cycle_standard_edit")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */

    public function edit(Request $request, LayerLifecycleStandard $entity): Response
    {
        $data = $request->request->all();
        $form = $this->createForm(LayerLifeCycleStandardFormType::class, $entity)
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('layer_life_cycle_standard', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('layer_life_cycle_standard');
        }
        return $this->render('@TerminalbdCrm/LayerLifeCycleStandard/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a BroilerStandard entity.
     * @Route("/{id}/delete", methods={"GET"}, name="layer_life_cycle_standard_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(BroilerStandard::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }

    



}
