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
use Terminalbd\CrmBundle\Entity\BroilerStandard;
use Terminalbd\CrmBundle\Entity\ChickLifeCycle;
use Terminalbd\CrmBundle\Form\ChickLifeCycleFormType;
use Terminalbd\CrmBundle\Entity\Setting;


/**
 * @Route("/crm/chick")
 */
class ChickLifeCycleController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="crm_chick")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function index(Request $request): Response
    {
        //  broiler index page
        $entitys = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->findAll();
        return $this->render('@TerminalbdCrm/chickLifecycle/index.html.twig',['entities' => $entitys]);
    }

    /**
     * @Route("/sonali", methods={"GET"}, name="crm_sonali")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function sonali_index(Request $request): Response
    {
        $entitys = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->findBy(
            ['birdMode'=>'SONALI']
        );
        return $this->render('@TerminalbdCrm/chickLifecycle/sonali_index.html.twig',['entities' => $entitys]);
    }

    /**
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     * @Route("/new", methods={"GET", "POST"}, name="chick_new")
     */
    public function new(Request $request): Response
    {
        $entity = new ChickLifeCycle();
        $data = $request->request->all();
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(ChickLifeCycleFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entity->setEmployee($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $this->addFlash('success', 'post.created_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('chick_new');
            }
            return $this->redirectToRoute('chick_new');
        }
        return $this->render('@TerminalbdCrm/chickLifecycle/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing ChickLifeCycle entity.
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="crm_chick_edit")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */

    public function edit(Request $request, ChickLifeCycle $entity): Response
    {
        $data = $request->request->all();
        $agentRepo = $this->getDoctrine()->getRepository(Agent::class);
        $form = $this->createForm(ChickLifeCycleFormType::class, $entity,array('user' => $this->getUser(),'agentRepo' => $agentRepo))
            ->add('SaveAndCreate', SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'post.updated_successfully');
            if ($form->get('SaveAndCreate')->isClicked()) {
                return $this->redirectToRoute('crm_chick', ['id' => $entity->getId()]);
            }
            return $this->redirectToRoute('chick_new');
        }
        return $this->render('@TerminalbdCrm/chickLifecycle/new.html.twig', [
            'entity' => $entity,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a ChickLifeCycle entity.
     * @Route("/{id}/delete", methods={"GET"}, name="crm_chick_delete")
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DOMAIN') or is_granted('ROLE_CRM')")
     */
    public function delete($id): Response
    {
        $entity = $this->getDoctrine()->getRepository(ChickLifeCycle::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
        $this->addFlash('success', 'post.deleted_successfully');
        return new Response('Success');
    }

    



}
